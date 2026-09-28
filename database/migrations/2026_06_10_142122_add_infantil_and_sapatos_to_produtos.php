<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('produtos', function (Blueprint $table) {
            // Medidas Infantil
            $table->string('idade_sugerida')->nullable(); // Ex: 4 a 6 anos
            $table->decimal('med_torax_infantil', 5, 2)->nullable();
            $table->decimal('med_cintura_infantil', 5, 2)->nullable();
            $table->decimal('med_comprimento_total', 5, 2)->nullable(); // Comprimento do vestido/terninho infantil

            // Sapatos / Calçados
            $table->integer('numeracao_calcado')->nullable(); // Ex: 36, 40
            $table->string('tipo_salto')->nullable(); // Ex: Salto Alto, Bloco, Sem Salto
        });
    }
};