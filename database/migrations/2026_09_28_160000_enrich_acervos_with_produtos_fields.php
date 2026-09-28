<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 1 - PR-A) Unificação do estoque em `acervos`.
 *
 * O projeto tinha DUAS tabelas de estoque para a mesma coisa: `produtos` (usada
 * só pela Agenda) e `acervos` (usada pelas Locações). Decidimos padronizar em
 * `acervos`. Esta migration leva para `acervos` os campos úteis que só existiam
 * em `produtos` (venda, foto, estoque, público e as medidas técnicas), para que
 * `acervos` passe a ser a única fonte de verdade do acervo/estoque.
 *
 * A tabela `produtos` NÃO é apagada aqui (para não arriscar dados existentes);
 * ela apenas deixa de ser usada pela aplicação.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('acervos', function (Blueprint $table) {
            // Comercial / catálogo
            if (!Schema::hasColumn('acervos', 'valor_venda')) {
                $table->decimal('valor_venda', 10, 2)->nullable()->after('valor_locacao');
            }
            if (!Schema::hasColumn('acervos', 'caucao')) {
                $table->decimal('caucao', 10, 2)->nullable()->after('valor_venda');
            }
            if (!Schema::hasColumn('acervos', 'quantidade_estoque')) {
                $table->integer('quantidade_estoque')->default(1)->after('caucao');
            }
            if (!Schema::hasColumn('acervos', 'publico')) {
                $table->string('publico')->default('feminino')->after('categoria'); // feminino, masculino, infantil
            }
            if (!Schema::hasColumn('acervos', 'foto')) {
                $table->string('foto')->nullable();
            }
            if (!Schema::hasColumn('acervos', 'localizacao')) {
                $table->string('localizacao')->nullable(); // Arara/prateleira física
            }

            // Medidas Femininas (Noivas/Debutantes)
            foreach ([
                'med_busto', 'med_alt_busto', 'med_cintura', 'med_alt_corpo',
                'med_quadril', 'med_costas', 'med_alt_saia', 'med_alt_manga',
                // Medidas Masculinas (Ternos)
                'med_ombro', 'med_torax', 'med_colarinho', 'med_comprimento_braco',
                // Infantil
                'med_torax_infantil', 'med_cintura_infantil', 'med_comprimento_total',
            ] as $coluna) {
                if (!Schema::hasColumn('acervos', $coluna)) {
                    $table->decimal($coluna, 5, 2)->nullable();
                }
            }

            // Infantil / Sapatos
            if (!Schema::hasColumn('acervos', 'idade_sugerida')) {
                $table->string('idade_sugerida')->nullable();
            }
            if (!Schema::hasColumn('acervos', 'numeracao_calcado')) {
                $table->integer('numeracao_calcado')->nullable();
            }
            if (!Schema::hasColumn('acervos', 'tipo_salto')) {
                $table->string('tipo_salto')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('acervos', function (Blueprint $table) {
            $table->dropColumn([
                'valor_venda', 'caucao', 'quantidade_estoque', 'publico', 'foto', 'localizacao',
                'med_busto', 'med_alt_busto', 'med_cintura', 'med_alt_corpo',
                'med_quadril', 'med_costas', 'med_alt_saia', 'med_alt_manga',
                'med_ombro', 'med_torax', 'med_colarinho', 'med_comprimento_braco',
                'med_torax_infantil', 'med_cintura_infantil', 'med_comprimento_total',
                'idade_sugerida', 'numeracao_calcado', 'tipo_salto',
            ]);
        });
    }
};
