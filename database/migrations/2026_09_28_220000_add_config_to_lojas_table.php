<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 8) Configurações/identidade visual da loja: logo, razão social,
 * inscrição estadual e endereço da loja. O logo aparece no topo do sistema e
 * nos contratos, dando a "cara" da loja (personalização = uau de venda).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            foreach ([
                'logo' => fn () => $table->string('logo')->nullable(),
                'razao_social' => fn () => $table->string('razao_social')->nullable(),
                'inscricao_estadual' => fn () => $table->string('inscricao_estadual')->nullable(),
                'endereco' => fn () => $table->string('endereco')->nullable(),
                'cidade' => fn () => $table->string('cidade')->nullable(),
                'estado' => fn () => $table->string('estado', 2)->nullable(),
                'cep' => fn () => $table->string('cep')->nullable(),
            ] as $coluna => $criar) {
                if (!Schema::hasColumn('lojas', $coluna)) {
                    $criar();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->dropColumn(['logo', 'razao_social', 'inscricao_estadual', 'endereco', 'cidade', 'estado', 'cep']);
        });
    }
};
