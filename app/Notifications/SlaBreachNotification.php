<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketSla;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlaBreachNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticketSla;
    protected $breachType;

    /**
     * Create a new notification instance.
     *
     * @param TicketSla $ticketSla
     * @param string $breachType Either 'response' or 'resolution'
     */
    public function __construct(TicketSla $ticketSla, string $breachType)
    {
        $this->ticketSla = $ticketSla;
        $this->breachType = $breachType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $ticket = $this->ticketSla->ticket;
        $breachTitle = $this->breachType === 'response' ? 'Response Time' : 'Resolution Time';
        $slaTime = $this->breachType === 'response' 
            ? $this->ticketSla->sla->response_time_hours 
            : $this->ticketSla->sla->resolution_time_hours;
        $dueAt = $this->breachType === 'response' 
            ? $this->ticketSla->response_due_at 
            : $this->ticketSla->resolution_due_at;

        return (new MailMessage)
            ->subject("SLA Breach Alert: {$breachTitle} for Ticket #{$ticket->ticket_number}")
            ->greeting("SLA Breach Alert")
            ->line("This is an alert that the {$breachTitle} SLA for ticket #{$ticket->ticket_number} has been breached.")
            ->line("Ticket: {$ticket->subject}")
            ->line("Customer: {$ticket->user->name}")
            ->line("Priority: " . ucfirst($ticket->priority))
            ->line("SLA Required: {$slaTime} hours")
            ->line("Due by: {$dueAt->format('M d, Y H:i')}")
            ->line("Assigned to: " . ($ticket->agent ? $ticket->agent->name : 'Unassigned'))
            ->action('View Ticket', url("/tickets/{$ticket->id}"))
            ->line('Please take immediate action to address this ticket.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $ticket = $this->ticketSla->ticket;
        
        return [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'breach_type' => $this->breachType,
            'due_at' => $this->breachType === 'response' 
                ? $this->ticketSla->response_due_at 
                : $this->ticketSla->resolution_due_at,
        ];
    }
} 