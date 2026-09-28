<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('produtos', function (Blueprint $table) {
            // Medidas Femininas (Noivas/Debutantes)
            $table->decimal('med_busto', 5,2)->nullable();
            $table->decimal('med_alt_busto', 5,2)->nullable();
            $table->decimal('med_cintura', 5,2)->nullable();
            $table->decimal('med_alt_corpo', 5,2)->nullable();
            $table->decimal('med_quadril', 5,2)->nullable();
            $table->decimal('med_costas', 5,2)->nullable();
            $table->decimal('med_alt_saia', 5,2)->nullable();
            $table->decimal('med_alt_manga', 5,2)->nullable();
            
            // Medidas Masculinas (Ternos)
            $table->decimal('med_ombro', 5,2)->nullable();
            $table->decimal('med_torax', 5,2)->nullable();
            $table->decimal('med_colarinho', 5,2)->nullable();
            $table->decimal('med_comprimento_braco', 5,2)->nullable();
        });
    }
};