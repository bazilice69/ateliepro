<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Depoimentos / elogios enviados pelos LOJISTAS.
 *
 * Fluxo: o lojista manda um "Elogio" pelo widget de feedback -> fica PENDENTE
 * (aprovado = false) -> o super_admin modera -> uma vez aprovado, aparece na
 * landing page (tela antes do login), como prova social. Opcionalmente leva o
 * LINK da loja dele junto, para divulgá-lo de volta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depoimentos', function (Blueprint $table) {
            $table->id();
            // Loja de origem (para rastreio/contato). Pode ser null se a loja for
            // removida depois — o depoimento público continua valendo.
            $table->foreignId('loja_id')->nullable()->constrained('lojas')->nullOnDelete();
            $table->string('autor_nome', 120);          // quem escreveu (ex.: "Andreia")
            $table->string('loja_nome', 120)->nullable(); // nome da loja exibido na vitrine
            $table->text('mensagem');                     // o elogio em si
            $table->string('link_loja', 255)->nullable(); // site/Instagram da loja (divulgação)
            $table->boolean('aprovado')->default(false);  // moderação do super_admin
            $table->timestamps();

            $table->index('aprovado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depoimentos');
    }
};
