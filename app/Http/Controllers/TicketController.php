<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Sla;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketSla;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketStatusUpdated;
use App\Notifications\NewTicketComment;
use App\Notifications\NewTicketCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\TicketAssignedToYou;
use App\Notifications\AgentAssignedToTicket;

class TicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tickets = null;
        $statuses = Ticket::STATUSES;
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        // Different views based on role
        if ($user->role === 'admin') {
            $tickets = Ticket::with(['user', 'agent', 'category']);
        } elseif ($user->role === 'agent') {
            $tickets = Ticket::with(['user', 'category']);
            
            // Apply assignment filter if provided
            if ($request->has('assignment')) {
                if ($request->assignment === 'assigned') {
                    $tickets->where('agent_id', $user->id);
                } elseif ($request->assignment === 'unassigned') {
                    $tickets->whereNull('agent_id');
                }
            } else {
                // Default behavior (show assigned to me and unassigned)
                $tickets->where(function ($query) use ($user) {
                    $query->where('agent_id', $user->id)
                          ->orWhereNull('agent_id');
                });
            }
        } else {
            $tickets = Ticket::with(['agent', 'category'])
                ->where('user_id', $user->id);
        }
        
        // Apply filters if present
        if ($request->has('status') && in_array($request->status, $statuses)) {
            $tickets->where('status', $request->status);
        }
        
        if ($request->has('priority') && in_array($request->priority, $priorities)) {
            $tickets->where('priority', $request->priority);
        }
        
        if ($request->filled('category_id')) {
            $tickets->where('category_id', $request->category_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $tickets->where(function ($query) use ($search) {
                $query->where('subject', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }
        
        $tickets = $tickets->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();
        
        return view('tickets.index', compact('tickets', 'categories', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $categories = Category::all();
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        return view('tickets.create', compact('categories', 'priorities'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max file size
        ]);

        $user = Auth::user();
        
        $ticket = Ticket::create([
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
            'priority' => $request->priority,
            'category_id' => $request->category_id,
            'user_id' => $user->id,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('ticket_attachments/' . $ticket->id, $filename, 'public');
                
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $filepath,
                    'filetype' => $file->getClientMimeType(),
                    'filesize' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }
        }

        // Create SLA for the ticket
        $sla = Sla::where('priority', $ticket->priority)->first();
        if ($sla) {
            TicketSla::create([
                'ticket_id' => $ticket->id,
                'sla_id' => $sla->id,
                'response_due_at' => now()->addHours($sla->response_time_hours),
                'resolution_due_at' => now()->addHours($sla->resolution_time_hours),
            ]);
        }
        
        // Check notification settings
        $settings = Cache::get('system_settings', []);
        $shouldNotifyOnTicketCreation = $settings['notify_on_ticket_creation'] ?? true;
        
        // Notify all agents about the new ticket if enabled
        if ($shouldNotifyOnTicketCreation) {
            // Create notification instance
            $newTicketNotification = new NewTicketCreated($ticket);
            
            // Get all agents
            $agents = User::where('role', 'agent')->where('status', 'active')->get();
            
            if ($agents->count() > 0) {
                foreach ($agents as $agent) {
                    $agent->storeNotification($newTicketNotification);
                }
                
                // Send notification to all agents at once
                Notification::send($agents, $newTicketNotification);
                
                Log::info('New ticket notification sent to agents', [
                    'ticket_id' => $ticket->id,
                    'agent_count' => $agents->count()
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully! Your ticket number is ' . $ticket->ticket_number);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Request $request, $ticketId)
    {
        $user = Auth::user();
        
        // First try to find the ticket normally
        $ticket = Ticket::find($ticketId);
        
        // If not found, check if it's been soft-deleted
        if (!$ticket) {
            // Look for the ticket including trashed (deleted) ones
            $ticket = Ticket::withTrashed()->find($ticketId);
            
            // If the ticket exists but is trashed, show a message
            if ($ticket && $ticket->trashed()) {
                return view('tickets.deleted', [
                    'ticket' => $ticket
                ]);
            }
            
            // If ticket doesn't exist at all
            abort(404, 'Ticket not found.');
        }
        
        // Check if user is allowed to view this ticket
        if ($user->role !== 'admin' && $user->role !== 'agent' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->load(['user', 'agent', 'category', 'comments.user', 'attachments']);
        
        // Get comments with appropriate visibility
        $commentsQuery = $ticket->comments();
        
        // If not admin or agent, filter out private comments
        if ($user->role !== 'admin' && $user->role !== 'agent') {
            $commentsQuery->where('is_private', false);
        }
        
        $comments = $commentsQuery->orderBy('created_at')->get();
        
        // Get agents for reassignment dropdown
        $agents = User::where('role', 'agent')->get();
        
        return view('tickets.show', compact('ticket', 'comments', 'agents'));
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role !== 'admin' && $user->role !== 'agent' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate request
        $request->validate([
            'status' => 'sometimes|required|in:' . implode(',', Ticket::STATUSES),
            'priority' => 'sometimes|required|in:low,medium,high,urgent',
            'agent_id' => 'sometimes|nullable|exists:users,id',
        ]);

        // Store old values for notifications
        $oldStatus = $ticket->status;
        $oldAgentId = $ticket->agent_id;

        // Update ticket
        $ticket->update($request->only(['status', 'priority', 'agent_id']));

        // Send notifications
        // If agent was assigned or changed
        if ($request->has('agent_id') && $oldAgentId !== $request->agent_id) {
            if ($request->agent_id) {
                $agent = User::find($request->agent_id);
                
                // Notification for agent
                $ticketAssignedToYouNotification = new TicketAssignedToYou($ticket);
                $agent->storeNotification($ticketAssignedToYouNotification);
                Notification::send([$agent], $ticketAssignedToYouNotification);
                
                // Notification for client that an agent was assigned to their ticket
                $agentAssignedToTicketNotification = new AgentAssignedToTicket($ticket, $agent);
                $ticket->user->storeNotification($agentAssignedToTicketNotification);
                Notification::send([$ticket->user], $agentAssignedToTicketNotification);
                
                Log::info('Agent assigned notifications sent', [
                    'ticket_id' => $ticket->id,
                    'agent_id' => $agent->id
                ]);
            }
        }

        // If status was changed
        if ($request->has('status') && $oldStatus !== $request->status) {
            // Create notification instance
            $statusNotification = new TicketStatusUpdated($ticket, $oldStatus);
            
            // Notify the ticket owner about status change
            $ticket->user->storeNotification($statusNotification);
            Notification::send([$ticket->user], $statusNotification);
            
            // Also notify the agent if one is assigned
            if ($ticket->agent) {
                $ticket->agent->storeNotification($statusNotification);
                Notification::send([$ticket->agent], $statusNotification);
            }
            
            Log::info('Status update notification sent', [
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $ticket->status
            ]);
        }

        // If status was changed to closed
        if ($request->has('status') && $request->status === 'closed' && $oldStatus !== 'closed') {
            // Notify user about feedback opportunity
            // Handled via Events/Listeners
        }

        // If priority was changed, update SLA
        if ($request->has('priority') && $request->priority !== $ticket->getOriginal('priority')) {
            $sla = Sla::where('priority', $request->priority)->first();
            
            if ($sla) {
                // Get or create ticket SLA record
                $ticketSla = TicketSla::firstOrNew(['ticket_id' => $ticket->id]);
                
                $ticketSla->sla_id = $sla->id;
                $ticketSla->response_due_at = now()->addHours($sla->response_time_hours);
                $ticketSla->resolution_due_at = now()->addHours($sla->resolution_time_hours);
                $ticketSla->save();
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    /**
     * Add a comment to a ticket.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role !== 'admin' && $user->role !== 'agent' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'message' => 'required|string',
            'is_private' => 'sometimes|boolean',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max file size
        ]);

        // Check if private comment is allowed
        if ($request->is_private && $user->role !== 'admin' && $user->role !== 'agent') {
            abort(403, 'Unauthorized action: Private comments are only for staff.');
        }

        // Create comment
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'is_private' => $request->is_private ?? false,
            'parent_id' => $request->parent_id, // Support for threaded comments
        ]);

        // Create notification instance
        $commentNotification = new NewTicketComment($comment);
        
        // Send notifications about the new comment
        // Notify the ticket owner if the comment is from an agent or admin
        if (($user->role === 'admin' || $user->role === 'agent') && !$request->is_private) {
            $ticket->user->storeNotification($commentNotification);
            Notification::send([$ticket->user], $commentNotification);
            
            Log::info('Comment notification sent to ticket owner', [
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id
            ]);
        }
        
        // Notify the assigned agent if the comment is from the ticket owner
        if ($user->id === $ticket->user_id && $ticket->agent) {
            $ticket->agent->storeNotification($commentNotification);
            Notification::send([$ticket->agent], $commentNotification);
            
            Log::info('Comment notification sent to agent', [
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id,
                'agent_id' => $ticket->agent->id
            ]);
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('ticket_attachments/' . $ticket->id, $filename, 'public');
                
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $filepath,
                    'filetype' => $file->getClientMimeType(),
                    'filesize' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }
        }

        // If agent is responding to client
        if ($user->role === 'admin' || $user->role === 'agent') {
            if ($ticket->status === 'open') {
                $ticket->update(['status' => 'in_progress']);
            }

            // Record first response for SLA tracking
            $ticketSla = TicketSla::where('ticket_id', $ticket->id)->first();
            if ($ticketSla && !$ticketSla->first_response_at) {
                $ticketSla->first_response_at = now();
                
                // Check if response is breached
                if ($ticketSla->first_response_at->gt($ticketSla->response_due_at)) {
                    $ticketSla->response_breached = true;
                }
                
                $ticketSla->save();
            }
        }

        // If client is responding to agent
        if ($user->role === 'client' && $ticket->status === 'waiting_customer') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Download attachment from a ticket.
     */
    public function downloadAttachment(Ticket $ticket, TicketAttachment $attachment)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role !== 'admin' && $user->role !== 'agent' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if attachment belongs to ticket
        if ($attachment->ticket_id !== $ticket->id) {
            abort(404, 'Attachment not found.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->filepath)) {
            abort(404, 'File not found.');
        }

        return response()->download(
            storage_path('app/public/' . $attachment->filepath),
            $attachment->filename
        );
    }
} 