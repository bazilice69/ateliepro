<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('loja_id')
                ->nullable()
                ->constrained('lojas')
                ->cascadeOnDelete();

            $table->enum('role', [
                'super_admin',
                'admin_loja',
                'funcionario'
            ])->default('admin_loja');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['loja_id']);
            $table->dropColumn(['loja_id', 'role']);
        });
    }
};