<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            
            // Relacionamentos Inteligentes
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->onDelete('set null');
            $table->foreignId('produto_id')->nullable()->constrained('produtos')->onDelete('set null');
            
            // Dados do Agendamento
            $table->dateTime('data_hora');
            $table->integer('duracao_minutos')->default(60);
            
            // Tipos e Status (Baseado no seu Checklist)
            $table->string('tipo_atendimento'); // Primeiro Atendimento, Prova 1, Retirada, Devolução
            $table->string('status_cor')->default('blue'); // blue=Agendado, green=Confirmado, yellow=Aguardando Ajuste
            $table->string('sala_atendimento')->nullable(); // Sala 1 - Noivas, Sala 2 - Festas
            
            $table->text('observacoes_checklist')->nullable(); // Para salvar o "Cliente chegou, Vestido separado..."
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agendamentos');
    }
};