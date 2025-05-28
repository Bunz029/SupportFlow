<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, string $rejectionReason)
    {
        $this->ticket = $ticket;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = route('tickets.show', $this->ticket->id);
        
        return (new MailMessage)
            ->subject('Ticket Rejected: #' . $this->ticket->ticket_number)
            ->line('Your ticket has been rejected.')
            ->line('Ticket: #' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->line('Reason for rejection: ' . $this->rejectionReason)
            ->action('View Ticket', $url);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'Ticket #' . $this->ticket->ticket_number . ' has been rejected',
            'reason' => $this->rejectionReason,
            'url' => route('tickets.show', $this->ticket->id)
        ];
    }
} 