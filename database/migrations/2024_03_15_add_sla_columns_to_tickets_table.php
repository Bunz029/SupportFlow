<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('first_response_at')->nullable();
            $table->boolean('sla_breached')->default(false);
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('resolution_due_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'first_response_at',
                'sla_breached',
                'sla_due_at',
                'resolution_due_at'
            ]);
        });
    }
}; 