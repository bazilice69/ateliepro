<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lancamento_financeiros', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['Receita', 'Despesa']); // Entrada ou Saída
            
            // Relacionamentos (Chaves Estrangeiras)
            $table->foreignId('categoria_id')->constrained('categoria_financeiras')->onDelete('restrict');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null'); // Pode ser nulo (ex: conta de luz não tem cliente)
            
            // Aqui futuramente vai o contrato_id e locacao_id, quando criarmos essas tabelas
            
            $table->string('descricao'); // Onde a dona vai escrever: "3m de Renda Guipir" ou "Conta de Luz Setembro"
            
            // Valores (Usamos decimal 10,2 para suportar até milhões com 2 casas de centavos)
            $table->decimal('valor_original', 10, 2);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('acrescimo', 10, 2)->default(0); // Para multas e juros
            $table->decimal('valor_final', 10, 2);
            
            // Datas
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            
            // Controle e Status
            $table->string('forma_pagamento')->nullable(); // Pix, Cartão, Dinheiro
            $table->enum('status', ['Pendente', 'Pago', 'Parcialmente Pago', 'Vencido', 'Cancelado'])->default('Pendente');
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lancamento_financeiros');
    }
};