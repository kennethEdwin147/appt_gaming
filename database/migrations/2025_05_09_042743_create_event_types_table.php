<?php

/*
 * EVENT TYPES - Types d'événements créés par les créateurs
 * 
 * LOGIQUE PRINCIPALE :
 * 
 * 1. SESSIONS INDIVIDUELLES (session_type = 'individual') :
 *    - allow_game_choice = true
 *    - creator_game_id = null
 *    - default_max_participants = 1
 *    - Le CLIENT choisit le jeu dans la liste du créateur
 *    - Exemple : "Coaching personnalisé" 
 * 
 * 2. SESSIONS DE GROUPE (session_type = 'group') :
 *    - allow_game_choice = false  
 *    - creator_game_id = ID du jeu imposé
 *    - default_max_participants = 2-10
 *    - Le CRÉATEUR impose le jeu via creator_game_id
 *    - Exemple : "Soirée Valorant ranked" avec jeu fixé
 * 
 * WORKFLOW CRÉATION :
 * Step 1: Choix individual/group
 * Step 2: Nom, description, durée, prix
 * Step 3: Si group → choix du jeu, si individual → skip
 * Step 4: Participants (auto pour individual)
 * Step 5: Plateforme communication
 * Step 6: Récurrence optionnelle
 * 
 * RELATIONS :
 * - event_types belongsTo creator
 * - event_types belongsTo creator_game (nullable, pour group seulement)
 * - reservations belongsTo event_type
 * - time_slots générés automatiquement selon availabilities
 */

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
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            
            // === INFORMATIONS DE BASE ===
            $table->string('name')->comment('Nom de l\'événement (ex: "Coaching Valorant", "Soirée Among Us")');
            $table->text('description')->nullable()->comment('Description détaillée visible par les clients');
            
            // === DURÉE ET TARIFICATION ===
            $table->integer('default_duration')->default(60)->comment('Durée par défaut en minutes (30, 60, 90, 120...)');
            $table->decimal('default_price', 10, 2)->nullable()->comment('Prix par participant en CAD');
            
            // === TYPE DE SESSION (CORE FEATURE) ===
            $table->integer('default_max_participants')->nullable()->comment('Nombre max de participants (1 pour individual, 2-10 pour group)');
            $table->enum('session_type', ['individual', 'group'])->default('individual')->comment('Type de session: individual = client choisit le jeu, group = créateur impose le jeu');
            
            // === PLATEFORME DE COMMUNICATION ===
            $table->string('meeting_platform')->default('zoom')->comment('Plateforme de réunion (zoom, discord, teams, google_meet, etc.) - voir MeetingPlatform enum');
            $table->string('meeting_link')->comment('Lien de réunion personnalisé du créateur');
            
            // === GESTION DES JEUX ===
            $table->string('game_title')->nullable()->comment('Jeu imposé (pour sessions de groupe)');
            $table->string('platform')->nullable()->comment('Plateforme imposée (Steam, PS5, etc.)');
            $table->boolean('allow_game_choice')->default(true)->comment('Le client peut-il choisir le jeu? true = individual, false = group');

            // === STATUT ET RELATIONS ===
            $table->boolean('is_active')->default(true)->comment('Événement activé/désactivé par le créateur');
            $table->foreignId('creator_id')->constrained('creators')->onDelete('cascade')->comment('Créateur propriétaire de cet événement');
            
            $table->timestamps();
            
            // === INDEX POUR PERFORMANCE ===
            $table->index(['creator_id', 'is_active'], 'creator_active_events_idx');
            $table->index(['session_type', 'is_active'], 'session_type_active_idx');
            $table->unique(['name', 'creator_id'], 'unique_event_name_per_creator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_types');
    }
};