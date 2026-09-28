<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_financeiras', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: "Materiais/Aviamentos", "Água e Luz", "Locação"
            $table->enum('tipo', ['Receita', 'Despesa']); // Trava se é entrada ou saída
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_financeiras');
    }
};