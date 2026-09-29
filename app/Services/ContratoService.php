<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Loja;
use App\Models\Locacao;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Preenche modelos de contrato substituindo variáveis {{chave}} pelos dados
 * reais do cliente, da locação, da peça e da loja.
 *
 * Ex.: "Eu, {{cliente_nome}}, CPF {{cliente_cpf}}..." vira o texto completo.
 */
class ContratoService
{
    /**
     * Lista de variáveis disponíveis (para exibir na tela de edição do modelo).
     * chave => descrição amigável.
     *
     * @return array<string, string>
     */
    public static function variaveisDisponiveis(): array
    {
        return [
            // Cliente
            'cliente_nome' => 'Nome do cliente',
            'cliente_cpf' => 'CPF do cliente',
            'cliente_rg' => 'RG do cliente',
            'cliente_telefone' => 'Telefone / WhatsApp',
            'cliente_email' => 'E-mail',
            'cliente_nascimento' => 'Data de nascimento',
            'cliente_nacionalidade' => 'Nacionalidade',
            'cliente_estado_civil' => 'Estado civil',
            'cliente_profissao' => 'Profissão',
            'cliente_endereco' => 'Endereço completo (uma linha)',
            'cliente_cep' => 'CEP',
            'cliente_cidade' => 'Cidade',
            'cliente_estado' => 'Estado (UF)',
            // Locação
            'locacao_peca' => 'Peça alugada (código + nome)',
            'locacao_valor_total' => 'Valor total (R$)',
            'locacao_sinal' => 'Sinal pago (R$)',
            'locacao_saldo' => 'Saldo a pagar (R$)',
            'locacao_caucao' => 'Caução da peça (R$)',
            'locacao_data_retirada' => 'Data de retirada',
            'locacao_data_evento' => 'Data do evento',
            'locacao_data_devolucao' => 'Data de devolução',
            // Loja
            'loja_nome' => 'Nome da loja',
            'loja_cnpj' => 'CNPJ/CPF da loja',
            'loja_telefone' => 'Telefone da loja',
            // Gerais
            'data_hoje' => 'Data de hoje (extenso)',
            'cidade_hoje' => 'Cidade da loja + data de hoje',
        ];
    }

    /**
     * Monta o mapa de valores reais para um cliente/locação/loja.
     *
     * @return array<string, string>
     */
    public function valores(Loja $loja, ?Cliente $cliente = null, ?Locacao $locacao = null): array
    {
        $mesesPt = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
            'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $hoje = Carbon::now();
        $dataExtenso = $hoje->day . ' de ' . $mesesPt[$hoje->month] . ' de ' . $hoje->year;

        $valores = [
            'loja_nome' => $loja->nome_fantasia,
            'loja_cnpj' => $loja->cnpj_cpf,
            'loja_telefone' => $loja->telefone ?? '',
            'data_hoje' => $dataExtenso,
            'cidade_hoje' => ($cliente?->cidade ?: '____________') . ', ' . $dataExtenso,
        ];

        if ($cliente) {
            $valores += [
                'cliente_nome' => $cliente->nome,
                'cliente_cpf' => $cliente->cpf ?? '',
                'cliente_rg' => $cliente->rg ?? '',
                'cliente_telefone' => $cliente->telefone ?? '',
                'cliente_email' => $cliente->email ?? '',
                'cliente_nascimento' => $cliente->data_nascimento ? Carbon::parse($cliente->data_nascimento)->format('d/m/Y') : '',
                'cliente_nacionalidade' => $cliente->nacionalidade ?? '',
                'cliente_estado_civil' => $cliente->estado_civil ?? '',
                'cliente_profissao' => $cliente->profissao ?? '',
                'cliente_endereco' => $this->enderecoCompleto($cliente),
                'cliente_cep' => $cliente->cep ?? '',
                'cliente_cidade' => $cliente->cidade ?? '',
                'cliente_estado' => $cliente->estado ?? '',
            ];
        }

        if ($locacao) {
            $peca = $locacao->acervo;
            $saldo = (float) $locacao->valor_total - (float) $locacao->sinal_pago;
            $valores += [
                'locacao_peca' => $peca ? "{$peca->codigo} - {$peca->nome}" : '',
                'locacao_valor_total' => $this->moeda($locacao->valor_total),
                'locacao_sinal' => $this->moeda($locacao->sinal_pago),
                'locacao_saldo' => $this->moeda($saldo),
                'locacao_caucao' => $this->moeda($peca->caucao ?? 0),
                'locacao_data_retirada' => optional($locacao->data_retirada)->format('d/m/Y') ?? '',
                'locacao_data_evento' => optional($locacao->data_evento)->format('d/m/Y') ?? '',
                'locacao_data_devolucao' => optional($locacao->data_devolucao)->format('d/m/Y') ?? '',
            ];
        }

        return $valores;
    }

    /**
     * Substitui as variáveis {{chave}} do conteúdo pelos valores reais.
     * Variáveis sem valor viram um traço para preenchimento manual.
     */
    public function preencher(string $conteudo, array $valores): string
    {
        return preg_replace_callback('/\{\{\s*([a-z_]+)\s*\}\}/i', function ($m) use ($valores) {
            $chave = strtolower(trim($m[1]));
            $valor = $valores[$chave] ?? null;
            return ($valor === null || $valor === '') ? '____________' : $valor;
        }, $conteudo);
    }

    private function enderecoCompleto(Cliente $c): string
    {
        $partes = array_filter([
            trim(($c->rua ?? '') . ($c->numero ? ', ' . $c->numero : '')),
            $c->complemento,
            $c->bairro,
            trim(($c->cidade ?? '') . ($c->estado ? ' - ' . $c->estado : '')),
            $c->cep ? 'CEP ' . $c->cep : null,
        ]);

        return implode(', ', $partes);
    }

    private function moeda($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}
