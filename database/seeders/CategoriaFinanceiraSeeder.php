<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaFinanceira;
use App\Models\Loja;

class CategoriaFinanceiraSeeder extends Seeder
{
    /**
     * Lista padrão de categorias que cada loja recebe ao ser criada.
     *
     * @return array<int, array{nome: string, tipo: string}>
     */
    public static function categoriasPadrao(): array
    {
        return [
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
    }

    /**
     * Cria a lista padrão de categorias para UMA loja específica (opção B:
     * categorias são isoladas por loja).
     */
    public static function popularParaLoja(int $lojaId): void
    {
        foreach (self::categoriasPadrao() as $categoria) {
            CategoriaFinanceira::withoutGlobalScope('loja')->firstOrCreate([
                'loja_id' => $lojaId,
                'nome' => $categoria['nome'],
                'tipo' => $categoria['tipo'],
            ]);
        }
    }

    /**
     * Ao rodar isolado, popula as categorias para todas as lojas existentes.
     */
    public function run(): void
    {
        Loja::all()->each(fn (Loja $loja) => self::popularParaLoja($loja->id));
    }
}
