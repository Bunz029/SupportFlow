<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketSla;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SlaBreachWarning extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $ticketSla;
    protected $breachType;
    protected $timeRemaining;

    /**
     * Create a new notification instance.
     *
     * @param Ticket $ticket
     * @param TicketSla $ticketSla
     * @param string $breachType 'response' or 'resolution'
     * @param int $timeRemaining Minutes remaining before breach
     * @return void
     */
    public function __construct(Ticket $ticket, TicketSla $ticketSla, string $breachType, int $timeRemaining)
    {
        $this->ticket = $ticket;
        $this->ticketSla = $ticketSla;
        $this->breachType = $breachType;
        $this->timeRemaining = $timeRemaining;
        
        Log::info('SlaBreachWarning notification created', [
            'ticket_id' => $ticket->id,
            'breach_type' => $breachType,
            'time_remaining' => $timeRemaining
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info('SlaBreachWarning notification channels', ['user_id' => $notifiable->id]);
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
        $breachTitle = $this->breachType === 'response' ? 'Response Time' : 'Resolution Time';
        $hours = floor($this->timeRemaining / 60);
        $minutes = $this->timeRemaining % 60;
        $timeLeft = "{$hours}h {$minutes}m";
        
        Log::info('SlaBreachWarning email being sent', [
            'user_id' => $notifiable->id, 
            'ticket_id' => $this->ticket->id
        ]);
        
        return (new MailMessage)
            ->subject('SLA Warning: ' . $breachTitle . ' for Ticket #' . $this->ticket->ticket_number)
            ->line('⚠️ SLA ' . $breachTitle . ' is approaching its deadline.')
            ->line('Ticket: #' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->line('Time Remaining: ' . $timeLeft)
            ->line('Priority: ' . ucfirst($this->ticket->priority))
            ->action('View Ticket', $url)
            ->line('Please address this ticket promptly to maintain service level agreements.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $breachTitle = $this->breachType === 'response' ? 'Response Time' : 'Resolution Time';
        $hours = floor($this->timeRemaining / 60);
        $minutes = $this->timeRemaining % 60;
        $timeLeft = "{$hours}h {$minutes}m";
        
        Log::info('SlaBreachWarning database notification being created', [
            'user_id' => $notifiable->id, 
            'ticket_id' => $this->ticket->id
        ]);
        
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'SLA Warning: ' . $breachTitle . ' for Ticket #' . $this->ticket->ticket_number,
            'comment_preview' => 'Time Remaining: ' . $timeLeft . ' | Priority: ' . ucfirst($this->ticket->priority),
            'url' => route('tickets.show', $this->ticket),
            'priority' => $this->ticket->priority,
            'priority_class' => 'yellow'
        ];
    }
} 