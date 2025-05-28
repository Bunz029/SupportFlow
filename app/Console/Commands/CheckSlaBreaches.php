<?php

namespace App\Console\Commands;

use App\Models\TicketSla;
use App\Models\User;
use App\Notifications\SlaBreachWarning;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckSlaBreaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:check-breaches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for tickets that are close to SLA breach and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for SLA breaches...');
        
        // Check notification settings
        $settings = Cache::get('system_settings', []);
        $shouldNotifyOnSLABreach = $settings['notify_on_sla_breach'] ?? true;
        
        if (!$shouldNotifyOnSLABreach) {
            $this->info('SLA breach notifications are disabled in settings.');
            return 0;
        }
        
        // Get active agents to notify
        $agents = User::where('role', 'agent')->where('status', 'active')->get();
        
        if ($agents->count() === 0) {
            $this->warn('No active agents found to notify about SLA breaches.');
            return 0;
        }
        
        // Warning time: 1 hour before breach
        $warningTimeMinutes = 60;
        
        // Check for response time breach warnings
        $this->checkBreachWarnings('response', $warningTimeMinutes, $agents);
        
        // Check for resolution time breach warnings
        $this->checkBreachWarnings('resolution', $warningTimeMinutes, $agents);
        
        $this->info('SLA breach check completed successfully.');
        return 0;
    }
    
    /**
     * Check for tickets approaching breach and send warnings
     */
    private function checkBreachWarnings($type, $warningMinutes, $agents)
    {
        $now = Carbon::now();
        $field = $type . '_due_at';
        $breachedField = $type . '_breached';
        
        // Find tickets approaching breach time but not yet breached
        $atRiskTickets = TicketSla::with('ticket')
            ->whereNull($breachedField)
            ->whereNotNull($field)
            ->where($field, '>', $now)
            ->where($field, '<=', $now->copy()->addMinutes($warningMinutes))
            ->get();
        
        foreach ($atRiskTickets as $ticketSla) {
            // Skip if ticket is null (shouldn't happen but just in case)
            if (!$ticketSla->ticket) {
                continue;
            }
            
            // Calculate time remaining
            $dueAt = Carbon::parse($ticketSla->$field);
            $minutesRemaining = $now->diffInMinutes($dueAt);
            
            $this->info("Ticket #{$ticketSla->ticket->ticket_number} {$type} SLA breach warning - {$minutesRemaining} minutes remaining");
            
            // Create notification
            $notification = new SlaBreachWarning(
                $ticketSla->ticket,
                $ticketSla,
                $type,
                $minutesRemaining
            );
            
            // Store notification for each agent
            foreach ($agents as $agent) {
                $agent->storeNotification($notification);
            }
            
            // Also notify the assigned agent if one exists
            if ($ticketSla->ticket->agent_id) {
                // Get the agent if they're not already in our list
                $assignedAgent = $agents->firstWhere('id', $ticketSla->ticket->agent_id);
                
                if (!$assignedAgent) {
                    $assignedAgent = User::find($ticketSla->ticket->agent_id);
                    if ($assignedAgent) {
                        $assignedAgent->storeNotification($notification);
                        // Add them to our notification list
                        $agents->push($assignedAgent);
                    }
                }
            }
            
            // Send notifications
            Notification::send($agents, $notification);
            
            Log::info("{$type} SLA breach warning notification sent", [
                'ticket_id' => $ticketSla->ticket->id,
                'ticket_number' => $ticketSla->ticket->ticket_number,
                'minutes_remaining' => $minutesRemaining,
                'agent_count' => $agents->count()
            ]);
        }
        
        $this->info("Processed " . $atRiskTickets->count() . " tickets at risk of {$type} SLA breach");
    }
} 