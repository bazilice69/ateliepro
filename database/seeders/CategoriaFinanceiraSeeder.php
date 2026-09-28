<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaFinanceira;

class CategoriaFinanceiraSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            // 🟢 RECEITAS (Entradas)
            ['nome' => 'Locação de Trajes', 'tipo' => 'Receita'],
            ['nome' => 'Venda de Peças', 'tipo' => 'Receita'],
            ['nome' => 'Confecção Sob Medida', 'tipo' => 'Receita'],
            ['nome' => 'Ajustes e Consertos', 'tipo' => 'Receita'],
            ['nome' => 'Retenção de Caução', 'tipo' => 'Receita'],
            ['nome' => 'Multas e Juros', 'tipo' => 'Receita'],
            ['nome' => 'Outras Receitas', 'tipo' => 'Receita'],

            // 🔴 DESPESAS (Saídas)
            ['nome' => 'Materiais e Aviamentos', 'tipo' => 'Despesa'], 
            ['nome' => 'Despesas Operacionais', 'tipo' => 'Despesa'], 
            ['nome' => 'Funcionários e Costureiras', 'tipo' => 'Despesa'], 
            ['nome' => 'Lavanderia e Limpeza', 'tipo' => 'Despesa'],
            ['nome' => 'Compra de Peças Prontas', 'tipo' => 'Despesa'], 
            ['nome' => 'Manutenção e Consertos', 'tipo' => 'Despesa'], 
            ['nome' => 'Marketing e Anúncios', 'tipo' => 'Despesa'],
            ['nome' => 'Impostos e Taxas', 'tipo' => 'Despesa'],
            ['nome' => 'Outras Despesas', 'tipo' => 'Despesa'],
        ];

      foreach ($categorias as $categoria) {
            CategoriaFinanceira::firstOrCreate([
                'nome' => $categoria['nome'],
                'tipo' => $categoria['tipo']
            ]);
        }
    }
}