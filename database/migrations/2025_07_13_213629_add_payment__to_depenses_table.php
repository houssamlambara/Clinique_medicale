<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->decimal('montant', 10, 2)->change();
            $table->boolean('est_paye')->default(false)->after('montant');
            $table->timestamp('date_paiement')->nullable()->after('est_paye');
        });
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropColumn(['est_paye', 'date_paiement']);
            $table->decimal('montant')->change();
        });
    }
};
