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
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->timestamp('confirmed_at')->nullable()->after('cancelled_at');
            $table->timestamp('completed_at')->nullable()->after('confirmed_at');
            $table->timestamp('rescheduled_at')->nullable()->after('completed_at');
            $table->timestamp('no_show_at')->nullable()->after('rescheduled_at');
            $table->datetime('actual_start')->nullable()->after('no_show_at');
            $table->datetime('actual_end')->nullable()->after('actual_start');
            $table->text('session_notes')->nullable()->after('actual_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_reason',
                'cancelled_at',
                'confirmed_at',
                'completed_at',
                'rescheduled_at',
                'no_show_at',
                'actual_start',
                'actual_end',
                'session_notes'
            ]);
        });
    }
};
