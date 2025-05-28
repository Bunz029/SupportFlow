<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class SlaSummaryReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The report data.
     *
     * @var array
     */
    public $reportData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $period = ucfirst($this->reportData['period']);
        $datePeriod = $this->reportData['startDate']->format('M d') . ' - ' . $this->reportData['endDate']->format('M d, Y');
        
        return new Envelope(
            subject: "SupportFlow {$period} SLA Performance Report: {$datePeriod}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sla-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 