<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\Sla;
use App\Models\TicketSla;
use Carbon\Carbon;

class TestSlaBreachSeeder extends Seeder
{
    public function run()
    {
        // Find or create a test client user
        $client = User::firstOrCreate(
            ['email' => 'testclient@example.com'],
            [
                'name' => 'Test Client',
                'password' => bcrypt('password'),
                'role' => 'client'
            ]
        );

        // Get the first category or create a default one
        $category = Category::first() ?? Category::create([
            'name' => 'General',
            'description' => 'General support category'
        ]);

        // Create tickets with different priorities that are already breached
        $priorities = ['high', 'medium', 'low'];
        
        foreach ($priorities as $priority) {
            // Create a ticket for each priority that's breached
            $ticket = Ticket::create([
                'user_id' => $client->id,
                'category_id' => $category->id,
                'subject' => "Test {$priority} priority SLA breach ticket",
                'description' => "This is a test ticket to demonstrate SLA breach for {$priority} priority",
                'status' => 'open',
                'priority' => $priority,
                'created_at' => Carbon::now()->subDays(5), // Created 5 days ago
                'updated_at' => Carbon::now()->subDays(5) // No updates for 5 days
            ]);
            
            // Get SLA policy for this priority or create a default one
            $sla = Sla::where('priority', $priority)->first();
            if (!$sla) {
                $responseHours = match($priority) {
                    'high' => 2,
                    'medium' => 8,
                    'low' => 24,
                    default => 24
                };
                
                $sla = Sla::create([
                    'name' => ucfirst($priority) . ' Priority SLA',
                    'response_time_hours' => $responseHours,
                    'resolution_time_hours' => $responseHours * 3,
                    'priority' => $priority
                ]);
            }
            
            // Create the SLA tracking record for this ticket - showing it as breached
            TicketSla::create([
                'ticket_id' => $ticket->id,
                'sla_id' => $sla->id,
                'response_due_at' => Carbon::now()->subDays(4), // Due date in the past
                'resolution_due_at' => Carbon::now()->subDays(2), // Due date in the past
                'response_breached' => true, // Mark as breached
                'resolution_breached' => true, // Mark as breached
                'first_response_at' => null // No response recorded
            ]);
        }
    }
}
 