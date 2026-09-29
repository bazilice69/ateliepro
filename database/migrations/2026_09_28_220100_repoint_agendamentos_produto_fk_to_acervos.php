<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 8 - correção) Repointa a FK de `agendamentos.produto_id`.
 *
 * Na Fase 1A o estoque foi unificado em `acervos`, mas a chave estrangeira de
 * `agendamentos.produto_id` continuava apontando para a tabela `produtos` (que
 * ficou vazia). Isso quebrava inserções de agendamentos vinculados a peças do
 * acervo. Aqui a FK passa a referenciar `acervos`.
 *
 * SQLite recria a constraint ao usar os métodos do Schema builder do Laravel.
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('agendamentos') || !Schema::hasColumn('agendamentos', 'produto_id')) {
            return;
        }

        Schema::table('agendamentos', function (Blueprint $table) {
            // Remove a FK antiga (para 'produtos'). Em SQLite, o Laravel recria
            // a tabela preservando os dados.
            try {
                $table->dropForeign(['produto_id']);
            } catch (\Throwable $e) {
                // Se a FK não existir com esse nome, seguimos.
            }
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreign('produto_id')
                ->references('id')->on('acervos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('agendamentos') || !Schema::hasColumn('agendamentos', 'produto_id')) {
            return;
        }

        Schema::table('agendamentos', function (Blueprint $table) {
            try {
                $table->dropForeign(['produto_id']);
            } catch (\Throwable $e) {
            }
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreign('produto_id')
                ->references('id')->on('produtos')
                ->nullOnDelete();
        });
    }
};
