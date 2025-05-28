<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        // No need to define middleware here, it's set in the routes file
    }

    /**
     * Display a listing of the tickets for admin management.
     */
    public function index(Request $request)
    {
        $statuses = Ticket::STATUSES;
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        $tickets = Ticket::with(['user', 'agent', 'category']);
        
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
        
        return view('admin.tickets.index', compact('tickets', 'statuses', 'priorities'));
    }

    /**
     * Mark a ticket as rejected.
     */
    public function reject(Request $request, Ticket $ticket)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Store the old status for notifications
        $oldStatus = $ticket->status;
        
        // Update ticket status to rejected
        $ticket->status = 'rejected';
        $ticket->save();

        // Send notification to client
        if ($ticket->user) {
            $rejectionNotification = new TicketRejected($ticket, $request->rejection_reason);
            $ticket->user->storeNotification($rejectionNotification);
            Notification::send([$ticket->user], $rejectionNotification);
        }

        // Send notification to assigned agent if exists
        if ($ticket->agent) {
            $rejectionNotification = new TicketRejected($ticket, $request->rejection_reason);
            $ticket->agent->storeNotification($rejectionNotification);
            Notification::send([$ticket->agent], $rejectionNotification);
        }

        Log::info('Ticket rejected', [
            'ticket_id' => $ticket->id,
            'reason' => $request->rejection_reason
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket #' . $ticket->ticket_number . ' has been rejected.');
    }

    /**
     * Delete a ticket from the system.
     */
    public function destroy(Ticket $ticket)
    {
        // Store user and agent info before deletion for notifications
        $user = $ticket->user;
        $agent = $ticket->agent;
        $ticketNumber = $ticket->ticket_number;
        
        // First mark as rejected to trigger notifications
        if ($ticket->status !== 'rejected') {
            // Send rejection notification before deleting
            $rejectionNotification = new TicketRejected($ticket, 'Ticket has been rejected and will be removed from the system.');
            
            if ($user) {
                $user->storeNotification($rejectionNotification);
                Notification::send([$user], $rejectionNotification);
            }
            
            if ($agent) {
                $agent->storeNotification($rejectionNotification);
                Notification::send([$agent], $rejectionNotification);
            }
        }
        
        // Now delete the ticket
        $ticket->delete();
        
        Log::info('Ticket deleted', [
            'ticket_number' => $ticketNumber
        ]);
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket #' . $ticketNumber . ' has been deleted.');
    }
} 