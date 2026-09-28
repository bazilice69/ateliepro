<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_evento'); // Ex: Casamento Ana & João
            $table->date('data_evento');
            $table->string('tipo_evento'); // Casamento, 15 Anos, Formatura
            $table->string('status')->default('Ativo'); // Ativo, Concluído, Cancelado
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('eventos');
    }
};