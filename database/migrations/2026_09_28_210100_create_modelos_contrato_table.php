<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 6) Modelos de contrato da loja. A loja escreve o texto usando
 * variáveis (ex: {{cliente_nome}}, {{valor_total}}) que serão substituídas
 * pelos dados reais na hora de gerar um contrato.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('modelos_contrato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->string('titulo');
            $table->longText('conteudo'); // texto com variáveis {{ }}
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['loja_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelos_contrato');
    }
};
