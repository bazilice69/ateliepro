<?php

namespace Database\Seeders;

use App\Models\Acervo;
use App\Models\Agendamento;
use App\Models\CategoriaFinanceira;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\LancamentoFinanceiro;
use App\Models\Loja;
use App\Models\Locacao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seed de DEMONSTRAÇÃO "vivo": deixa a loja Bella Noivas cheia de dados
 * realistas para uma apresentação convincente — noivas, madrinha, um casamento
 * chegando (contagem regressiva), provas agendadas, locações e financeiro.
 *
 * Rode DEPOIS do DemoMultiTenantSeeder:
 *   php artisan db:seed --class=DemoVivoSeeder
 */
class DemoVivoSeeder extends Seeder
{
    public function run(): void
    {
        $loja = Loja::where('cnpj_cpf', '11.111.111/0001-11')->first(); // Bella Noivas
        if (!$loja) {
            $this->command?->warn('Rode o DemoMultiTenantSeeder primeiro.');
            return;
        }

        $semScope = fn ($q) => $q->withoutGlobalScope('loja');

        // --- Clientes (noiva, noivo, madrinha, mãe, daminha) ---
        $ana = Cliente::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'nome' => 'Ana Clara Ribeiro'],
            ['telefone' => '(11) 98888-1111', 'email' => 'ana@email.com', 'cpf' => '111.222.333-44',
             'rg' => '12.345.678-9', 'tipo' => 'Feminino', 'cidade' => 'São Paulo', 'estado' => 'SP',
             'rua' => 'Rua das Noivas', 'numero' => '100', 'bairro' => 'Luz', 'estado_civil' => 'Solteira',
             'nacionalidade' => 'Brasileira', 'profissao' => 'Professora']
        );
        $joao = Cliente::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'nome' => 'João Pedro Santos'],
            ['telefone' => '(11) 98888-2222', 'tipo' => 'Masculino', 'cidade' => 'São Paulo', 'estado' => 'SP']
        );
        $madrinha = Cliente::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'nome' => 'Fernanda Lima'],
            ['telefone' => '(11) 98888-3333', 'tipo' => 'Feminino']
        );
        $debutante = Cliente::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'nome' => 'Sofia Mendes (15 anos)'],
            ['telefone' => '(11) 98888-4444', 'tipo' => 'Feminino']
        );

        // --- Peças ---
        $vestidoNoiva = Acervo::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'codigo' => 'VN-100'],
            ['nome' => 'Vestido Sereia Marfim', 'categoria' => 'Noiva', 'publico' => 'feminino',
             'tamanho' => '38', 'cor' => 'Marfim', 'valor_locacao' => 1800, 'caucao' => 500, 'status' => 'reservada']
        );
        $terno = Acervo::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'codigo' => 'TN-200'],
            ['nome' => 'Terno Slim Grafite', 'categoria' => 'Terno', 'publico' => 'masculino',
             'tamanho' => '48', 'cor' => 'Grafite', 'valor_locacao' => 600, 'status' => 'disponivel']
        );
        $vestidoFesta = Acervo::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'codigo' => 'VF-300'],
            ['nome' => 'Vestido Longo Champagne', 'categoria' => 'Festa', 'publico' => 'feminino',
             'tamanho' => '40', 'cor' => 'Champagne', 'valor_locacao' => 450, 'status' => 'disponivel']
        );

        // --- Evento (casamento daqui a 25 dias — contagem regressiva) ---
        $casamento = Evento::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'nome_evento' => 'Casamento Ana & João'],
            ['data_evento' => Carbon::now()->addDays(25), 'tipo_evento' => 'Casamento', 'status' => 'Ativo']
        );
        Evento::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'nome_evento' => '15 Anos da Sofia'],
            ['data_evento' => Carbon::now()->addDays(60), 'tipo_evento' => '15 Anos', 'status' => 'Ativo']
        );

        // --- Agenda (provas de hoje e dos próximos dias) ---
        $agenda = [
            [$ana->id, $vestidoNoiva->id, Carbon::today()->setTime(10, 0), 'Prova Final', 'green', 'Sala 1 - Noivas'],
            [$madrinha->id, $vestidoFesta->id, Carbon::today()->setTime(11, 30), 'Prova 1', 'yellow', 'Sala 2 - Festas'],
            [$joao->id, $terno->id, Carbon::today()->setTime(14, 0), 'Primeiro Atendimento', 'blue', 'Sala 3'],
            [$ana->id, $vestidoNoiva->id, Carbon::tomorrow()->setTime(9, 0), 'Retirada', 'blue', 'Sala 1 - Noivas'],
        ];
        foreach ($agenda as [$cli, $prod, $dh, $tipo, $cor, $sala]) {
            Agendamento::withoutGlobalScope('loja')->firstOrCreate(
                ['loja_id' => $loja->id, 'cliente_id' => $cli, 'data_hora' => $dh],
                ['evento_id' => $casamento->id, 'produto_id' => $prod, 'duracao_minutos' => 60,
                 'tipo_atendimento' => $tipo, 'status_cor' => $cor, 'sala_atendimento' => $sala]
            );
        }

        // --- Locação da noiva ---
        Locacao::withoutGlobalScope('loja')->firstOrCreate(
            ['loja_id' => $loja->id, 'cliente_id' => $ana->id, 'acervo_id' => $vestidoNoiva->id],
            ['data_retirada' => Carbon::now()->addDays(23), 'data_evento' => Carbon::now()->addDays(25),
             'data_devolucao' => Carbon::now()->addDays(27), 'data_liberacao_prevista' => Carbon::now()->addDays(30),
             'valor_total' => 1800, 'sinal_pago' => 800, 'status' => 'reservada']
        );

        // --- Financeiro (receitas e despesas do mês) ---
        $catReceita = CategoriaFinanceira::withoutGlobalScope('loja')->where('loja_id', $loja->id)->where('tipo', 'Receita')->first();
        $catDespesa = CategoriaFinanceira::withoutGlobalScope('loja')->where('loja_id', $loja->id)->where('tipo', 'Despesa')->first();

        $lancamentos = [
            ['Receita', $catReceita?->id, $ana->id, 'Sinal - Vestido Ana', 800, 'Pago', Carbon::now()->subDays(5)],
            ['Receita', $catReceita?->id, $ana->id, 'Saldo - Vestido Ana', 1000, 'Pendente', Carbon::now()->addDays(20)],
            ['Receita', $catReceita?->id, $madrinha->id, 'Locação - Madrinha', 450, 'Pago', Carbon::now()->subDays(2)],
            ['Despesa', $catDespesa?->id, null, 'Renda e aviamentos', 320, 'Pago', Carbon::now()->subDays(3)],
            ['Receita', $catReceita?->id, null, 'Locação avulsa', 500, 'Vencido', Carbon::now()->subDays(4)],
        ];
        foreach ($lancamentos as [$tipo, $catId, $cliId, $desc, $valor, $status, $venc]) {
            LancamentoFinanceiro::withoutGlobalScope('loja')->firstOrCreate(
                ['loja_id' => $loja->id, 'descricao' => $desc],
                ['tipo' => $tipo, 'categoria_id' => $catId, 'cliente_id' => $cliId,
                 'valor_original' => $valor, 'desconto' => 0, 'acrescimo' => 0, 'valor_final' => $valor,
                 'data_vencimento' => $venc, 'status' => $status,
                 'data_pagamento' => $status === 'Pago' ? $venc : null]
            );
        }

        $this->command?->info('Demo "vivo" populado na loja Bella Noivas!');
    }
}
