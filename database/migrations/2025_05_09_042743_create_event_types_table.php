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
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('default_duration')->default(60)->comment('Duration in minutes');
            $table->decimal('default_price', 10, 2)->nullable();
            $table->integer('default_max_participants')->nullable();
            $table->string('meeting_platform')->default('zoom')->comment('Plateforme de réunion (zoom, teams, google_meet, etc.)');
            $table->string('meeting_link')->comment('Lien de réunion personnalisé');

            // Ajouter SEULEMENT à ta table event_types existante :
            $table->enum('session_type', ['individual', 'group'])->default('individual')->after('default_max_participants');


            $table->boolean('is_active')->default(true);
            $table->foreignId('creator_id')->constrained('creators')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['name', 'creator_id']);
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