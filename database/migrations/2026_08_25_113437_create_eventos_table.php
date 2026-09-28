<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 0 - correção) Migration de `eventos` DUPLICADA.
 *
 * A tabela `eventos` passou a ser criada pela migration 110057 (que antes criava
 * `agendamentos` por engano). Este arquivo era uma segunda criação da mesma
 * tabela e agora está neutralizado com um guard `hasTable` para não quebrar o
 * `migrate:fresh`. Mantido no histórico para preservar a linha de migrations já
 * aplicada em ambientes existentes.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            return;
        }

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_evento');
            $table->date('data_evento');
            $table->string('tipo_evento');
            $table->string('status')->default('Ativo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Não faz nada: a tabela é gerenciada pela migration 110057.
    }
};
