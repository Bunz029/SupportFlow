<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketFeedbackReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $feedback;

    /**
     * Create a new notification instance.
     *
     * @param Ticket $ticket
     * @param Feedback $feedback
     * @return void
     */
    public function __construct(Ticket $ticket, Feedback $feedback)
    {
        $this->ticket = $ticket;
        $this->feedback = $feedback;
        Log::info('TicketFeedbackReceived notification created', ['ticket_id' => $ticket->id, 'feedback_id' => $feedback->id]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info('TicketFeedbackReceived notification channels', ['user_id' => $notifiable->id]);
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('tickets.show', $this->ticket);
        Log::info('TicketFeedbackReceived email being sent', ['user_id' => $notifiable->id, 'ticket_id' => $this->ticket->id]);
        
        return (new MailMessage)
            ->subject('Feedback Received on Ticket: #' . $this->ticket->ticket_number)
            ->line('A client has provided feedback on ticket #' . $this->ticket->ticket_number)
            ->line('Rating: ' . $this->formatRating($this->feedback->rating))
            ->when($this->feedback->comment, function ($message) {
                return $message->line('Comments: ' . $this->feedback->comment);
            })
            ->action('View Ticket', $url)
            ->line('Thank you for your support work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        Log::info('TicketFeedbackReceived database notification being created', [
            'user_id' => $notifiable->id, 
            'ticket_id' => $this->ticket->id
        ]);
        
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'Feedback received on ticket #' . $this->ticket->ticket_number . ' - ' . $this->formatRating($this->feedback->rating),
            'comment_preview' => $this->feedback->comment ?? 'No additional comments provided.',
            'url' => route('tickets.show', $this->ticket)
        ];
    }
    
    /**
     * Format the rating for display
     *
     * @param int $rating
     * @return string
     */
    protected function formatRating($rating)
    {
        $ratings = [
            1 => '★☆☆☆☆ (Very Poor)',
            2 => '★★☆☆☆ (Poor)', 
            3 => '★★★☆☆ (Average)',
            4 => '★★★★☆ (Good)',
            5 => '★★★★★ (Excellent)'
        ];
        
        return $ratings[$rating] ?? "{$rating}/5";
    }
} 