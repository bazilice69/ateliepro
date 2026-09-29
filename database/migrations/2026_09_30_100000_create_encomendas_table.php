<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Encomendas / Sob Medida.
     *
     * Diferente da locação de peça de acervo (que já existe), aqui a estilista
     * CRIA a peça do ZERO em cima das medidas da cliente. Exige mais atenção:
     * medidas rígidas, croqui, tecidos, etapas de produção e prazo de entrega.
     * A peça pode ser COMPRADA (fica com a cliente) ou ALUGADA (exclusiva).
     */
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();

            // Multi-tenant: cada encomenda pertence a uma loja (trait BelongsToLoja).
            $table->foreignId('loja_id')->constrained('lojas')->onDelete('cascade');

            // A cliente dona da encomenda.
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');

            // Ficha de medidas usada nesta encomenda (opcional; reaproveita o
            // histórico de medidas da cliente que já existe no sistema).
            $table->foreignId('medida_id')->nullable()->constrained('medidas')->nullOnDelete();

            // Descrição da peça criada do zero.
            $table->string('titulo');                 // Ex: "Vestido de noiva sereia"
            $table->text('descricao')->nullable();     // Detalhes do modelo/pedido

            // A peça é COMPRA (fica com a cliente) ou ALUGUEL (exclusiva do ateliê)?
            $table->string('tipo')->default('compra'); // compra | aluguel

            // Datas-chave da ficha da estilista.
            $table->date('data_pedido')->nullable();
            $table->date('data_prova')->nullable();    // 1ª data de prova combinada
            $table->date('data_entrega')->nullable();  // prazo final (crítico!)

            // Financeiro.
            $table->decimal('valor', 10, 2)->default(0);   // valor total combinado
            $table->decimal('sinal', 10, 2)->default(0);   // entrada/adiantamento pago

            // Tecidos e materiais (campo livre da ficha).
            $table->text('tecidos')->nullable();

            // Croqui / desenho da criação (upload — disco public).
            $table->string('croqui_path')->nullable();

            // Etapa de produção (fluxo do zero até a entrega).
            // pedido -> modelagem -> corte -> costura -> prova -> ajustes ->
            // acabamento -> pronta -> entregue | cancelada
            $table->string('etapa')->default('pedido');

            $table->text('observacoes')->nullable();

            $table->timestamps();

            $table->index(['loja_id', 'etapa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
