<?php

namespace Database\Seeders;

use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\Loja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seed de DEMONSTRAÇÃO do multi-tenancy.
 *
 * Cria um super_admin (dono do SaaS) e DUAS lojas independentes (Bella Noivas e
 * Elegance Noivas), cada uma com seu próprio admin, clientes e acervo. Serve
 * para você TESTAR o isolamento: logue como o admin de uma loja e confirme que
 * não consegue ver os dados da outra (nem pela URL/ID).
 *
 * Rode com:  php artisan db:seed --class=DemoMultiTenantSeeder
 */
class DemoMultiTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin (dono do AteliêPro) — sem loja, enxerga tudo.
        User::firstOrCreate(
            ['email' => 'admin@ateliepro.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'loja_id' => null,
            ]
        );

        $this->criarLoja(
            nomeFantasia: 'Bella Noivas',
            cnpj: '11.111.111/0001-11',
            adminEmail: 'bella@ateliepro.com',
            adminNome: 'Maria (Bella)',
            clientes: ['Ana Noiva', 'Carla Madrinha'],
            pecas: [['BELLA-001', 'Vestido Sereia Marfim'], ['BELLA-002', 'Vestido Princesa']],
        );

        $this->criarLoja(
            nomeFantasia: 'Elegance Noivas',
            cnpj: '22.222.222/0001-22',
            adminEmail: 'elegance@ateliepro.com',
            adminNome: 'João (Elegance)',
            clientes: ['Beatriz Debutante', 'Rafael Noivo'],
            pecas: [['ELEG-001', 'Terno Slim Preto'], ['ELEG-002', 'Vestido Debutante Rosa']],
        );
    }

    /**
     * @param array<int, string> $clientes
     * @param array<int, array{0: string, 1: string}> $pecas
     */
    private function criarLoja(
        string $nomeFantasia,
        string $cnpj,
        string $adminEmail,
        string $adminNome,
        array $clientes,
        array $pecas,
    ): void {
        $loja = Loja::firstOrCreate(
            ['cnpj_cpf' => $cnpj],
            [
                'chave_licenca' => (string) Str::uuid(),
                'nome_fantasia' => $nomeFantasia,
                'email_responsavel' => $adminEmail,
                'status' => Loja::STATUS_ATIVO,
            ]
        );

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminNome,
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN_LOJA,
                'loja_id' => $loja->id,
            ]
        );

        // Categorias financeiras próprias da loja (opção B).
        CategoriaFinanceiraSeeder::popularParaLoja($loja->id);

        foreach ($clientes as $nome) {
            Cliente::withoutGlobalScope('loja')->firstOrCreate(
                ['loja_id' => $loja->id, 'nome' => $nome],
                ['telefone' => '(11) 90000-0000', 'tipo' => 'Feminino']
            );
        }

        foreach ($pecas as [$codigo, $nome]) {
            Acervo::withoutGlobalScope('loja')->firstOrCreate(
                ['loja_id' => $loja->id, 'codigo' => $codigo],
                ['nome' => $nome, 'categoria' => 'Noiva', 'valor_locacao' => 500, 'status' => Acervo::STATUS_DISPONIVEL]
            );
        }
    }
}
