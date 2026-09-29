<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 5) Despesas recorrentes/fixas da loja (internet, luz, água, aluguel,
 * mão de obra...). Todo mês o sistema pode gerar automaticamente um lançamento
 * financeiro a partir de cada despesa recorrente ativa.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('despesas_recorrentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categoria_financeiras')->nullOnDelete();
            $table->string('descricao');                 // Ex: Internet, Aluguel
            $table->decimal('valor', 10, 2);
            $table->unsignedTinyInteger('dia_vencimento')->default(5); // dia do mês
            $table->boolean('ativo')->default(true);
            $table->date('ultimo_lancamento_em')->nullable(); // controle de idempotência mensal
            $table->timestamps();

            $table->index(['loja_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesas_recorrentes');
    }
};
