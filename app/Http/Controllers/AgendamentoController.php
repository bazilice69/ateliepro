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

        return view('agenda.index', compact(
            'agendamentosHoje',
            'clientesComPendencia',
            'cards',
            'eventoDossie',
            'clientes',
            'produtos',
            'eventos'
        ));
    }
}
