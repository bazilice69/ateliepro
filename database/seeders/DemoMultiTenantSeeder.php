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
            plano: 'Profissional',
            valorMensal: 149.90,
            funcionarioEmail: 'funcionaria.bella@ateliepro.com',
            funcionarioNome: 'Paula (Costureira)',
        );

        $this->criarLoja(
            nomeFantasia: 'Elegance Noivas',
            cnpj: '22.222.222/0001-22',
            adminEmail: 'elegance@ateliepro.com',
            adminNome: 'João (Elegance)',
            clientes: ['Beatriz Debutante', 'Rafael Noivo'],
            pecas: [['ELEG-001', 'Terno Slim Preto'], ['ELEG-002', 'Vestido Debutante Rosa']],
            plano: 'Essencial',
            valorMensal: 89.90,
            funcionarioEmail: null,
            funcionarioNome: null,
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
        string $plano = 'Essencial',
        float $valorMensal = 89.90,
        ?string $funcionarioEmail = null,
        ?string $funcionarioNome = null,
    ): void {
        $loja = Loja::firstOrCreate(
            ['cnpj_cpf' => $cnpj],
            [
                'chave_licenca' => (string) Str::uuid(),
                'nome_fantasia' => $nomeFantasia,
                'email_responsavel' => $adminEmail,
                'status' => Loja::STATUS_ATIVO,
                'plano' => $plano,
                'valor_mensal' => $valorMensal,
                'data_vencimento' => now()->addMonth(),
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

        // Funcionário de exemplo (acesso restrito) — se informado.
        if ($funcionarioEmail) {
            User::firstOrCreate(
                ['email' => $funcionarioEmail],
                [
                    'name' => $funcionarioNome,
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_FUNCIONARIO,
                    'loja_id' => $loja->id,
                ]
            );
        }

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

        // Modelo de contrato de exemplo (com variáveis de auto-preenchimento).
        \App\Models\ModeloContrato::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'titulo' => 'Contrato de Locação de Traje'],
            ['ativo' => true, 'conteudo' => <<<TXT
CONTRATO DE LOCAÇÃO DE TRAJE

LOCADORA: {{loja_nome}}, CNPJ/CPF {{loja_cnpj}}.
LOCATÁRIO(A): {{cliente_nome}}, RG {{cliente_rg}}, CPF {{cliente_cpf}}, residente em {{cliente_endereco}}.

OBJETO: Locação da peça {{locacao_peca}}.
VALORES: Total {{locacao_valor_total}} (sinal {{locacao_sinal}}, saldo {{locacao_saldo}}).
DATAS: Retirada {{locacao_data_retirada}}, evento {{locacao_data_evento}}, devolução {{locacao_data_devolucao}}.

{{cidade_hoje}}

______________________________          ______________________________
       {{loja_nome}}                            {{cliente_nome}}
TXT]
        );
    }
}
