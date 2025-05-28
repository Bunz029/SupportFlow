<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $oldStatus;

    public function __construct(Ticket $ticket, $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('tickets.show', $this->ticket->id);
        $newStatus = ucfirst(str_replace('_', ' ', $this->ticket->status));
        
        return (new MailMessage)
            ->subject('Ticket Status Updated: #' . $this->ticket->ticket_number)
            ->line('The status of your ticket has been updated.')
            ->line('Ticket: #' . $this->ticket->ticket_number)
            ->line('New Status: ' . $newStatus)
            ->action('View Ticket', $url);
    }

    public function toArray($notifiable)
    {
        $newStatus = ucfirst(str_replace('_', ' ', $this->ticket->status));
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'Ticket #' . $this->ticket->ticket_number . ' status updated to ' . $newStatus,
            'old_status' => ucfirst(str_replace('_', ' ', $this->oldStatus)),
            'new_status' => $newStatus,
            'url' => route('tickets.show', $this->ticket->id)
        ];
    }
} 