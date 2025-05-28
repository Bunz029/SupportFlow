<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssigned;
use App\Notifications\TicketStatusUpdated;
use App\Notifications\NewTicketComment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TestNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification sending and debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification system...');
        
        // Check if notifications table exists and is accessible
        try {
            $notificationCount = DB::table('notifications')->count();
            $this->info("Current notification count: {$notificationCount}");
        } catch (\Exception $e) {
            $this->error("Error accessing notifications table: {$e->getMessage()}");
            return 1;
        }
        
        // Get a user to notify
        try {
            $user = User::first();
            if (!$user) {
                $this->error("No users found in the database");
                return 1;
            }
            $this->info("Selected user for notification: {$user->name} (ID: {$user->id})");
        } catch (\Exception $e) {
            $this->error("Error finding a user: {$e->getMessage()}");
            return 1;
        }
        
        // Get a ticket for testing
        try {
            $ticket = Ticket::first();
            if (!$ticket) {
                $this->error("No tickets found in the database");
                return 1;
            }
            $this->info("Selected ticket for notification: #{$ticket->ticket_number} (ID: {$ticket->id})");
        } catch (\Exception $e) {
            $this->error("Error finding a ticket: {$e->getMessage()}");
            return 1;
        }

        // Attempt to send a notification
        try {
            $this->info("Sending test notification to {$user->email}...");
            
            // Create the notification instance
            $notification = new TicketAssigned($ticket);
            
            // 1. Try to save directly to database channel
            $this->info("Testing direct database channel...");
            $data = $notification->toArray($user);
            $notificationId = Str::uuid()->toString();
            
            DB::table('notifications')->insert([
                'id' => $notificationId,
                'type' => get_class($notification),
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->id,
                'data' => json_encode($data),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info("Direct database insert completed!");
            
            // 2. Try using the notification system
            $this->info("Testing notification system with explicit channels...");
            
            // Use the Notification facade with explicit database channel
            Notification::route('database', [
                'id' => $user->id,
                'type' => get_class($user),
            ])->notify($notification);
            
            $this->info("Notification sent via explicit database channel!");
            
            // Check if notification was created in database
            $newCount = DB::table('notifications')->count();
            $this->info("Final notification count: {$newCount}");
            
            // Fetch the notification details
            $latestNotification = DB::table('notifications')
                ->latest('created_at')
                ->first();
                
            if ($latestNotification) {
                $this->info("Latest notification data:");
                $this->info("ID: {$latestNotification->id}");
                $this->info("Type: {$latestNotification->type}");
                $this->info("Notifiable ID: {$latestNotification->notifiable_id}");
                $this->info("Created At: {$latestNotification->created_at}");
                $this->info("Data: " . substr($latestNotification->data, 0, 100) . "...");
            }
            
        } catch (\Exception $e) {
            $this->error("Error during notification test: {$e->getMessage()}");
            $this->error("Stack trace: {$e->getTraceAsString()}");
            return 1;
        }

        return 0;
    }
} 