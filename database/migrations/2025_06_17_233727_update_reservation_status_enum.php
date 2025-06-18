<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to drop and recreate the enum constraint
        DB::statement("UPDATE reservations SET status = 'pending' WHERE status NOT IN ('pending', 'confirmed', 'cancelled', 'rescheduled', 'completed', 'no_show_customer', 'no_show_creator')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
        });
    }
};
