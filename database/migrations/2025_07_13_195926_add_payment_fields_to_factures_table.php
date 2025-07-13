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
        Schema::table('factures', function (Blueprint $table) {
            // Modifier la précision du champ montant
            $table->decimal('montant', 10, 2)->change();
            
            // Ajouter les champs de paiement
            $table->boolean('est_paye')->default(false)->after('montant');
            $table->timestamp('date_paiement')->nullable()->after('est_paye');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            // Supprimer les champs de paiement
            $table->dropColumn(['est_paye', 'date_paiement']);
            
            // Remettre la précision originale du montant
            $table->decimal('montant')->change();
        });
    }
};
