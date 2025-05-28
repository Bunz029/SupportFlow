<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AgentAssignedToTicket extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $agent;

    /**
     * Create a new notification instance.
     *
     * @param Ticket $ticket
     * @param User $agent
     * @return void
     */
    public function __construct(Ticket $ticket, User $agent)
    {
        $this->ticket = $ticket;
        $this->agent = $agent;
        Log::info('AgentAssignedToTicket notification created', ['ticket_id' => $ticket->id, 'agent_id' => $agent->id]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info('AgentAssignedToTicket notification channels', ['user_id' => $notifiable->id]);
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
        Log::info('AgentAssignedToTicket email being sent', ['user_id' => $notifiable->id, 'ticket_id' => $this->ticket->id]);
        
        return (new MailMessage)
            ->subject('Agent Assigned to Ticket: #' . $this->ticket->ticket_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('An agent has been assigned to your ticket #' . $this->ticket->ticket_number)
            ->line('Agent: ' . $this->agent->name)
            ->line('Subject: ' . $this->ticket->subject)
            ->action('View Ticket', $url)
            ->line('Your ticket is now being handled by our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        Log::info('AgentAssignedToTicket database notification being created', [
            'user_id' => $notifiable->id, 
            'ticket_id' => $this->ticket->id,
            'agent_id' => $this->agent->id
        ]);
        
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'Agent ' . $this->agent->name . ' has been assigned to ticket #' . $this->ticket->ticket_number,
            'comment_preview' => 'Your ticket is now being handled by our support team.',
            'url' => route('tickets.show', $this->ticket)
        ];
    }
} 