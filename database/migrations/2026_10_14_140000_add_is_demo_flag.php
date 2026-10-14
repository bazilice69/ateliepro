<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Flag `is_demo` nas tabelas de negócio: marca registros criados pelo botão
 * "Ver com dados de exemplo", para que possam ser removidos depois sem afetar
 * os dados reais do lojista.
 */
return new class extends Migration
{
    private array $tabelas = [
        'clientes', 'acervos', 'eventos', 'locacaos', 'provas', 'servicos', 'lancamento_financeiros',
    ];

    public function up(): void
    {
        foreach ($this->tabelas as $tabela) {
            if (Schema::hasTable($tabela) && !Schema::hasColumn($tabela, 'is_demo')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->boolean('is_demo')->default(false)->index();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tabelas as $tabela) {
            if (Schema::hasTable($tabela) && Schema::hasColumn($tabela, 'is_demo')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->dropColumn('is_demo');
                });
            }
        }
    }
};
