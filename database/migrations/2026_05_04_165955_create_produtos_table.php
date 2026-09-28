<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno')->unique(); // Ex: VEST-001
            $table->string('nome');
            $table->string('categoria'); // Vestido de Noiva, Festa, Debutante, etc.
            $table->string('tamanho');
            $table->string('cor');
            $table->decimal('valor_locacao', 10, 2);
            $table->integer('quantidade_estoque')->default(1);
            $table->text('descricao')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('produtos');
    }
};