<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 6) Contratos gerados.
 *
 * IMPORTANTE: o campo `conteudo_final` guarda o texto JÁ PREENCHIDO no momento
 * da geração (congelado). Se a loja editar o modelo depois, os contratos
 * antigos NÃO mudam — preservam o que foi acordado com o cliente.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('locacao_id')->nullable()->constrained('locacaos')->nullOnDelete();
            $table->foreignId('modelo_contrato_id')->nullable()->constrained('modelos_contrato')->nullOnDelete();
            $table->string('titulo');
            $table->longText('conteudo_final'); // conteúdo congelado (já preenchido)
            $table->enum('status', ['rascunho', 'finalizado', 'assinado', 'cancelado'])->default('rascunho');
            $table->date('data_contrato')->nullable();
            $table->timestamps();

            $table->index(['loja_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
