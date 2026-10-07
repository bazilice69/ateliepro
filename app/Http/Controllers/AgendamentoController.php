<?php

namespace App\Http\Controllers;

use App\Models\Acervo;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\LancamentoFinanceiro;
use Illuminate\Support\Carbon;

class AgendamentoController extends Controller
{
    /** Status financeiros que contam como "em aberto". */
    private const STATUS_PENDENTES = ['Pendente', 'Vencido', 'Parcialmente Pago'];

    public function index()
    {
        $hoje = Carbon::today();

        // ===== Agendamentos de HOJE (com tudo conectado) =====
        $agendamentosHoje = Agendamento::with(['cliente', 'evento', 'acervo'])
            ->whereDate('data_hora', $hoje)
            ->orderBy('data_hora')
            ->get();

        // Clientes com pendência financeira em aberto (para marcar na lista
        // e contar no card "R$ Pendente"). Cruza o módulo Financeiro.
        $clientesComPendencia = LancamentoFinanceiro::query()
            ->whereIn('status', self::STATUS_PENDENTES)
            ->whereNotNull('cliente_id')
            ->pluck('cliente_id')
            ->unique();

        // ===== Cards de resumo (dados REAIS) =====
        $ehAjuste = fn ($tipo) => str_contains(mb_strtolower((string) $tipo), 'ajuste');
        $ehProva  = fn ($tipo) => str_contains(mb_strtolower((string) $tipo), 'prova');

        $cards = [
            // Total de atendimentos do dia
            'atendimentos' => $agendamentosHoje->count(),
            // Provas do dia (qualquer tipo que contenha "prova")
            'provas' => $agendamentosHoje->filter(fn ($a) => $ehProva($a->tipo_atendimento))->count(),
            // Ajustes do dia
            'ajustes' => $agendamentosHoje->filter(fn ($a) => $ehAjuste($a->tipo_atendimento))->count(),
            // Atrasos: horário do dia já passou e não está marcado como concluído (verde)
            'atrasos' => $agendamentosHoje->filter(
                fn ($a) => $a->data_hora->isPast() && $a->status_cor !== 'green'
            )->count(),
            // R$ Pendente: clientes com agendamento hoje que estão devendo
            'pendentes' => $agendamentosHoje
                ->filter(fn ($a) => $a->cliente_id && $clientesComPendencia->contains($a->cliente_id))
                ->pluck('cliente_id')->unique()->count(),
        ];

        // ===== Dossiê: o próximo evento ativo (timeline montada dos dados) =====
        $eventoDossie = Evento::with(['agendamentos' => fn ($q) => $q->with('cliente')->orderBy('data_hora')])
            ->where('status', 'Ativo')
            ->orderBy('data_evento')
            ->first();

        // ===== Listas para o modal "Novo Agendamento" =====
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Acervo::where('status', 'disponivel')->orderBy('nome')->get();
        $eventos  = Evento::where('status', 'Ativo')->get();

        // ===== Eventos para o CALENDÁRIO (FullCalendar) =====
        $eventosCalendario = $this->montarEventosCalendario($clientesComPendencia);

        return view('agenda.index', compact(
            'agendamentosHoje',
            'clientesComPendencia',
            'cards',
            'eventoDossie',
            'clientes',
            'produtos',
            'eventos',
            'eventosCalendario'
        ));
    }

    /**
     * Monta a lista de eventos para o calendário, com uma categoria/cor por tipo
     * (atendimento, prova, locação, confecção, aniversário). Cada módulo "conversa"
     * com o calendário aqui.
     */
    private function montarEventosCalendario($clientesComPendencia): array
    {
        $eventos = [];

        // Cores por categoria (usadas tanto na pílula quanto nos filtros).
        $cores = [
            'prova'       => '#3b82f6', // azul
            'retirada'    => '#10b981', // verde
            'devolucao'   => '#f59e0b', // âmbar
            'ajuste'      => '#a855f7', // roxo
            'atendimento' => '#64748b', // cinza
            'aniversario' => '#ec4899', // rosa
        ];

        // --- Agendamentos (provas, retiradas, ajustes, atendimentos) ---
        $agendamentos = Agendamento::with(['cliente', 'evento'])->get();
        foreach ($agendamentos as $ag) {
            $tipoLower = mb_strtolower((string) $ag->tipo_atendimento);
            $cat = match (true) {
                str_contains($tipoLower, 'prova')     => 'prova',
                str_contains($tipoLower, 'retirada')  => 'retirada',
                str_contains($tipoLower, 'devolu')    => 'devolucao',
                str_contains($tipoLower, 'ajuste')    => 'ajuste',
                default                                => 'atendimento',
            };
            $temPendencia = $ag->cliente_id && $clientesComPendencia->contains($ag->cliente_id);

            $eventos[] = [
                'id'    => 'ag-'.$ag->id,
                'title' => ($ag->cliente?->nome ?? 'Cliente').' · '.$ag->tipo_atendimento.($temPendencia ? ' 💰' : ''),
                'start' => $ag->data_hora->toIso8601String(),
                'color' => $cores[$cat],
                'extendedProps' => [
                    'categoria' => $cat,
                    'cliente'   => $ag->cliente?->nome,
                    'evento'    => $ag->evento?->nome_evento,
                    'sala'      => $ag->sala_atendimento,
                    'url'       => $ag->cliente_id ? route('clientes.show', $ag->cliente_id) : null,
                ],
            ];
        }

        // --- Aniversários das clientes (recorrentes, no mês atual e nos próximos) ---
        $clientesComNascimento = Cliente::whereNotNull('data_nascimento')->get();
        foreach ($clientesComNascimento as $cli) {
            try {
                $nasc = \Illuminate\Support\Carbon::parse($cli->data_nascimento);
            } catch (\Throwable $e) {
                continue;
            }
            // Aniversário neste ano (o FullCalendar mostra na data certa do mês).
            $aniversarioEsteAno = $nasc->copy()->year(now()->year)->format('Y-m-d');
            $eventos[] = [
                'id'    => 'bday-'.$cli->id,
                'title' => '🎂 '.$cli->nome,
                'start' => $aniversarioEsteAno,
                'allDay' => true,
                'color' => $cores['aniversario'],
                'extendedProps' => [
                    'categoria' => 'aniversario',
                    'cliente'   => $cli->nome,
                    'url'       => route('clientes.show', $cli->id),
                ],
            ];
        }

        return $eventos;
    }
}
