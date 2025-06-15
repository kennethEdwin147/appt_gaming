<?php

/*
 * TIME SLOTS - Créneaux horaires concrets bookables
 * 
 * DIFFÉRENCE CLÉE :
 * - availabilities = Template récurrent ("Tous les mardis 19h-23h")
 * - time_slots = Créneaux concrets générés ("Mardi 17 juin 19h00-19h30")
 * 
 * EXEMPLE CONCRET :
 * Gaming Creator "ProGamer" a une availability :
 * - day_of_week: 'tuesday'
 * - start_time: '19:00' 
 * - end_time: '23:00'
 * 
 * Cette availability génère automatiquement ces time_slots :
 * - 2025-06-17 19:00:00 → 2025-06-17 19:30:00 (status: available)
 * - 2025-06-17 19:30:00 → 2025-06-17 20:00:00 (status: available) 
 * - 2025-06-17 20:00:00 → 2025-06-17 20:30:00 (status: booked)   ← Client réserve
 * - 2025-06-17 20:30:00 → 2025-06-17 21:00:00 (status: booked)   ← Suite de la session 1h
 * - 2025-06-17 21:00:00 → 2025-06-17 21:30:00 (status: available)
 * - etc. jusqu'à 23h
 * 
 * PUIS la semaine suivante :
 * - 2025-06-24 19:00:00 → 2025-06-24 19:30:00 (status: available)
 * - etc.
 * 
 * AVANTAGES :
 * ✅ Performance - Pas de calcul en temps réel côté client
 * ✅ Flexibilité - Chaque slot peut être modifié individuellement 
 * ✅ Statuts individuels - available/booked/blocked par slot
 * ✅ UX simple - Client clique sur slot = réservation directe
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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->onDelete('cascade');
            
            // Créneau horaire concret
            $table->datetime('start_time')->comment('Début du créneau (ex: 2025-06-17 19:00:00)');
            $table->datetime('end_time')->comment('Fin du créneau (ex: 2025-06-17 19:30:00)');
            $table->string('timezone')->default('America/Toronto')->comment('Fuseau horaire du créateur');
            
            // Statut du créneau
            $table->enum('status', [
                'available',  // Libre pour réservation
                'booked',     // Réservé par un client
                'blocked',    // Bloqué manuellement par le créateur
                'past'        // Créneau passé (nettoyage automatique)
            ])->default('available');
            
            // Métadonnées de génération
            $table->date('generated_for_date')->comment('Pour quel jour ce slot a été généré (2025-06-17)');
            $table->boolean('is_recurring_slot')->default(true)->comment('Généré automatiquement vs créé manuellement');
            
            // Prix personnalisé optionnel (override du event_type)
            $table->decimal('custom_price', 8, 2)->nullable()->comment('Prix spécial pour ce créneau si différent du tarif normal');
            
            // Notes du créateur
            $table->text('creator_notes')->nullable()->comment('Notes privées du créateur sur ce créneau');
            
            $table->timestamps();
            
            // Index pour performance
            $table->index(['creator_id', 'start_time', 'status'], 'creator_time_status_idx');
            $table->index(['status', 'start_time'], 'status_time_idx');
            $table->index(['generated_for_date', 'creator_id'], 'date_creator_idx');
            
            // Contrainte : un créateur ne peut pas avoir 2 slots qui se chevauchent
            $table->unique(['creator_id', 'start_time'], 'unique_creator_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};