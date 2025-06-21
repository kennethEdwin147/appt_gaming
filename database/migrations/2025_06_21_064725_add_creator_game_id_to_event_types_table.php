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
        Schema::table('event_types', function (Blueprint $table) {
            // Référence vers le jeu choisi pour les sessions de groupe
            $table->foreignId('creator_game_id')->nullable()->constrained()->onDelete('set null')->comment('Jeu imposé pour les sessions de groupe')->after('allow_game_choice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            $table->dropForeign(['creator_game_id']);
            $table->dropColumn('creator_game_id');
        });
    }
};
