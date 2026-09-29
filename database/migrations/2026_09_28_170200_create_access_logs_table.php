<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * (Fase 2) Log de acessos — cada login gera um registro.
 * Permite ao super_admin ver claramente quem entrou e em qual loja.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('loja_id')->nullable()->constrained('lojas')->nullOnDelete();
            $table->string('user_nome')->nullable();  // snapshot (caso o user seja apagado)
            $table->string('loja_nome')->nullable();  // snapshot
            $table->string('evento')->default('login'); // login, logout, etc.
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['loja_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
