<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rastreio de ORIGEM dos lançamentos financeiros.
 *
 * Permite que o sistema gere lançamentos automaticamente a partir de operações
 * (locações, encomendas, etc.) e, principalmente, EVITE DUPLICAÇÃO: cada
 * lançamento automático é identificado por (origem, origem_id, origem_item) e
 * atualizado em vez de recriado quando a operação muda.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lancamento_financeiros', function (Blueprint $table) {
            // Ex.: 'locacao', 'encomenda'. Null = lançamento manual (digitado).
            $table->string('origem', 30)->nullable()->after('categoria_id');
            // Id do registro de origem (ex.: id da locação).
            $table->unsignedBigInteger('origem_id')->nullable()->after('origem');
            // Qual "parte" da origem (ex.: 'sinal', 'saldo', 'multa', 'caucao').
            $table->string('origem_item', 30)->nullable()->after('origem_id');

            $table->index(['origem', 'origem_id', 'origem_item'], 'lanc_origem_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lancamento_financeiros', function (Blueprint $table) {
            $table->dropIndex('lanc_origem_idx');
            $table->dropColumn(['origem', 'origem_id', 'origem_item']);
        });
    }
};
