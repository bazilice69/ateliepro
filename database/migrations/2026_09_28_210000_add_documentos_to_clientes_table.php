<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 6) Documentos de praxe do cliente, usados no auto-preenchimento de
 * contratos (RG, nacionalidade, estado civil, profissão, e-mail).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            foreach ([
                'rg' => fn () => $table->string('rg')->nullable(),
                'email' => fn () => $table->string('email')->nullable(),
                'nacionalidade' => fn () => $table->string('nacionalidade')->nullable()->default('Brasileira'),
                'estado_civil' => fn () => $table->string('estado_civil')->nullable(),
                'profissao' => fn () => $table->string('profissao')->nullable(),
            ] as $coluna => $criar) {
                if (!Schema::hasColumn('clientes', $coluna)) {
                    $criar();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['rg', 'email', 'nacionalidade', 'estado_civil', 'profissao']);
        });
    }
};
