<?php

namespace App\Services;

use App\Models\Acervo;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\LancamentoFinanceiro;
use App\Models\Locacao;
use App\Models\Loja;
use App\Support\IA;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Assistente de IA especializado em ateliês/lojas de noivas.
 *
 * Monta um "contexto do negócio" com dados REAIS da loja (agenda, financeiro,
 * peças, clientes) e conversa com o provedor de IA. Quando não há chave
 * configurada, opera em MODO DEMONSTRAÇÃO: responde perguntas comuns usando
 * diretamente os dados da loja, sem chamar nenhuma API externa.
 */
class AssistenteService
{
    /**
     * Responde a uma pergunta da lojista.
     *
     * @param  array<int, array{role: string, content: string}>  $historico
     */
    public function responder(Loja $loja, string $pergunta, array $historico = []): string
    {
        $contexto = $this->montarContexto($loja);

        if (!IA::configurada()) {
            return $this->respostaDemo($loja, $pergunta, $contexto);
        }

        $system = $this->systemPrompt($loja, $contexto);
        $mensagens = array_merge($historico, [['role' => 'user', 'content' => $pergunta]]);

        try {
            return IA::conversar($system, $mensagens);
        } catch (\Throwable $e) {
            return 'Não consegui falar com o serviço de IA agora ('
                . Str::limit($e->getMessage(), 120)
                . '). Verifique a chave em Configurações. Enquanto isso, posso te dar os números da loja pelo modo básico.';
        }
    }

    /**
     * Instrução de sistema: define a IA como especialista no nicho.
     */
    private function systemPrompt(Loja $loja, string $contexto): string
    {
        $nomeSistema = \App\Models\Setting::nomeSistema();

        return <<<TXT
Você é a assistente virtual do {$nomeSistema}, um sistema de gestão para ateliês e
lojas de noivas/festa. Você atende a lojista "{$loja->nome_fantasia}".

Seja objetiva, cordial e prática. Use o contexto de dados abaixo para responder
com números reais. Você entende do negócio: provas, ajustes, retiradas,
devoluções, caução, locação x venda, medidas, eventos (casamento, 15 anos),
cortejo (noiva, madrinhas, daminhas). Responda em português do Brasil.
Se não houver dado suficiente, diga o que falta cadastrar. Não invente valores.

=== CONTEXTO ATUAL DA LOJA ===
{$contexto}
TXT;
    }

    /**
     * Reúne um retrato atual da loja (dados já escopados por loja via trait).
     */
    private function montarContexto(Loja $loja): string
    {
        $hoje = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $provasHoje = Agendamento::whereDate('data_hora', $hoje)->count();
        $provasSemana = Agendamento::whereBetween('data_hora', [$hoje, $hoje->copy()->addDays(7)])->count();

        $recebidoMes = (float) LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Pago')
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes])->sum('valor_final');
        $aReceber = (float) LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Pendente')->sum('valor_final');

        $totalClientes = Cliente::count();
        $totalPecas = Acervo::count();
        $pecasDisponiveis = Acervo::where('status', 'disponivel')->count();
        $locacoesAtivas = Locacao::where('status', '!=', 'cancelada')->count();

        $proximosEventos = Evento::where('status', 'Ativo')
            ->whereDate('data_evento', '>=', $hoje)->orderBy('data_evento')->limit(3)
            ->get()->map(fn ($e) => "- {$e->nome_evento} ({$e->tipo_evento}) em " . optional($e->data_evento)->format('d/m/Y'))
            ->implode("\n");

        $moeda = fn ($v) => 'R$ ' . number_format($v, 2, ',', '.');

        return implode("\n", array_filter([
            "Data de hoje: " . $hoje->format('d/m/Y'),
            "Provas/atendimentos hoje: {$provasHoje}; nos próximos 7 dias: {$provasSemana}",
            "Recebido no mês: {$moeda($recebidoMes)}; a receber: {$moeda($aReceber)}",
            "Clientes cadastrados: {$totalClientes}",
            "Peças no acervo: {$totalPecas} (disponíveis: {$pecasDisponiveis})",
            "Locações ativas: {$locacoesAtivas}",
            $proximosEventos ? "Próximos eventos:\n{$proximosEventos}" : "Sem eventos futuros cadastrados.",
        ]));
    }

    /**
     * Modo demonstração (sem chave de IA): responde perguntas comuns a partir
     * dos dados reais, por palavras-chave.
     */
    private function respostaDemo(Loja $loja, string $pergunta, string $contexto): string
    {
        $p = Str::lower($pergunta);

        $intro = "🤖 (modo demonstração — configure a chave de IA em Configurações para conversas completas)\n\n";

        if (Str::contains($p, ['prova', 'agenda', 'hoje', 'amanhã', 'semana'])) {
            $hoje = Carbon::today();
            $qtd = Agendamento::whereDate('data_hora', $hoje)->count();
            $semana = Agendamento::whereBetween('data_hora', [$hoje, $hoje->copy()->addDays(7)])->count();
            return $intro . "Hoje você tem {$qtd} atendimento(s) na agenda e {$semana} nos próximos 7 dias. Veja detalhes no menu Agenda.";
        }

        if (Str::contains($p, ['faturamento', 'receb', 'dinheiro', 'financeiro', 'caixa', 'quanto'])) {
            $inicioMes = Carbon::now()->startOfMonth();
            $fimMes = Carbon::now()->endOfMonth();
            $recebido = (float) LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Pago')
                ->whereBetween('data_vencimento', [$inicioMes, $fimMes])->sum('valor_final');
            $aReceber = (float) LancamentoFinanceiro::where('tipo', 'Receita')->where('status', 'Pendente')->sum('valor_final');
            return $intro . 'No mês você já recebeu R$ ' . number_format($recebido, 2, ',', '.')
                . ' e tem R$ ' . number_format($aReceber, 2, ',', '.') . ' a receber. Veja o Financeiro para detalhes.';
        }

        if (Str::contains($p, ['peça', 'peca', 'vestido', 'acervo', 'estoque', 'disponí', 'disponi'])) {
            $total = Acervo::count();
            $disp = Acervo::where('status', 'disponivel')->count();
            return $intro . "Seu acervo tem {$total} peça(s), sendo {$disp} disponível(is) para locação agora. Veja em Acervo/Estoque.";
        }

        if (Str::contains($p, ['cliente', 'noiva', 'quantos'])) {
            $total = Cliente::count();
            return $intro . "Você tem {$total} cliente(s) cadastrado(s). Veja a lista completa no menu Clientes.";
        }

        if (Str::contains($p, ['evento', 'casamento', 'próximo', 'proximo'])) {
            $evento = Evento::where('status', 'Ativo')->whereDate('data_evento', '>=', today())->orderBy('data_evento')->first();
            if ($evento) {
                $dias = (int) today()->diffInDays($evento->data_evento, false);
                return $intro . "Seu próximo evento é \"{$evento->nome_evento}\" em " . optional($evento->data_evento)->format('d/m/Y') . " (faltam {$dias} dias).";
            }
            return $intro . 'Não há eventos futuros cadastrados. Cadastre em Agenda/Eventos.';
        }

        // Resposta padrão: mostra o panorama.
        return $intro . "Aqui está um resumo atual da {$loja->nome_fantasia}:\n\n{$contexto}\n\n"
            . 'Você pode me perguntar sobre: provas de hoje, faturamento do mês, peças disponíveis, clientes ou próximos eventos.';
    }
}
