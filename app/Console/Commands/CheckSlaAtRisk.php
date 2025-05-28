<?php

namespace App\Console\Commands;

use App\Services\SlaMonitoringService;
use Illuminate\Console\Command;

class CheckSlaAtRisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:check-at-risk {--response-hours=2} {--resolution-hours=4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for tickets at risk of breaching SLA';

    /**
     * Execute the console command.
     */
    public function handle(SlaMonitoringService $slaService)
    {
        $responseHours = $this->option('response-hours');
        $resolutionHours = $this->option('resolution-hours');
        
        $this->info("Checking for tickets at risk of breaching SLA within {$responseHours}h (response) and {$resolutionHours}h (resolution)...");
        
        $atRiskTickets = $slaService->getTicketsAtRisk($responseHours, $resolutionHours);
        
        $responseCount = count($atRiskTickets['response_at_risk']);
        $resolutionCount = count($atRiskTickets['resolution_at_risk']);
        
        $this->info('Check completed.');
        $this->line('');
        $this->line('Results:');
        $this->line("- Tickets at risk of response breach: {$responseCount}");
        $this->line("- Tickets at risk of resolution breach: {$resolutionCount}");
        
        if ($responseCount > 0) {
            $this->line('');
            $this->line('Response SLA at risk:');
            $this->table(
                ['Ticket #', 'Subject', 'Customer', 'Agent', 'Due In'],
                $atRiskTickets['response_at_risk']->map(function ($ticketSla) {
                    $ticket = $ticketSla->ticket;
                    $dueIn = now()->diffInMinutes($ticketSla->response_due_at);
                    $dueFormatted = floor($dueIn / 60) . 'h ' . ($dueIn % 60) . 'm';
                    
                    return [
                        $ticket->ticket_number,
                        $ticket->subject,
                        $ticket->user->name,
                        $ticket->agent ? $ticket->agent->name : 'Unassigned',
                        $dueFormatted
                    ];
                })
            );
        }
        
        if ($resolutionCount > 0) {
            $this->line('');
            $this->line('Resolution SLA at risk:');
            $this->table(
                ['Ticket #', 'Subject', 'Customer', 'Agent', 'Due In'],
                $atRiskTickets['resolution_at_risk']->map(function ($ticketSla) {
                    $ticket = $ticketSla->ticket;
                    $dueIn = now()->diffInMinutes($ticketSla->resolution_due_at);
                    $dueFormatted = floor($dueIn / 60) . 'h ' . ($dueIn % 60) . 'm';
                    
                    return [
                        $ticket->ticket_number,
                        $ticket->subject,
                        $ticket->user->name,
                        $ticket->agent ? $ticket->agent->name : 'Unassigned',
                        $dueFormatted
                    ];
                })
            );
        }
        
        return Command::SUCCESS;
    }
} 