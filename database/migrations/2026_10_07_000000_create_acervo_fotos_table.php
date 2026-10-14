<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Galeria de fotos das peças do acervo (várias fotos por peça).
 * A foto de capa continua podendo ser referenciada na coluna `acervos.foto`
 * (para listagem/compatibilidade), mas aqui ficam todas as fotos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acervo_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->nullable()->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('acervo_id')->constrained('acervos')->cascadeOnDelete();
            $table->string('caminho');            // caminho no disco public
            $table->boolean('capa')->default(false);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acervo_fotos');
    }
};
