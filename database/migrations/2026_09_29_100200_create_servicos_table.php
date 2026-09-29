<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 10) Serviços internos sobre uma peça: ajuste/costura, lavanderia e
 * manutenção. Cada serviço tem um tipo, itens (ex.: barra, cintura, alça),
 * responsável (costureira), prazo, custo e status. É a "fila da oficina".
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('acervo_id')->nullable()->constrained('acervos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('locacao_id')->nullable()->constrained('locacaos')->nullOnDelete();

            // ajuste | lavanderia | manutencao
            $table->string('tipo')->default('ajuste');
            $table->string('descricao');                 // resumo do serviço
            $table->text('itens')->nullable();           // JSON: ["barra","cintura",...]
            $table->string('responsavel')->nullable();   // costureira / responsável
            $table->date('prazo')->nullable();
            $table->decimal('custo', 10, 2)->default(0);
            // solicitado | em_andamento | pronto | aprovado | cancelado
            $table->string('status')->default('solicitado');
            $table->text('observacoes')->nullable();
            $table->text('fotos')->nullable();           // JSON
            $table->timestamps();

            $table->index(['loja_id', 'status']);
            $table->index(['loja_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
