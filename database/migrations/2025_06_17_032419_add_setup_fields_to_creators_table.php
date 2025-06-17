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
        Schema::table('creators', function (Blueprint $table) {
            $table->timestamp('setup_completed_at')->nullable();
            $table->string('main_game')->nullable();
            $table->string('rank_info')->nullable();
            $table->decimal('default_hourly_rate', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creators', function (Blueprint $table) {
            $table->dropColumn(['setup_completed_at', 'main_game', 'rank_info', 'default_hourly_rate']);
        });
    }
};
