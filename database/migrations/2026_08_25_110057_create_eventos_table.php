<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cria a tabela `eventos`.
 *
 * NOTA (Fase 0 - correção): o conteúdo original deste arquivo criava por engano
 * a tabela `agendamentos` (com nome de arquivo "create_eventos_table"),
 * referenciando as tabelas `eventos` e `produtos` antes delas existirem, o que
 * quebrava o `migrate:fresh`. Como este arquivo roda ANTES da migration de
 * agendamentos (110108), ele agora cria de fato a tabela `eventos`, garantindo
 * que a FK `evento_id` em `agendamentos` encontre a tabela já criada.
 * A migration 113437 (que também criava `eventos`) foi neutralizada para não
 * duplicar a criação.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('eventos')) {
            return;
        }

        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_evento'); // Ex: Casamento Ana & João
            $table->date('data_evento');
            $table->string('tipo_evento'); // Casamento, 15 Anos, Formatura
            $table->string('status')->default('Ativo'); // Ativo, Concluído, Cancelado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
