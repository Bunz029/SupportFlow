<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Sla;
use App\Models\Ticket;
use App\Models\TicketSla;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all clients, agents, and categories
        $clients = User::role('client')->get();
        $agents = User::role('agent')->get();
        $categories = Category::all();
        $slas = Sla::all();

        // Create 10 tickets
        $tickets = [
            [
                'subject' => 'Cannot login to my account',
                'description' => 'I am unable to login to my account since yesterday. I keep getting "Invalid credentials" error even though I am sure my password is correct.',
                'status' => 'open',
                'priority' => 'high',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'subject' => 'Feature request: Dark mode',
                'description' => 'I would like to request a dark mode feature for the dashboard. It would be easier on the eyes, especially when working late.',
                'status' => 'in_progress',
                'priority' => 'low',
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'subject' => 'Billing discrepancy in last invoice',
                'description' => 'My latest invoice (INV-2023-1234) shows a charge for features I am not using. Please review and adjust the billing accordingly.',
                'status' => 'open',
                'priority' => 'medium',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'subject' => 'Application crashing on file upload',
                'description' => 'Whenever I try to upload a file larger than 10MB, the application crashes and I need to refresh the page. This happens consistently.',
                'status' => 'in_progress',
                'priority' => 'high',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'subject' => 'How to configure email notifications',
                'description' => 'I would like to know how to configure email notifications for my account. I want to receive notifications only for high priority items.',
                'status' => 'waiting_customer',
                'priority' => 'low',
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'subject' => 'Data export not working',
                'description' => 'The data export feature is not working. When I click on "Export to CSV", nothing happens. I need this report urgently.',
                'status' => 'open',
                'priority' => 'urgent',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'subject' => 'Account upgrade inquiry',
                'description' => 'I am interested in upgrading my account to the Premium plan. Can you provide details about the features and pricing?',
                'status' => 'closed',
                'priority' => 'medium',
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'subject' => 'API documentation clarification',
                'description' => 'I am having trouble understanding the API documentation for the /users endpoint. The parameters are not clearly explained.',
                'status' => 'open',
                'priority' => 'medium',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'subject' => 'Password reset not sending email',
                'description' => 'I tried to reset my password but I am not receiving any email with the reset link. I have checked my spam folder too.',
                'status' => 'in_progress',
                'priority' => 'high',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'subject' => 'Browser compatibility issue',
                'description' => 'The application is not working properly in Safari browser. The dashboard layout is broken and some features are not accessible.',
                'status' => 'open',
                'priority' => 'medium',
                'created_at' => Carbon::now()->subDays(4),
            ],
        ];

        foreach ($tickets as $index => $ticketData) {
            // Select a random client and category
            $client = $clients->random();
            $category = $categories->random();

            // Create the ticket
            $ticket = Ticket::create([
                'subject' => $ticketData['subject'],
                'description' => $ticketData['description'],
                'status' => $ticketData['status'],
                'priority' => $ticketData['priority'],
                'category_id' => $category->id,
                'user_id' => $client->id,
                'agent_id' => $index % 3 == 0 ? null : $agents->random()->id,
                'created_at' => $ticketData['created_at'],
                'updated_at' => $ticketData['created_at'],
            ]);

            // Create SLA for the ticket
            $sla = $slas->where('priority', $ticket->priority)->first();
            if ($sla) {
                TicketSla::create([
                    'ticket_id' => $ticket->id,
                    'sla_id' => $sla->id,
                    'response_due_at' => $ticket->created_at->addHours($sla->response_time_hours),
                    'resolution_due_at' => $ticket->created_at->addHours($sla->resolution_time_hours),
                    'response_breached' => false,
                    'resolution_breached' => false,
                ]);
            }

            // Add some comments to the ticket
            if ($index % 2 == 0) {
                // Customer comment
                Comment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $client->id,
                    'message' => 'Any update on this issue? I need it resolved as soon as possible.',
                    'created_at' => $ticket->created_at->addHours(rand(1, 5)),
                ]);

                // If ticket has an agent, add agent response
                if ($ticket->agent_id) {
                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $ticket->agent_id,
                        'message' => 'We are working on your issue and will update you as soon as possible.',
                        'created_at' => $ticket->created_at->addHours(rand(6, 10)),
                    ]);

                    // Add a private note
                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $ticket->agent_id,
                        'message' => 'Internal note: This issue requires further investigation with the development team.',
                        'is_private' => true,
                        'created_at' => $ticket->created_at->addHours(rand(6, 10)),
                    ]);
                }
            }
        }
    }
} 