<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 0 - correção) O conteúdo original deste arquivo era o TEXTO de um
 * tutorial (instruções em português + bloco markdown) em vez de PHP válido, o
 * que causava parse error e impedia o `php artisan migrate` de rodar. Abaixo
 * está o código PHP correto que estava embutido no bloco de exemplo, com guards
 * `hasColumn` para ser idempotente.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Campos de Controle e Venda (caso não existam)
            if (!Schema::hasColumn('produtos', 'valor_venda')) {
                $table->decimal('valor_venda', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('produtos', 'status')) {
                $table->integer('status')->default(1); // 1=Disponível, 2=Locado, 3=Manutenção, 4=Vendido
            }

            // Campos do Setor Infantil
            if (!Schema::hasColumn('produtos', 'idade_sugerida')) {
                $table->string('idade_sugerida')->nullable();
            }
            if (!Schema::hasColumn('produtos', 'med_torax_infantil')) {
                $table->decimal('med_torax_infantil', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('produtos', 'med_cintura_infantil')) {
                $table->decimal('med_cintura_infantil', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('produtos', 'med_comprimento_total')) {
                $table->decimal('med_comprimento_total', 5, 2)->nullable();
            }

            // Campos do Setor de Sapatos
            if (!Schema::hasColumn('produtos', 'numeracao_calcado')) {
                $table->integer('numeracao_calcado')->nullable();
            }
            if (!Schema::hasColumn('produtos', 'tipo_salto')) {
                $table->string('tipo_salto')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn([
                'valor_venda', 'status', 'idade_sugerida',
                'med_torax_infantil', 'med_cintura_infantil',
                'med_comprimento_total', 'numeracao_calcado', 'tipo_salto',
            ]);
        });
    }
};
