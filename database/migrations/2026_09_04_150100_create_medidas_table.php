<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medidas', function (Blueprint $table) {
            $table->id();
            // Trava a medida ao cliente. Se apagar o cliente, apaga as medidas dele.
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            
            $table->date('data_medicao');
            $table->string('responsavel_medicao')->nullable();

            // Dimensões Gerais
            $table->string('altura')->nullable();
            $table->string('peso')->nullable();
            $table->string('manequim')->nullable();
            $table->string('calcado')->nullable();
            $table->string('salto')->nullable(); // Altura do salto para a barra

            // Medidas Superiores
            $table->string('busto_torax')->nullable(); // Serve para Busto (Fem) ou Tórax (Masc/Inf)
            $table->string('cintura')->nullable();
            $table->string('quadril')->nullable();
            $table->string('ombro')->nullable();
            $table->string('pescoco')->nullable(); // Para camisa social
            
            // Medidas de Membros e Comprimento
            $table->string('braco')->nullable();
            $table->string('manga')->nullable();
            $table->string('entrepernas')->nullable(); // Para calça masculina
            $table->string('comprimento_total')->nullable(); // Comprimento do vestido/paletó

            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medidas');
    }
};