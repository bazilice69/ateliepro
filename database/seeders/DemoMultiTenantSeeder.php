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
        // Contato de suporte padrão (configurável depois em /admin/config).
        \App\Models\Setting::set('saas_nome', 'Bonacci Rental');
        \App\Models\Setting::set('saas_email_suporte', 'thg.bazilice@gmail.com');
        \App\Models\Setting::set('saas_whatsapp_suporte', '5511957866836');

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

        // Modelo de contrato REAL da Andreia Bonacci (com auto-preenchimento).
        \App\Models\ModeloContrato::withoutGlobalScope('loja')->updateOrCreate(
            ['loja_id' => $loja->id, 'titulo' => 'Contrato de Venda, 1º Aluguel e Realuguel'],
            ['ativo' => true, 'conteudo' => <<<TXT
CONTRATO DE VENDA, 1º ALUGUEL E REALUGUEL

Pelo presente instrumento particular, de um lado Andréia A. P. da Silva Confecções-ME, empresa de direito privado, Avenida Laranja da China, nº 148 - São Miguel Paulista - SP, inscrita no CNPJ 18.113.247/0001-03, doravante denominada {{loja_nome}}, e de outro lado:

Nome: {{cliente_nome}}          CPF: {{cliente_cpf}}
Endereço: {{cliente_endereco}}          Tel: {{cliente_telefone}}
Bairro: ____________   CEP: {{cliente_cep}}   Cel: {{cliente_telefone}}
E-mail: {{cliente_email}}   Face: ____________   Insta: ____________
Evento: {{locacao_data_evento}}   Saída: {{locacao_data_retirada}}   Devolução: {{locacao_data_devolucao}}
Referência: {{locacao_peca}}
Modelo: {{locacao_peca}}

Forma de pagamento: ____________          Valor: {{locacao_valor_total}}
Sinal/Entrada: {{locacao_sinal}}          Saldo a pagar: {{locacao_saldo}}
Caução: {{locacao_caucao}}

CONDIÇÕES GERAIS:

CLÁUSULA 1ª - A(o) CLIENTE contrata a {{loja_nome}} para o fornecimento de um traje de noiva(o)/festa e acessórios, escolhido com as especificações detalhadas e nas medidas discriminadas na ficha de controle de qualidade anexa, que deverá ser entregue, pronto e acabado, na data prevista, cabendo a(o) CLIENTE retirá-lo na sede da loja, ou em local previamente combinado entre as partes.

PARÁGRAFO 1º - Esgotado o prazo de vencimento, em caso de locação, dito aluguel deverá ser pago com acréscimo de 20% (vinte por cento) sobre o valor integral; caso ultrapasse 30 (trinta) dias do vencimento, além da multa ora prevista, incidirá na cobrança integral do traje locado.

CLÁUSULA 2ª - A(o) CLIENTE deverá conservar o traje em boas condições e devolvê-lo na forma e nas condições em que o retirou. Por eventuais danos, tais como manchas, rasgos, queimados, excesso de sujeira, decorrentes de mau uso ou de caso fortuito, será cobrado um valor equivalente para o referido reparo.

CLÁUSULA 3ª - Fica fazendo parte integrante do presente contrato um TERMO DE RESPONSABILIDADE, documento anexo, consistente do estado geral em que a(o) CLIENTE recebe o traje, o qual é assinado pelas partes contratantes, pelo qual se compromete a restituir, no caso de locação, à {{loja_nome}}, no mesmo estado.

CLÁUSULA 4ª - A reparação ou indenização dos estragos ficará sob responsabilidade e custas da(o) CLIENTE, podendo a {{loja_nome}} recusar o recebimento do traje caso o mesmo se negue a reparar ou indenizar os estragos porventura apurados, continuando a correr o aluguel por conta da(o) CLIENTE até a solução final, valendo como documento probatório da restituição do traje.

CLÁUSULA 5ª - A(o) CLIENTE tem o prazo de 24 horas, contados da retirada do traje, para comunicar à {{loja_nome}} ou seu representante possíveis defeitos ou estragos, desde que comprovados por imperícia ou negligência na entrega por parte da locadora, sendo certo que o não pronunciamento nesse prazo implicará no reconhecimento de que o traje lhe foi entregue em perfeitas condições descritas no TERMO DE GARANTIA, isentando a {{loja_nome}} de qualquer responsabilidade por eventuais danos.

CLÁUSULA 6ª - Fica expressamente proibido a(o) CLIENTE promover mudanças ou permitir que terceiros façam qualquer alteração ou modificação no traje, sem prévia comunicação escrita autorizada da {{loja_nome}}. No caso de ser dado total consentimento, a(o) CLIENTE expressamente manifesta sua renúncia quanto ao direito de retenção ou indenização por tal serviço.

CLÁUSULA 7ª - A(o) CLIENTE não poderá transferir, sublocar, ceder ou emprestar o traje.

CLÁUSULA 8ª - É de inteira responsabilidade da(o) CLIENTE as informações aqui prestadas, não podendo desistir da presente transação, pois a partir desta data o traje ficará à disposição da(o) CLIENTE, em estado de conservação para eventuais reparos e/ou confecção. Arcará a(o) CLIENTE, em caso de desistência, com multa de 40% do valor integral até 7 dias após a contratação da venda ou locação; após esse período, fica de inteira responsabilidade da(o) CLIENTE arcar com o valor integral da transação, seja por justo motivo, seja fortuito ou por motivo de força maior.

CLÁUSULA 9ª - Em caso de desistência, aplicam-se as normas do artigo 420 do Código Civil Brasileiro.

CLÁUSULA 10ª - A(o) CLIENTE se obriga a pagar a taxa de lavanderia, não podendo o traje ser lavado por terceiros.

CLÁUSULA 11ª - A(o) CLIENTE fica ciente que, na entrega do traje, deverá ser dado em caução um cheque ou uma promissória no valor total do traje, que será restituído na devolução do mesmo à {{loja_nome}}. Será cobrada uma taxa de lavanderia no ato da retirada de {{locacao_caucao}}.

Este contrato está de acordo com as regras estabelecidas pelo Código de Defesa do Consumidor e pelo Código Civil Brasileiro e suas alterações.

E, assim, por estarem justas e contratadas, as partes assinam o presente instrumento em 2 (duas) vias de igual teor e forma, para os devidos fins de direito.

{{cidade_hoje}}


______________________________          ______________________________
   CLIENTE OU PROCURADOR                      {{loja_nome}}
   {{cliente_nome}}


Declaro estar recebendo o traje em perfeito estado e que o mesmo encontra-se devidamente revisado em suas características e de acordo com as medidas devidamente elaboradas nas provas efetuadas, neste ato da entrega e demais procedimentos.

{{cidade_hoje}}

De acordo: ______________________________
TXT]
        );
    }
}
