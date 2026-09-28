<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 2.5) Tabela de PLANOS do SaaS, gerenciada pelo super_admin.
 * Os preços deixam de ser fixos no HTML: passam a ser controlados pelo painel
 * e exibidos dinamicamente na landing page e no checkout.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                       // Ex: Mensal, Profissional
            $table->string('slug')->unique();             // Ex: mensal, profissional
            $table->decimal('preco', 10, 2);              // Preço do período
            $table->unsignedInteger('meses')->default(1); // Duração em meses (1, 3, 6, 12)
            $table->string('periodo_label')->nullable();  // Ex: /mês, /tri, /sem, /ano
            $table->json('recursos')->nullable();         // Lista de bullets exibidos no card
            $table->boolean('destaque')->default(false);  // "Mais Popular"
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
