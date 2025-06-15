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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Contact
            $table->string('phone', 20)->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            
            // Personnel
            $table->date('date_of_birth')->nullable();
            
            // Système (notifications TOUJOURS actives)
            $table->string('timezone', 50)->default('Europe/Paris');
            $table->string('language', 5)->default('fr');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_activity_at')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->unique('user_id');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
