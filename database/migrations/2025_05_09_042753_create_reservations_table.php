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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('creator_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('availability_id')->nullable()->constrained()->onDelete('set null')->comment('Référence à la disponibilité utilisée pour cette réservation');
            $table->string('guest_first_name')->nullable();
            $table->string('guest_last_name')->nullable();
            $table->dateTime('reserved_datetime');
            $table->string('timezone')->nullable()->comment('Fuseau horaire de l\'utilisateur qui a fait la réservation');
            $table->string('meeting_link')->nullable()->comment('Lien de réunion spécifique à cette réservation');
            $table->timestamp('reservation_time')->useCurrent();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'rescheduled', 'completed'])->default('pending')->comment('Statut de la réservation indépendamment du paiement');
            
            
            $table->foreignId('time_slot_id')->constrained()->after('event_type_id');
            $table->decimal('price_paid', 8, 2)->after('timezone');
            $table->text('special_requests')->nullable()->after('price_paid');
            $table->integer('participants_count')->default(1)->after('special_requests');

            $table->string('payment_status')->default('pending');
            $table->string('payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};