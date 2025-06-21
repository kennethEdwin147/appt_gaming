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
        Schema::create('creator_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->onDelete('cascade');
            $table->string('platform')->default('pc')->comment('Gaming platform (pc, ps4, ps5, xbox_one, xbox_series, nintendo_switch, mobile)');
            $table->string('game_title')->comment('Name of the game');
            $table->boolean('is_active')->default(true)->comment('Is this game currently available for sessions');
            $table->timestamps();
            
            // Index pour performance
            $table->index(['creator_id', 'platform']);
            $table->index(['platform', 'is_active']);
            $table->index(['creator_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_games');
    }
};
