@extends('admin.layout')

@section('titulo', 'Novos Cadastros')

@section('conteudo')
    <p class="text-sm text-slate-500 mb-6">Lojas que se cadastraram no AteliêPro (mais recentes primeiro).</p>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="py-4 pl-6">Data</th>
                    <th class="py-4">Loja</th>
                    <th class="py-4">Responsável</th>
                    <th class="py-4">Contato</th>
                    <th class="py-4 pr-6 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($cadastros as $c)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 pl-6 text-sm text-slate-600">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-4 font-bold text-slate-800 text-sm">{{ $c->loja_nome ?? '—' }}</td>
                        <td class="py-4 text-sm text-slate-600">{{ $c->user_nome ?? '—' }}</td>
                        <td class="py-4 text-sm text-slate-600">{{ $c->loja->email_responsavel ?? '—' }}{{ $c->loja && $c->loja->telefone ? ' · '.$c->loja->telefone : '' }}</td>
                        <td class="py-4 pr-6 text-right">
                            @if($c->loja)
                                <a href="{{ route('admin.lojas.show', $c->loja) }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 uppercase">Ver loja →</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-16 text-center text-slate-400 font-medium">Nenhum cadastro ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $cadastros->links() }}</div>
@endsection
