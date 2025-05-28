<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Ticket;
use App\Notifications\TicketFeedbackReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a form to submit feedback for a ticket.
     */
    public function create(Ticket $ticket)
    {
        $user = Auth::user();
        
        // Check if user is authorized to give feedback
        if ($ticket->user_id !== $user->id) {
            abort(403, 'You are not authorized to give feedback for this ticket.');
        }
        
        // Check if ticket is closed
        if ($ticket->status !== 'closed') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'You can only give feedback for closed tickets.');
        }
        
        // Check if feedback already exists
        $existingFeedback = Feedback::where('ticket_id', $ticket->id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existingFeedback) {
            return redirect()->route('feedback.edit', [$ticket, $existingFeedback])
                ->with('info', 'You have already provided feedback for this ticket. You can edit it if you wish.');
        }
        
        return view('feedback.create', compact('ticket'));
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Check if user is authorized to give feedback
        if ($ticket->user_id !== $user->id) {
            abort(403, 'You are not authorized to give feedback for this ticket.');
        }
        
        // Check if ticket is closed
        if ($ticket->status !== 'closed') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'You can only give feedback for closed tickets.');
        }
        
        // Validate request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        // Create feedback
        $feedback = Feedback::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'agent_id' => $ticket->agent_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        // Check notification settings
        $settings = Cache::get('system_settings', []);
        $shouldNotifyOnFeedback = $settings['notify_on_feedback'] ?? true;
        
        // Notify agent about feedback if there's an agent assigned and notifications are enabled
        if ($shouldNotifyOnFeedback && $ticket->agent) {
            $feedbackNotification = new TicketFeedbackReceived($ticket, $feedback);
            $ticket->agent->storeNotification($feedbackNotification);
            Notification::send([$ticket->agent], $feedbackNotification);
            
            Log::info('Feedback notification sent to agent', [
                'ticket_id' => $ticket->id,
                'agent_id' => $ticket->agent_id,
                'feedback_id' => $feedback->id
            ]);
        }
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Thank you for your feedback!');
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit(Ticket $ticket, Feedback $feedback)
    {
        $user = Auth::user();
        
        // Check if user is authorized to edit feedback
        if ($feedback->user_id !== $user->id || $feedback->ticket_id !== $ticket->id) {
            abort(403, 'You are not authorized to edit this feedback.');
        }
        
        return view('feedback.edit', compact('ticket', 'feedback'));
    }

    /**
     * Update the specified feedback in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Check if user is authorized to update this feedback
        if ($ticket->user_id !== $user->id) {
            abort(403, 'You are not authorized to update feedback for this ticket.');
        }
        
        // Check if feedback exists
        $feedback = Feedback::where('ticket_id', $ticket->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$feedback) {
            abort(404, 'Feedback not found.');
        }
        
        // Validate request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        // Store old rating for comparison
        $oldRating = $feedback->rating;
        
        // Update feedback
        $feedback->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        // Check notification settings
        $settings = Cache::get('system_settings', []);
        $shouldNotifyOnFeedback = $settings['notify_on_feedback'] ?? true;
        
        // Notify agent if rating was changed and notifications are enabled
        if ($shouldNotifyOnFeedback && $oldRating != $request->rating && $ticket->agent) {
            $feedbackNotification = new TicketFeedbackReceived($ticket, $feedback);
            $ticket->agent->storeNotification($feedbackNotification);
            Notification::send([$ticket->agent], $feedbackNotification);
            
            Log::info('Updated feedback notification sent to agent', [
                'ticket_id' => $ticket->id,
                'agent_id' => $ticket->agent_id,
                'feedback_id' => $feedback->id
            ]);
        }
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Feedback updated successfully!');
    }

    /**
     * Display feedback statistics for the admin dashboard.
     */
    public function statistics()
    {
        // Only admin and agents can view statistics
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'agent') {
            abort(403, 'Unauthorized action.');
        }
        
        $overallRating = Feedback::avg('rating');
        $totalFeedback = Feedback::count();
        
        $ratingBreakdown = Feedback::selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();
        
        $agentRatings = Feedback::selectRaw('agent_id, AVG(rating) as average_rating, COUNT(*) as count')
            ->whereNotNull('agent_id')
            ->groupBy('agent_id')
            ->with('agent:id,name')
            ->orderByDesc('average_rating')
            ->get();
        
        return view('feedback.statistics', compact(
            'overallRating',
            'totalFeedback',
            'ratingBreakdown',
            'agentRatings'
        ));
    }
} 