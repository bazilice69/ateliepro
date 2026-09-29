<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 10) Campos operacionais da locação: caução, e o registro real de
 * RETIRADA e DEVOLUÇÃO com vistoria (checklist, estado da peça, multa e fotos).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('locacaos', function (Blueprint $table) {
            // Caução (garantia) — pode diferir do valor sugerido no acervo.
            if (!Schema::hasColumn('locacaos', 'caucao')) {
                $table->decimal('caucao', 10, 2)->default(0)->after('sinal_pago');
            }
            if (!Schema::hasColumn('locacaos', 'caucao_status')) {
                // pendente | retida | devolvida
                $table->string('caucao_status')->default('pendente')->after('caucao');
            }

            // Retirada
            if (!Schema::hasColumn('locacaos', 'retirada_em')) {
                $table->dateTime('retirada_em')->nullable();
            }
            if (!Schema::hasColumn('locacaos', 'retirada_checklist')) {
                $table->text('retirada_checklist')->nullable(); // itens entregues
            }
            if (!Schema::hasColumn('locacaos', 'retirada_fotos')) {
                $table->text('retirada_fotos')->nullable(); // JSON com caminhos das fotos
            }

            // Devolução / vistoria
            if (!Schema::hasColumn('locacaos', 'devolucao_em')) {
                $table->dateTime('devolucao_em')->nullable();
            }
            if (!Schema::hasColumn('locacaos', 'vistoria_estado')) {
                // ok | mancha | dano | rasgo | falta_item
                $table->string('vistoria_estado')->nullable();
            }
            if (!Schema::hasColumn('locacaos', 'vistoria_observacoes')) {
                $table->text('vistoria_observacoes')->nullable();
            }
            if (!Schema::hasColumn('locacaos', 'multa')) {
                $table->decimal('multa', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('locacaos', 'devolucao_fotos')) {
                $table->text('devolucao_fotos')->nullable(); // JSON
            }
        });
    }

    public function down(): void
    {
        Schema::table('locacaos', function (Blueprint $table) {
            $table->dropColumn([
                'caucao', 'caucao_status', 'retirada_em', 'retirada_checklist',
                'retirada_fotos', 'devolucao_em', 'vistoria_estado',
                'vistoria_observacoes', 'multa', 'devolucao_fotos',
            ]);
        });
    }
};
