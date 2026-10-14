<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blindagem: torna as colunas de custo do orçamento NULLABLE.
 *
 * A migration original definiu ->default(0), mas o Eloquent envia essas colunas
 * com valor null explícito (dependendo do modo de cálculo escolhido), o que
 * viola o NOT NULL e derruba o INSERT com SQLSTATE[23000]. Deixando-as nullable
 * (mantendo default 0), nenhum caminho de gravação volta a quebrar.
 *
 * Requer doctrine/dbal apenas em bancos que não suportam ALTER nativo; MySQL
 * 8 do ambiente aceita o change() direto do Laravel 11+.
 */
return new class extends Migration
{
    private array $colunas = [
        'custo_material', 'horas_trabalho', 'valor_hora', 'despesas_fixas',
        'custo_peca', 'custo_manutencao', 'margem_percent', 'taxa_cartao_percent',
        'custo_total', 'lucro_estimado', 'valor_final',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('orcamentos')) {
            return;
        }

        Schema::table('orcamentos', function (Blueprint $table) {
            foreach ($this->colunas as $coluna) {
                // Mantém a mesma precisão decimal, agora nullable com default 0.
                $table->decimal($coluna, 10, 2)->nullable()->default(0)->change();
            }
        });
    }

    public function down(): void
    {
        // Sem reversão destrutiva: manter nullable é seguro.
    }
};
