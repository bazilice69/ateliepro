<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">{{ $contrato->titulo }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('contratos.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 uppercase tracking-widest self-center">← Voltar</a>
                <a href="{{ route('contratos.imprimir', $contrato) }}" target="_blank" class="bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">
                    <i class="fas fa-print mr-1"></i> Imprimir / PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            <div class="bg-white rounded-3xl p-10 shadow-sm border border-slate-100">
                <pre class="whitespace-pre-wrap font-sans text-sm text-slate-700 leading-relaxed">{{ $contrato->conteudo_final }}</pre>
            </div>
            <div class="mt-4 flex justify-end">
                <form method="POST" action="{{ route('contratos.destroy', $contrato) }}" onsubmit="return confirm('Remover este contrato?')">
                    @csrf @method('DELETE')
                    <button class="text-xs font-bold text-rose-500 hover:text-rose-700 uppercase">Remover contrato</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
