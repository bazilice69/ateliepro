<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loja_id')
                ->constrained('lojas')
                ->cascadeOnDelete();

            $table->string('plano');

            $table->decimal('valor', 10, 2);

            $table->string('transacao_mp_id')->nullable();

            $table->date('data_vencimento')->nullable();

            $table->enum('status', [
                'pendente',
                'paga',
                'cancelada'
            ])->default('pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};