<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            
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
        });
    }
} 