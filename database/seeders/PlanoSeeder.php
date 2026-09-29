<?php

namespace Database\Seeders;

use App\Models\Plano;
use Illuminate\Database\Seeder;

/**
 * Popula os planos padrão do AteliêPro (baseados na landing page atual).
 * Depois, o super_admin ajusta preços/recursos pelo painel /admin/planos.
 */
class PlanoSeeder extends Seeder
{
    public function run(): void
    {
        $planos = [
            [
                'nome' => 'Mensal', 'slug' => 'mensal', 'preco' => 89.00, 'meses' => 1,
                'periodo_label' => '/mês', 'destaque' => false, 'ordem' => 1,
                'recursos' => ['Gestão de Locações', 'Controle Financeiro', 'Suporte por Email'],
            ],
            [
                'nome' => 'Trimestral', 'slug' => 'trimestral', 'preco' => 239.00, 'meses' => 3,
                'periodo_label' => '/tri', 'destaque' => false, 'ordem' => 2,
                'recursos' => ['Todos os recursos', 'Economia de 10%', 'Suporte por Email'],
            ],
            [
                'nome' => 'Semestral', 'slug' => 'semestral', 'preco' => 429.00, 'meses' => 6,
                'periodo_label' => '/sem', 'destaque' => true, 'ordem' => 3,
                'recursos' => ['Todos os recursos', 'Economia de 20%', 'Suporte Prioritário'],
            ],
            [
                'nome' => 'Anual', 'slug' => 'anual', 'preco' => 749.00, 'meses' => 12,
                'periodo_label' => '/ano', 'destaque' => false, 'ordem' => 4,
                'recursos' => ['Todos os recursos', 'Economia de 30%', 'Treinamento VIP'],
            ],
        ];

        foreach ($planos as $plano) {
            Plano::updateOrCreate(['slug' => $plano['slug']], $plano);
        }
    }
}
