<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acervos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Ex: VN-001
            $table->string('nome'); // Nome ou modelo do vestido/terno
            $table->string('categoria'); // Noiva, Debutante, Festa, Madrinha, Terno...
            $table->string('tamanho')->nullable(); // P, M, G, 42, 44...
            $table->string('cor')->nullable();
            $table->decimal('valor_locacao', 10, 2); // Preço do aluguel
            
            // Status baseado no seu checklist
            $table->enum('status', [
                'disponivel', 
                'reservada', 
                'em_prova', 
                'em_ajuste', 
                'alugada', 
                'lavanderia', 
                'manutencao', 
                'indisponivel'
            ])->default('disponivel');

            $table->text('observacoes')->nullable();
            $table->timestamps(); // Cria as colunas created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acervos');
    }
};