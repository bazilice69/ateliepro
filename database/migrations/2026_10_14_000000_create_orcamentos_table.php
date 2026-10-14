<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Orçamentos gerados pela calculadora de preço.
 *
 * Guarda os CUSTOS INTERNOS (material, mão de obra, margem...) só para a
 * lojista acompanhar — esses dados NUNCA aparecem na impressão para o cliente,
 * que vê apenas o valor_final. Ao ser aceito, o orçamento vira um contrato.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->nullable()->constrained('lojas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('acervo_id')->nullable()->constrained('acervos')->nullOnDelete();

            $table->string('modo')->default('sob_medida'); // sob_medida | aluguel | ajuste
            $table->string('descricao')->nullable();        // o que será feito/alugado

            // Valor que o CLIENTE vê (único que aparece na impressão).
            $table->decimal('valor_final', 10, 2)->default(0);

            // Custos internos (só da lojista — nunca na impressão do cliente).
            $table->decimal('custo_material', 10, 2)->default(0);
            $table->decimal('horas_trabalho', 8, 2)->default(0);
            $table->decimal('valor_hora', 10, 2)->default(0);
            $table->decimal('despesas_fixas', 10, 2)->default(0);
            $table->decimal('custo_peca', 10, 2)->default(0);
            $table->integer('num_alugueis')->nullable();
            $table->decimal('custo_manutencao', 10, 2)->default(0);
            $table->decimal('margem_percent', 6, 2)->default(0);
            $table->decimal('taxa_cartao_percent', 6, 2)->default(0);
            $table->decimal('custo_total', 10, 2)->default(0);
            $table->decimal('lucro_estimado', 10, 2)->default(0);

            // rascunho | enviado | aceito | recusado
            $table->string('status')->default('rascunho');
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
