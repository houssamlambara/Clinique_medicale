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
        Schema::table('rendezvous', function (Blueprint $table) {
            $table->time('heure_rdv')->nullable()->after('date_rdv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendezvous', function (Blueprint $table) {
            $table->dropColumn('heure_rdv');
        });
    }
};
