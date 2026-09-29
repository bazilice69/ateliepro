<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Torna a medida "viva" dentro da encomenda.
     *
     * Cada prova de uma peça sob medida gera uma NOVA medição (nunca sobrescreve),
     * ficando no histórico para comparar a evolução. Para isso a medida passa a
     * poder pertencer a uma encomenda e ganha um rótulo (ex: "Prova 1").
     *
     * `loja_id` também é adicionado aqui porque o Model Medida usa a trait
     * BelongsToLoja (multi-tenant) e a tabela original não tinha essa coluna.
     */
    public function up(): void
    {
        Schema::table('medidas', function (Blueprint $table) {
            if (! Schema::hasColumn('medidas', 'loja_id')) {
                $table->foreignId('loja_id')->nullable()->after('id')
                    ->constrained('lojas')->nullOnDelete();
            }

            // A qual encomenda esta medição pertence (opcional: medidas gerais da
            // cliente continuam sem encomenda).
            $table->foreignId('encomenda_id')->nullable()->after('cliente_id')
                ->constrained('encomendas')->nullOnDelete();

            // Rótulo da medição no fluxo da encomenda: ex. "Prova 1", "Ajuste".
            $table->string('rotulo')->nullable()->after('responsavel_medicao');
        });

        // Backfill do loja_id nas medidas antigas, herdando da cliente dona.
        if (Schema::hasColumn('medidas', 'loja_id')) {
            \Illuminate\Support\Facades\DB::statement(
                'UPDATE medidas SET loja_id = (SELECT clientes.loja_id FROM clientes WHERE clientes.id = medidas.cliente_id) WHERE loja_id IS NULL'
            );
        }
    }

    public function down(): void
    {
        Schema::table('medidas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('encomenda_id');
            $table->dropColumn('rotulo');
            // loja_id é mantido para não quebrar a trait BelongsToLoja.
        });
    }
};
