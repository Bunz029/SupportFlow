<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        Log::info('TicketAssigned notification created', ['ticket_id' => $ticket->id]);
    }

    public function via($notifiable)
    {
        Log::info('TicketAssigned notification channels', ['user_id' => $notifiable->id]);
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('tickets.show', $this->ticket->id);
        Log::info('TicketAssigned email being sent', ['user_id' => $notifiable->id, 'ticket_id' => $this->ticket->id]);
        
        return (new MailMessage)
            ->subject('Ticket Assigned: #' . $this->ticket->ticket_number)
            ->line('A ticket has been assigned to you.')
            ->line('Ticket: #' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->action('View Ticket', $url)
            ->line('Please review and respond to this ticket as soon as possible.');
    }

    public function toArray($notifiable)
    {
        Log::info('TicketAssigned database notification being created', ['user_id' => $notifiable->id, 'ticket_id' => $this->ticket->id]);
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'Ticket #' . $this->ticket->ticket_number . ' has been assigned to you',
            'url' => route('tickets.show', $this->ticket)
        ];
    }
} 