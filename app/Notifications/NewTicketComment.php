<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketComment extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('tickets.show', $this->comment->ticket->id);
        
        return (new MailMessage)
            ->subject('New Comment on Ticket #' . $this->comment->ticket->ticket_number)
            ->line('A new comment has been added to your ticket.')
            ->line('Ticket: #' . $this->comment->ticket->ticket_number)
            ->line('Comment by: ' . $this->comment->user->name)
            ->line('Comment: ' . \Illuminate\Support\Str::limit($this->comment->message, 100))
            ->action('View Ticket', $url);
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->comment->ticket->id,
            'ticket_number' => $this->comment->ticket->ticket_number,
            'comment_id' => $this->comment->id,
            'message' => 'New comment on ticket #' . $this->comment->ticket->ticket_number . ' by ' . $this->comment->user->name,
            'comment_preview' => \Illuminate\Support\Str::limit($this->comment->message, 100),
            'url' => route('tickets.show', $this->comment->ticket->id)
        ];
    }
} 