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
          Schema::create('creators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('gaming_pseudo')->nullable()->unique();
            $table->text('bio')->nullable();
            $table->string('platform_name')->nullable();
            $table->string('platform_url')->nullable();
            $table->string('type')->nullable();
            $table->string('timezone')->nullable();  // Fuseau horaire du créateur
            $table->text('confirmation_token')->nullable();  // Token de confirmation pour l'inscription
            $table->timestamp('confirmed_at')->nullable();  // Date de confirmation du compte
            $table->decimal('platform_commission_rate', 5, 2)->default(0.05)->comment('Taux de commission de la plateforme pour ce créateur (par défaut 5%)');
            $table->timestamp('setup_completed_at')->nullable();
            $table->string('main_game')->nullable();
            $table->string('rank_info')->nullable();
            $table->decimal('default_hourly_rate', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creators');
    }
};
