<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            // 1. Dados Pessoais
            $table->string('nome');
            $table->string('cpf')->nullable()->unique();
            $table->string('telefone');
            $table->date('data_nascimento')->nullable();
            
            // 2. Perfil
            $table->enum('tipo', ['Feminino', 'Masculino', 'Infantil'])->default('Feminino');
            
            // 3. Endereço
            $table->string('cep')->nullable();
            $table->string('rua')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable(); // SP, RJ, etc.
            
            // 4. Observações e Preferências
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};