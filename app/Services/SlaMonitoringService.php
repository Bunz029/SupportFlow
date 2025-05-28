<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketSla;
use Illuminate\Support\Facades\Log;
use App\Notifications\SlaBreachNotification;
use App\Notifications\SlaAtRiskNotification;
use Illuminate\Support\Facades\Notification;

class SlaMonitoringService
{
    /**
     * Check all active tickets for SLA breaches
     * 
     * @return array Statistics about the checked SLAs
     */
    public function checkAllSlas(): array
    {
        $stats = [
            'total_checked' => 0,
            'response_breaches' => 0,
            'resolution_breaches' => 0,
        ];

        // Get all active tickets with their SLAs
        $ticketSlas = TicketSla::with(['ticket', 'ticket.user', 'ticket.agent'])
            ->whereHas('ticket', function ($query) {
                $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
            })
            ->where(function ($query) {
                $query->where('response_breached', false)
                    ->orWhere('resolution_breached', false);
            })
            ->get();

        $stats['total_checked'] = $ticketSlas->count();

        foreach ($ticketSlas as $ticketSla) {
            $updated = false;

            // Check for response breach
            if (!$ticketSla->response_breached && !$ticketSla->first_response_at && now()->gt($ticketSla->response_due_at)) {
                $ticketSla->response_breached = true;
                $stats['response_breaches']++;
                $updated = true;
                
                Log::warning("Response SLA breached for ticket #{$ticketSla->ticket->ticket_number}");
                
                // Send notification to agent and admin
                $this->sendBreachNotification($ticketSla, 'response');
            }

            // Check for resolution breach
            if (!$ticketSla->resolution_breached && now()->gt($ticketSla->resolution_due_at)) {
                $ticketSla->resolution_breached = true;
                $stats['resolution_breaches']++;
                $updated = true;
                
                Log::warning("Resolution SLA breached for ticket #{$ticketSla->ticket->ticket_number}");
                
                // Send notification to agent and admin
                $this->sendBreachNotification($ticketSla, 'resolution');
            }

            if ($updated) {
                $ticketSla->save();
            }
        }

        return $stats;
    }
    
    /**
     * Send SLA breach notifications to agent and admin
     */
    protected function sendBreachNotification(TicketSla $ticketSla, string $breachType): void
    {
        // Prepare the notification
        $notification = new SlaBreachNotification($ticketSla, $breachType);
        
        // Get recipients - the agent and administrators
        $recipients = [];
        
        // Add the agent if assigned
        if ($ticketSla->ticket->agent) {
            $recipients[] = $ticketSla->ticket->agent;
        }
        
        // Add all admins
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            $recipients[] = $admin;
        }
        
        // Send notifications
        Notification::send($recipients, $notification);
    }

    /**
     * Get tickets that are at risk of breaching their SLA
     * 
     * @param int $responseHoursThreshold Hours before response SLA breach to warn
     * @param int $resolutionHoursThreshold Hours before resolution SLA breach to warn
     * @return array Tickets at risk of breaching SLA
     */
    public function getTicketsAtRisk(int $responseHoursThreshold = 2, int $resolutionHoursThreshold = 4): array
    {
        $now = now();
        $responseThreshold = $now->copy()->addHours($responseHoursThreshold);
        $resolutionThreshold = $now->copy()->addHours($resolutionHoursThreshold);

        // Response SLA at risk - includes both approaching deadline and already breached
        $responseAtRisk = TicketSla::with(['ticket', 'ticket.user', 'ticket.agent'])
            ->whereHas('ticket', function ($query) {
                $query->whereIn('status', ['open', 'in_progress']);
            })
            ->where(function ($query) use ($now, $responseThreshold) {
                $query->where(function ($q) use ($now, $responseThreshold) {
                    // Tickets approaching response deadline
                    $q->whereNull('first_response_at')
                      ->where('response_breached', false)
                      ->where('response_due_at', '<=', $responseThreshold)
                      ->where('response_due_at', '>', $now);
                })->orWhere(function ($q) {
                    // Already breached tickets that are still open
                    $q->where('response_breached', true)
                      ->whereNull('first_response_at');
                });
            })
            ->get();

        // Resolution SLA at risk - includes both approaching deadline and already breached
        $resolutionAtRisk = TicketSla::with(['ticket', 'ticket.user', 'ticket.agent'])
            ->whereHas('ticket', function ($query) {
                $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
            })
            ->where(function ($query) use ($now, $resolutionThreshold) {
                $query->where(function ($q) use ($now, $resolutionThreshold) {
                    // Tickets approaching resolution deadline
                    $q->where('resolution_breached', false)
                      ->where('resolution_due_at', '<=', $resolutionThreshold)
                      ->where('resolution_due_at', '>', $now);
                })->orWhere(function ($q) {
                    // Already breached tickets that are still open
                    $q->where('resolution_breached', true);
                });
            })
            ->get();

        return [
            'response_at_risk' => $responseAtRisk,
            'resolution_at_risk' => $resolutionAtRisk
        ];
    }
    
    /**
     * Send notifications for tickets at risk of breaching SLA
     * 
     * @param int $responseHoursThreshold Hours before response SLA breach to warn
     * @param int $resolutionHoursThreshold Hours before resolution SLA breach to warn
     * @return array Statistics about notifications sent
     */
    public function sendAtRiskNotifications(int $responseHoursThreshold = 2, int $resolutionHoursThreshold = 4): array
    {
        $atRiskTickets = $this->getTicketsAtRisk($responseHoursThreshold, $resolutionHoursThreshold);
        $stats = [
            'response_notifications' => 0,
            'resolution_notifications' => 0
        ];
        
        // Process response at-risk tickets
        foreach ($atRiskTickets['response_at_risk'] as $ticketSla) {
            $timeRemaining = now()->diffInMinutes($ticketSla->response_due_at);
            $this->sendAtRiskNotification($ticketSla, 'response', $timeRemaining);
            $stats['response_notifications']++;
        }
        
        // Process resolution at-risk tickets
        foreach ($atRiskTickets['resolution_at_risk'] as $ticketSla) {
            $timeRemaining = now()->diffInMinutes($ticketSla->resolution_due_at);
            $this->sendAtRiskNotification($ticketSla, 'resolution', $timeRemaining);
            $stats['resolution_notifications']++;
        }
        
        return $stats;
    }
    
    /**
     * Send a notification for a ticket at risk of breaching SLA
     */
    protected function sendAtRiskNotification(TicketSla $ticketSla, string $riskType, int $timeRemaining): void
    {
        // Prepare the notification
        $notification = new SlaAtRiskNotification($ticketSla, $riskType, $timeRemaining);
        
        // Get recipients - primarily the agent
        $recipients = [];
        
        // Add the agent if assigned
        if ($ticketSla->ticket->agent) {
            $recipients[] = $ticketSla->ticket->agent;
        } else {
            // If no agent, notify admins about unassigned ticket
            $admins = \App\Models\User::role('admin')->get();
            foreach ($admins as $admin) {
                $recipients[] = $admin;
            }
        }
        
        // Send notifications
        if (!empty($recipients)) {
            Notification::send($recipients, $notification);
        }
    }
} 