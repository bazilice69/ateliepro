<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 4) Período de teste grátis (trial) da loja.
 * Loja nova entra em trial de 7 dias; ao expirar sem assinatura paga, é
 * direcionada ao checkout.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            if (!Schema::hasColumn('lojas', 'trial_termina_em')) {
                $table->date('trial_termina_em')->nullable()->after('data_vencimento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->dropColumn('trial_termina_em');
        });
    }
};
