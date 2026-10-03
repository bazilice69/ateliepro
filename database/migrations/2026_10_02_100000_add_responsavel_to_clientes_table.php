<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dados do RESPONSÁVEL do cliente (usados quando o cliente é Infantil).
 *
 * O formulário de cadastro já enviava `responsavel_nome` e
 * `responsavel_parentesco`, mas essas colunas não existiam — o que fazia o
 * cadastro quebrar (página em branco / erro SQL) ao salvar um cliente que
 * passasse esses campos. Esta migration cria as colunas.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'responsavel_nome')) {
                $table->string('responsavel_nome')->nullable();
            }
            if (! Schema::hasColumn('clientes', 'responsavel_parentesco')) {
                $table->string('responsavel_parentesco')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            foreach (['responsavel_nome', 'responsavel_parentesco'] as $coluna) {
                if (Schema::hasColumn('clientes', $coluna)) {
                    $table->dropColumn($coluna);
                }
            }
        });
    }
};
