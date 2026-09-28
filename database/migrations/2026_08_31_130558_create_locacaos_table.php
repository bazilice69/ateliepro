<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locacaos', function (Blueprint $table) {
            $table->id();
            
            // Relacionamentos: Quem alugou e Qual é a peça
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->foreignId('acervo_id')->constrained('acervos')->onDelete('restrict');

            // A Regra de Ouro (Datas e Período de Indisponibilidade)
            $table->date('data_retirada');
            $table->date('data_evento');
            $table->date('data_devolucao');
            $table->date('data_liberacao_prevista')->nullable(); // Data de devolução + lavanderia

            // Financeiro básico
            $table->decimal('valor_total', 10, 2);
            $table->decimal('sinal_pago', 10, 2)->default(0);

            // Fluxo da locação do seu checklist
            $table->enum('status', [
                'orcamento',
                'reservada',
                'contrato_assinado',
                'em_provas',
                'ajustes',
                'pronta_para_retirada',
                'retirada', 
                'aguardando_avaliacao', 
                'cancelada'
            ])->default('reservada');

            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locacaos');
    }
};