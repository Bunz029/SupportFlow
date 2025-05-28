<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\Sla;
use App\Models\TicketSla;
use Carbon\Carbon;

class TestMetricsSeeder extends Seeder
{
    public function run()
    {
        // Find or create a test client user
        $client = User::firstOrCreate(
            ['email' => 'testmetrics@example.com'],
            [
                'name' => 'Test Metrics Client',
                'password' => bcrypt('password'),
                'role' => 'client'
            ]
        );

        // Get the first category or create a default one
        $category = Category::first() ?? Category::create([
            'name' => 'General',
            'description' => 'General support category'
        ]);

        // 1. Create tickets with response times (for Avg. Response Time)
        $this->createTicketsWithResponseTimes($client, $category);

        // 2. Create at-risk tickets (approaching SLA breach but not breached yet)
        $this->createAtRiskTickets($client, $category);
    }

    private function createTicketsWithResponseTimes($client, $category)
    {
        $priorities = ['high', 'medium', 'low'];
        $responseTimes = [
            1, // 1 hour response
            3, // 3 hours response
            5, // 5 hours response
        ];

        foreach ($priorities as $index => $priority) {
            // Get or create SLA policy
            $sla = $this->getOrCreateSla($priority);

            // Create ticket
            $ticket = Ticket::create([
                'user_id' => $client->id,
                'category_id' => $category->id,
                'subject' => "Response Time Test - {$priority} priority",
                'description' => "Testing response time tracking",
                'status' => 'closed',
                'priority' => $priority,
                'created_at' => Carbon::now()->subHours($responseTimes[$index] + 1),
                'updated_at' => Carbon::now()->subHours($responseTimes[$index])
            ]);

            // Create SLA record with response time
            TicketSla::create([
                'ticket_id' => $ticket->id,
                'sla_id' => $sla->id,
                'response_due_at' => Carbon::now()->addDay(),
                'resolution_due_at' => Carbon::now()->addDays(2),
                'response_breached' => false,
                'resolution_breached' => false,
                'first_response_at' => Carbon::now()->subHours($responseTimes[$index])
            ]);
        }
    }

    private function createAtRiskTickets($client, $category)
    {
        $priorities = ['high', 'medium', 'low'];
        
        foreach ($priorities as $priority) {
            // Get or create SLA policy
            $sla = $this->getOrCreateSla($priority);

            // Calculate when the ticket should be "at risk" (80% of the way to SLA breach)
            $responseHours = $sla->response_time_hours;
            $atRiskHours = $responseHours * 0.8;

            // Create ticket that's at risk
            $ticket = Ticket::create([
                'user_id' => $client->id,
                'category_id' => $category->id,
                'subject' => "At Risk Test - {$priority} priority",
                'description' => "Testing at-risk status",
                'status' => 'open',
                'priority' => $priority,
                'created_at' => Carbon::now()->subHours($atRiskHours),
                'updated_at' => Carbon::now()->subHours($atRiskHours)
            ]);

            // Create SLA record showing it's at risk
            TicketSla::create([
                'ticket_id' => $ticket->id,
                'sla_id' => $sla->id,
                'response_due_at' => Carbon::now()->addHours($responseHours - $atRiskHours),
                'resolution_due_at' => Carbon::now()->addDays(2),
                'response_breached' => false,
                'resolution_breached' => false,
                'first_response_at' => null
            ]);
        }
    }

    private function getOrCreateSla($priority)
    {
        return Sla::firstOrCreate(
            ['priority' => $priority],
            [
                'name' => ucfirst($priority) . ' Priority SLA',
                'response_time_hours' => match($priority) {
                    'high' => 2,
                    'medium' => 8,
                    'low' => 24,
                    default => 24
                },
                'resolution_time_hours' => match($priority) {
                    'high' => 24,
                    'medium' => 48,
                    'low' => 72,
                    default => 72
                },
            ]
        );
    }
} 