<?php

namespace App\Console\Commands;

use App\Services\SlaMonitoringService;
use Illuminate\Console\Command;

class SendSlaNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:notify {--response-hours=2} {--resolution-hours=4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for tickets at risk of breaching SLA';

    /**
     * Execute the console command.
     */
    public function handle(SlaMonitoringService $slaService)
    {
        $responseHours = $this->option('response-hours');
        $resolutionHours = $this->option('resolution-hours');
        
        $this->info("Sending notifications for tickets at risk of breaching SLA within {$responseHours}h (response) and {$resolutionHours}h (resolution)...");
        
        $stats = $slaService->sendAtRiskNotifications($responseHours, $resolutionHours);
        
        $this->info('Notifications sent.');
        $this->line('');
        $this->line('Results:');
        $this->line("- Response SLA notifications: {$stats['response_notifications']}");
        $this->line("- Resolution SLA notifications: {$stats['resolution_notifications']}");
        
        return Command::SUCCESS;
    }
} 