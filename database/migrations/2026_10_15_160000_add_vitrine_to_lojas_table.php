<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * VITRINE PÚBLICA de lojas parceiras na landing page.
 *
 * - vitrine_publico: opt-in do próprio lojista (padrão false). Só aparece na
 *   página inicial quem LIGAR esse botão no painel da loja.
 * - instagram / site: canais de contato para o botão "Falar com a loja"
 *   (além do telefone/WhatsApp que a loja já tem).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->boolean('vitrine_publico')->default(false)->after('logo');
            $table->string('instagram', 120)->nullable()->after('vitrine_publico');
            $table->string('site', 180)->nullable()->after('instagram');
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->dropColumn(['vitrine_publico', 'instagram', 'site']);
        });
    }
};
