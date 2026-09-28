<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('chave_licenca');
            $table->string('tipo_plano');
            $table->date('data_expiracao');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('licenses');
    }
};