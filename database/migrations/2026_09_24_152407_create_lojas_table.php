<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lojas', function (Blueprint $table) {
            $table->id();

            $table->uuid('chave_licenca')->unique();

            $table->string('nome_fantasia');

            $table->string('cnpj_cpf')->unique();

            $table->string('email_responsavel');

            $table->string('telefone')->nullable();

            $table->enum('status', [
                'ativo',
                'inadimplente',
                'bloqueado'
            ])->default('ativo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lojas');
    }
};