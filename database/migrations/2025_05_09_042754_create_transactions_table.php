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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->unique()->constrained()->onDelete('cascade');
            $table->string('payment_provider')->nullable()->comment('Fournisseur de paiement (stripe, paypal, manual)');
            $table->string('payment_id')->nullable()->comment('ID de transaction du fournisseur');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 10)->default('CAD');
            $table->string('status', 50);
            $table->json('payment_details')->nullable()->comment('Détails supplémentaires du paiement');
            $table->decimal('platform_commission_rate', 5, 2)->nullable()->comment('Taux de commission de la plateforme appliqué à cette transaction');
            $table->decimal('platform_commission_amount', 8, 2)->nullable()->comment('Montant de la commission de la plateforme sur cette transaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};