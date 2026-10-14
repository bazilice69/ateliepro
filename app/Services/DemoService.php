<?php

namespace App\Services;

use App\Models\Acervo;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\LancamentoFinanceiro;
use App\Models\Loja;
use App\Models\Locacao;
use App\Models\Prova;
use App\Models\Servico;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Popula (e limpa) uma loja com dados de DEMONSTRAÇÃO realistas, para o lojista
 * conhecer o sistema "vivo" durante o teste grátis. Tudo é marcado com a flag
 * `is_demo = true` para poder ser removido depois sem tocar nos dados reais.
 *
 * Reaproveita a mesma linha de dados do DemoVivoSeeder, mas aplicada à loja
 * informada (a loja do usuário logado), respeitando o multi-tenant.
 */
class DemoService
{
    /** Popula a loja com dados fictícios. Idempotente (não duplica). */
    public function popular(Loja $loja): void
    {
        DB::transaction(function () use ($loja) {
            // --- Clientes ---
            $ana = $this->criar(Cliente::class, $loja, ['nome' => 'Ana Clara Ribeiro (exemplo)'], [
                'telefone' => '(11) 98888-1111', 'email' => 'ana.exemplo@email.com', 'cpf' => '111.222.333-44',
                'rg' => '12.345.678-9', 'tipo' => 'Feminino', 'cidade' => 'São Paulo', 'estado' => 'SP',
                'rua' => 'Rua das Noivas', 'numero' => '100', 'bairro' => 'Luz', 'estado_civil' => 'Solteira',
                'nacionalidade' => 'Brasileira', 'profissao' => 'Professora',
            ]);
            $joao = $this->criar(Cliente::class, $loja, ['nome' => 'João Pedro Santos (exemplo)'], [
                'telefone' => '(11) 98888-2222', 'tipo' => 'Masculino', 'cidade' => 'São Paulo', 'estado' => 'SP',
            ]);
            $madrinha = $this->criar(Cliente::class, $loja, ['nome' => 'Fernanda Lima (exemplo)'], [
                'telefone' => '(11) 98888-3333', 'tipo' => 'Feminino',
            ]);

            // --- Peças ---
            $vestidoNoiva = $this->criar(Acervo::class, $loja, ['codigo' => 'EX-100'], [
                'nome' => 'Vestido Sereia Marfim (exemplo)', 'categoria' => 'Noiva', 'publico' => 'feminino',
                'tamanho' => '38', 'cor' => 'Marfim', 'valor_locacao' => 1800, 'caucao' => 500, 'status' => 'reservada',
            ]);
            $terno = $this->criar(Acervo::class, $loja, ['codigo' => 'EX-200'], [
                'nome' => 'Terno Slim Grafite (exemplo)', 'categoria' => 'Terno', 'publico' => 'masculino',
                'tamanho' => '48', 'cor' => 'Grafite', 'valor_locacao' => 600, 'status' => 'disponivel',
            ]);
            $vestidoFesta = $this->criar(Acervo::class, $loja, ['codigo' => 'EX-300'], [
                'nome' => 'Vestido Longo Champagne (exemplo)', 'categoria' => 'Festa', 'publico' => 'feminino',
                'tamanho' => '40', 'cor' => 'Champagne', 'valor_locacao' => 450, 'status' => 'disponivel',
            ]);

            // --- Evento (casamento daqui a 25 dias — contagem regressiva) ---
            $casamento = $this->criar(Evento::class, $loja, ['nome_evento' => 'Casamento Ana & João (exemplo)'], [
                'data_evento' => Carbon::now()->addDays(25), 'tipo_evento' => 'Casamento', 'status' => 'Ativo',
            ]);

            // --- Locação da noiva ---
            $locacaoAna = $this->criar(Locacao::class, $loja,
                ['cliente_id' => $ana->id, 'acervo_id' => $vestidoNoiva->id], [
                    'data_retirada' => Carbon::now()->addDays(23), 'data_evento' => Carbon::now()->addDays(25),
                    'data_devolucao' => Carbon::now()->addDays(27), 'data_liberacao_prevista' => Carbon::now()->addDays(30),
                    'valor_total' => 1800, 'sinal_pago' => 800, 'caucao' => 500, 'status' => 'em_provas',
                ]);

            // --- Provas (uma realizada com ajuste, uma agendada p/ hoje) ---
            $this->criar(Prova::class, $loja,
                ['cliente_id' => $ana->id, 'data_prova' => Carbon::now()->subDays(3)->setTime(10, 0)], [
                    'acervo_id' => $vestidoNoiva->id, 'locacao_id' => $locacaoAna->id, 'responsavel' => 'Equipe',
                    'status' => 'realizada', 'resultado' => 'precisa_ajuste', 'ajustes_necessarios' => 'Ajustar barra e cintura',
                ]);
            $this->criar(Prova::class, $loja,
                ['cliente_id' => $ana->id, 'data_prova' => Carbon::today()->setTime(15, 0)], [
                    'acervo_id' => $vestidoNoiva->id, 'locacao_id' => $locacaoAna->id, 'responsavel' => 'Equipe',
                    'status' => 'agendada',
                ]);

            // --- Serviço na oficina (ajuste em andamento) ---
            $this->criar(Servico::class, $loja,
                ['acervo_id' => $vestidoNoiva->id, 'descricao' => 'Ajuste de barra e cintura (exemplo)'], [
                    'cliente_id' => $ana->id, 'locacao_id' => $locacaoAna->id, 'tipo' => 'ajuste',
                    'itens' => ['Barra', 'Cintura'], 'responsavel' => 'Costureira',
                    'prazo' => Carbon::now()->addDays(10), 'custo' => 0, 'status' => 'em_andamento',
                ]);

            // --- Financeiro (receitas e despesas) ---
            $lancamentos = [
                ['Receita', $ana->id, 'Sinal - Vestido Ana (exemplo)', 800, 'Pago', Carbon::now()->subDays(5)],
                ['Receita', $ana->id, 'Saldo - Vestido Ana (exemplo)', 1000, 'Pendente', Carbon::now()->addDays(20)],
                ['Receita', $madrinha->id, 'Locação - Madrinha (exemplo)', 450, 'Pago', Carbon::now()->subDays(2)],
                ['Despesa', null, 'Renda e aviamentos (exemplo)', 320, 'Pago', Carbon::now()->subDays(3)],
            ];
            foreach ($lancamentos as [$tipo, $cliId, $desc, $valor, $status, $venc]) {
                $this->criar(LancamentoFinanceiro::class, $loja, ['descricao' => $desc], [
                    'tipo' => $tipo, 'cliente_id' => $cliId,
                    'valor_original' => $valor, 'desconto' => 0, 'acrescimo' => 0, 'valor_final' => $valor,
                    'data_vencimento' => $venc, 'status' => $status,
                    'data_pagamento' => $status === 'Pago' ? $venc : null,
                ]);
            }
        });
    }

    /** Remove todos os dados marcados como demo da loja. */
    public function limpar(Loja $loja): void
    {
        DB::transaction(function () use ($loja) {
            foreach ([Servico::class, Prova::class, Locacao::class, LancamentoFinanceiro::class,
                      Evento::class, Acervo::class, Cliente::class] as $model) {
                $model::withoutGlobalScope('loja')
                    ->where('loja_id', $loja->id)
                    ->where('is_demo', true)
                    ->delete();
            }
        });
    }

    public function temDemo(Loja $loja): bool
    {
        return Cliente::withoutGlobalScope('loja')
            ->where('loja_id', $loja->id)->where('is_demo', true)->exists();
    }

    /**
     * Cria um registro de demo (idempotente) marcando is_demo = true e o loja_id.
     */
    private function criar(string $model, Loja $loja, array $chave, array $valores)
    {
        $chave['loja_id'] = $loja->id;
        $valores['is_demo'] = true;

        return $model::withoutGlobalScope('loja')->firstOrCreate($chave, $valores);
    }
}
