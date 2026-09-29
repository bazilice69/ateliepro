<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 2) Campos comerciais da loja, controlados pelo super_admin:
 * plano, valor mensal, desconto e vencimento da assinatura.
 *
 * A `assinaturas` continua guardando o histórico de transações; estes campos em
 * `lojas` são o "estado atual" (plano/valor vigente) para leitura rápida no
 * painel do super_admin.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            if (!Schema::hasColumn('lojas', 'plano')) {
                $table->string('plano')->default('Essencial')->after('telefone');
            }
            if (!Schema::hasColumn('lojas', 'valor_mensal')) {
                $table->decimal('valor_mensal', 10, 2)->default(0)->after('plano');
            }
            if (!Schema::hasColumn('lojas', 'desconto')) {
                $table->decimal('desconto', 10, 2)->default(0)->after('valor_mensal');
            }
            if (!Schema::hasColumn('lojas', 'data_vencimento')) {
                $table->date('data_vencimento')->nullable()->after('desconto');
            }
            if (!Schema::hasColumn('lojas', 'observacoes_admin')) {
                $table->text('observacoes_admin')->nullable(); // Anotações internas do super_admin
            }
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->dropColumn(['plano', 'valor_mensal', 'desconto', 'data_vencimento', 'observacoes_admin']);
        });
    }
};
