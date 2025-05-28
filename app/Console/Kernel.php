<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckSlaBreaches;
use App\Console\Commands\CheckSlaAtRisk;
use App\Console\Commands\SendSlaNotifications;
use App\Console\Commands\SendSlaReportSummary;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckSlaBreaches::class,
        CheckSlaAtRisk::class,
        SendSlaNotifications::class,
        SendSlaReportSummary::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for SLA breaches every 15 minutes
        $schedule->command('sla:check-breaches')->everyFifteenMinutes();
        
        // Check for at-risk SLAs every 15 minutes
        $schedule->command('sla:check-at-risk')->everyFifteenMinutes();
        
        // Send notifications for at-risk SLAs hourly
        $schedule->command('sla:notify')->hourly();
        
        // Send daily SLA summary report at 8 AM
        $schedule->command('sla:report-summary --period=daily --recipients=admin')
            ->dailyAt('08:00')
            ->emailOutputTo('it-team@example.com');
            
        // Send weekly SLA summary report at 9 AM on Mondays
        $schedule->command('sla:report-summary --period=weekly --recipients=all')
            ->weeklyOn(1, '09:00')
            ->emailOutputTo('management@example.com');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 