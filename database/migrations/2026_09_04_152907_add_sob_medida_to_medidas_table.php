<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medidas', function (Blueprint $table) {
            // Tronco e Busto
            $table->string('alto_busto')->nullable();
            $table->string('torax_medida')->nullable(); // renomeado para não conflitar com o torax padrão
            $table->string('altura_corpo')->nullable();
            $table->string('centro_frente')->nullable();
            $table->string('centro_costas')->nullable();
            $table->string('altura_busto')->nullable();
            $table->string('distancia_busto')->nullable();
            $table->string('cava_a_cava')->nullable();
            $table->string('ombro_a_ombro')->nullable();
            
            // Braços
            $table->string('circ_cava')->nullable();
            $table->string('cotovelo_dobrado')->nullable();
            $table->string('altura_cotovelo')->nullable();
            $table->string('punho')->nullable();
            $table->string('mao')->nullable();
            $table->string('cabeca_manga')->nullable();

            // Quadril e Comprimentos
            $table->string('altura_quadril')->nullable();
            $table->string('quadril_alto')->nullable();
            $table->string('quadril_baixo')->nullable();
            $table->string('altura_q_alto')->nullable();
            $table->string('altura_q_baixo')->nullable();
            $table->string('comp_saia')->nullable();
            $table->string('comp_cauda')->nullable();
            $table->string('decotes')->nullable();
            $table->string('diagonais')->nullable();
            $table->string('lateral')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('medidas', function (Blueprint $table) {
            // Reverte as colunas caso você precise desfazer a migration
            $table->dropColumn([
                'alto_busto', 'torax_medida', 'altura_corpo', 'centro_frente', 'centro_costas', 
                'altura_busto', 'distancia_busto', 'cava_a_cava', 'ombro_a_ombro', 'circ_cava', 
                'cotovelo_dobrado', 'altura_cotovelo', 'punho', 'mao', 'cabeca_manga', 
                'altura_quadril', 'quadril_alto', 'quadril_baixo', 'altura_q_alto', 
                'altura_q_baixo', 'comp_saia', 'comp_cauda', 'decotes', 'diagonais', 'lateral'
            ]);
        });
    }
};