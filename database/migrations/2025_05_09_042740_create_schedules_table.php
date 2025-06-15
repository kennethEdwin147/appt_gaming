<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cette migration crée la table 'schedules' (horaires) qui appartient à un créateur
     */
    public function up(): void
    {
        // Création de la table des horaires (schedules)
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // Un horaire appartient à un créateur
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->comment('Nom de l\'horaire (ex: "Horaires de travail", "Horaires de consultation")');
            $table->date('effective_from')->nullable()->comment('Date de début de validité de l\'horaire');
            $table->date('effective_until')->nullable()->comment('Date de fin de validité de l\'horaire');
            $table->timestamps();

            // Un créateur ne peut pas avoir deux horaires avec le même nom
            $table->unique(['creator_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression de la table des horaires
        Schema::dropIfExists('schedules');
    }
};
