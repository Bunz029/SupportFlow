<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketSla;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlaAtRiskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticketSla;
    protected $riskType;
    protected $timeRemaining;

    /**
     * Create a new notification instance.
     *
     * @param TicketSla $ticketSla
     * @param string $riskType Either 'response' or 'resolution'
     * @param int $timeRemaining Time remaining in minutes
     */
    public function __construct(TicketSla $ticketSla, string $riskType, int $timeRemaining)
    {
        $this->ticketSla = $ticketSla;
        $this->riskType = $riskType;
        $this->timeRemaining = $timeRemaining;
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
        $riskTitle = $this->riskType === 'response' ? 'Response Time' : 'Resolution Time';
        $hours = floor($this->timeRemaining / 60);
        $minutes = $this->timeRemaining % 60;
        $timeLeft = "{$hours}h {$minutes}m";
        
        $dueAt = $this->riskType === 'response' 
            ? $this->ticketSla->response_due_at 
            : $this->ticketSla->resolution_due_at;

        return (new MailMessage)
            ->subject("SLA Warning: {$riskTitle} for Ticket #{$ticket->ticket_number}")
            ->greeting("SLA Alert: {$timeLeft} Remaining")
            ->line("This is a warning that the {$riskTitle} SLA for ticket #{$ticket->ticket_number} is at risk of being breached.")
            ->line("Ticket: {$ticket->subject}")
            ->line("Customer: {$ticket->user->name}")
            ->line("Priority: " . ucfirst($ticket->priority))
            ->line("Time remaining: {$timeLeft}")
            ->line("Due by: {$dueAt->format('M d, Y H:i')}")
            ->line("Assigned to: " . ($ticket->agent ? $ticket->agent->name : 'Unassigned'))
            ->action('View Ticket', url("/tickets/{$ticket->id}"))
            ->line('Please take action to ensure this ticket is handled within the SLA timeframe.');
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
        $hours = floor($this->timeRemaining / 60);
        $minutes = $this->timeRemaining % 60;
        
        return [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'risk_type' => $this->riskType,
            'time_remaining' => [
                'total_minutes' => $this->timeRemaining,
                'formatted' => "{$hours}h {$minutes}m"
            ],
            'due_at' => $this->riskType === 'response' 
                ? $this->ticketSla->response_due_at 
                : $this->ticketSla->resolution_due_at,
        ];
    }
} 