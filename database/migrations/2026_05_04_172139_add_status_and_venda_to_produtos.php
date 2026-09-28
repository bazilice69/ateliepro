<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('produtos', function (Blueprint $table) {
            $table->decimal('valor_venda', 10, 2)->nullable()->after('valor_locacao');
            // Status: 1=Disponível, 2=Locado, 3=Manutenção, 4=Vendido
            $table->integer('status')->default(1)->after('quantidade_estoque');
        });
    }
};