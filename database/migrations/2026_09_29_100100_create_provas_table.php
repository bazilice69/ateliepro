<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 10) Provas do cliente. Cada prova registra o resultado, os ajustes
 * necessários, a próxima prova e fotos. Liga-se ao cliente, à peça (acervo) e,
 * opcionalmente, à locação.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('provas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('acervo_id')->nullable()->constrained('acervos')->nullOnDelete();
            $table->foreignId('locacao_id')->nullable()->constrained('locacaos')->nullOnDelete();

            $table->dateTime('data_prova');
            $table->string('responsavel')->nullable();     // quem atendeu
            // agendada | realizada | remarcada | cancelada
            $table->string('status')->default('agendada');
            $table->string('resultado')->nullable();        // aprovada | precisa_ajuste
            $table->text('ajustes_necessarios')->nullable(); // o que precisa ajustar
            $table->date('proxima_prova')->nullable();
            $table->text('observacoes')->nullable();
            $table->text('fotos')->nullable();              // JSON com caminhos
            $table->timestamps();

            $table->index(['loja_id', 'data_prova']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provas');
    }
};
