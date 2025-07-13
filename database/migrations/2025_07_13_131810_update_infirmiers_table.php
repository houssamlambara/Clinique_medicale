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
        Schema::table('infirmiers', function (Blueprint $table) {
            // Supprimer les anciennes colonnes
            $table->dropColumn(['nom', 'prenom', 'email', 'password']);
            
            // Ajouter les nouvelles colonnes comme dans medecins
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialite');
            $table->string('numero_licence')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infirmiers', function (Blueprint $table) {
            // Supprimer les nouvelles colonnes
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'specialite', 'numero_licence']);
            
            // Restaurer les anciennes colonnes
            $table->string('nom');
            $table->string('prenom');
            $table->string('email');
            $table->string('password');
        });
    }
};
