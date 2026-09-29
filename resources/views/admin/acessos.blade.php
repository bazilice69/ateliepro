@extends('admin.layout')

@section('titulo', 'Atividade / Acessos')

@section('conteudo')
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="py-4 pl-6">Data / Hora</th>
                    <th class="py-4">Usuário</th>
                    <th class="py-4">Loja</th>
                    <th class="py-4">Evento</th>
                    <th class="py-4">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3 pl-6 text-sm text-slate-600">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 text-sm font-semibold text-slate-800">{{ $log->user_nome ?? '—' }}</td>
                        <td class="py-3 text-sm text-slate-600">{{ $log->loja_nome ?? 'Super Admin' }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">{{ $log->evento }}</span>
                        </td>
                        <td class="py-3 text-sm text-slate-500 font-mono">{{ $log->ip ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-16 text-center text-slate-400 font-medium">Nenhum acesso registrado ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
@endsection
