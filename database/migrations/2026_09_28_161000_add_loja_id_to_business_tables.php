<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 1B - Multi-tenancy) Adiciona `loja_id` a todas as tabelas de negócio.
 *
 * Este é o alicerce do isolamento por loja: cada registro passa a pertencer a
 * uma loja. O preenchimento e a filtragem automáticos ficam a cargo da trait
 * App\Models\Concerns\BelongsToLoja (Global Scope + evento creating).
 *
 * A coluna é `nullable` para não quebrar dados já existentes (que ainda não têm
 * loja). Novos registros sempre recebem `loja_id` via a trait.
 */
return new class extends Migration {
    /**
     * Tabelas de negócio que passam a ser isoladas por loja.
     */
    private array $tabelas = [
        'clientes',
        'acervos',
        'locacaos',
        'agendamentos',
        'eventos',
        'medidas',
        'categoria_financeiras',
        'lancamento_financeiros',
    ];

    public function up(): void
    {
        foreach ($this->tabelas as $tabela) {
            if (!Schema::hasTable($tabela) || Schema::hasColumn($tabela, 'loja_id')) {
                continue;
            }

            Schema::table($tabela, function (Blueprint $table) {
                $table->foreignId('loja_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('lojas')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tabelas as $tabela) {
            if (!Schema::hasTable($tabela) || !Schema::hasColumn($tabela, 'loja_id')) {
                continue;
            }

            Schema::table($tabela, function (Blueprint $table) {
                $table->dropConstrainedForeignId('loja_id');
            });
        }
    }
};
