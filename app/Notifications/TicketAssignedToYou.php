<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketAssignedToYou extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        Log::info('TicketAssignedToYou notification created', ['ticket_id' => $ticket->id]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info('TicketAssignedToYou notification channels', ['user_id' => $notifiable->id]);
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
        $url = route('tickets.show', $this->ticket->id);
        Log::info('TicketAssignedToYou email being sent', ['user_id' => $notifiable->id, 'ticket_id' => $this->ticket->id]);
        
        return (new MailMessage)
            ->subject('You\'ve been assigned to Ticket: #' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned to ticket #' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->line('Priority: ' . ucfirst($this->ticket->priority))
            ->when($this->ticket->due_date, function ($message) {
                return $message->line('Due Date: ' . $this->ticket->due_date->format('M d, Y'));
            })
            ->action('View Ticket', $url)
            ->line('Please review and respond to this ticket as soon as possible.')
            ->line('Thank you for your dedication to our support team!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        Log::info('TicketAssignedToYou database notification being created', [
            'user_id' => $notifiable->id, 
            'ticket_id' => $this->ticket->id
        ]);
        
        $priorityClass = [
            'low' => 'green',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red'
        ][$this->ticket->priority] ?? 'gray';
        
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'You\'ve been assigned to ticket #' . $this->ticket->ticket_number,
            'comment_preview' => $this->ticket->subject . ' (Priority: ' . ucfirst($this->ticket->priority) . ')',
            'url' => route('tickets.show', $this->ticket->id),
            'priority' => $this->ticket->priority,
            'priority_class' => $priorityClass
        ];
    }
} 