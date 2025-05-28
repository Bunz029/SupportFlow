<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketSla;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendSlaReportSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:report-summary {--period=daily} {--recipients=admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SLA summary report via email (daily or weekly)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->option('period');
        $recipients = $this->option('recipients');
        
        $this->info("Generating {$period} SLA report summary...");
        
        // Determine date range based on period
        $startDate = $period === 'weekly' 
            ? Carbon::now()->subWeek()->startOfDay() 
            : Carbon::now()->subDay()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        // Get SLA statistics
        $stats = $this->getSlaStats($startDate, $endDate);
        
        $this->info("Report generated for period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tickets', $stats['totalTickets']],
                ['With SLA', $stats['totalWithSla']],
                ['Response Breaches', $stats['responseBreaches']],
                ['Resolution Breaches', $stats['resolutionBreaches']],
                ['Response Breach Rate', $stats['responseBreachRate'] . '%'],
                ['Resolution Breach Rate', $stats['resolutionBreachRate'] . '%'],
            ]
        );
        
        // Get recipients
        $emailRecipients = $this->getRecipients($recipients);
        
        if (empty($emailRecipients)) {
            $this->error('No recipients found for the specified criteria.');
            return Command::FAILURE;
        }
        
        // Send emails
        $this->info("Sending report to " . count($emailRecipients) . " recipients...");
        
        $reportData = [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
        ];
        
        foreach ($emailRecipients as $recipient) {
            Mail::to($recipient)->send(new \App\Mail\SlaSummaryReport($reportData));
            $this->line("- Email sent to: {$recipient['email']}");
        }
        
        $this->info("Report emails have been sent successfully.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get SLA statistics for the given period
     */
    private function getSlaStats(Carbon $startDate, Carbon $endDate): array
    {
        // Get overall statistics
        $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $totalWithSla = TicketSla::whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
            
        $responseBreaches = TicketSla::where('response_breached', true)
            ->whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
            
        $resolutionBreaches = TicketSla::where('resolution_breached', true)
            ->whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
            
        // Calculate breach rates
        $responseBreachRate = $totalWithSla > 0 ? round(($responseBreaches / $totalWithSla) * 100, 2) : 0;
        $resolutionBreachRate = $totalWithSla > 0 ? round(($resolutionBreaches / $totalWithSla) * 100, 2) : 0;
        
        return [
            'totalTickets' => $totalTickets,
            'totalWithSla' => $totalWithSla,
            'responseBreaches' => $responseBreaches,
            'resolutionBreaches' => $resolutionBreaches,
            'responseBreachRate' => $responseBreachRate,
            'resolutionBreachRate' => $resolutionBreachRate,
        ];
    }
    
    /**
     * Get email recipients based on the specified criteria
     */
    private function getRecipients(string $recipientType): array
    {
        if ($recipientType === 'all') {
            // Send to all admins and agents
            return User::whereIn('role', ['admin', 'agent'])->get()->toArray();
        } elseif ($recipientType === 'admin') {
            // Send only to admins
            return User::where('role', 'admin')->get()->toArray();
        } elseif ($recipientType === 'agent') {
            // Send only to agents
            return User::where('role', 'agent')->get()->toArray();
        } else {
            // Try to find specific user emails (comma-separated list)
            $emails = explode(',', $recipientType);
            return User::whereIn('email', $emails)->get()->toArray();
        }
    }
} 