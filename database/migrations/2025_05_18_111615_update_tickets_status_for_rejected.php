<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration ensures that the ticket status column accepts 'rejected' value
        // For MySQL, we can use ENUM if it's defined that way
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY status ENUM('open', 'in_progress', 'waiting_customer', 'closed', 'rejected') NOT NULL DEFAULT 'open'");
        }
        
        // For other database drivers, we need to ensure the validation happens at the application level
        // through the Ticket::STATUSES constant
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original values if needed
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tickets MODIFY status ENUM('open', 'in_progress', 'waiting_customer', 'closed') NOT NULL DEFAULT 'open'");
        }
    }
};
