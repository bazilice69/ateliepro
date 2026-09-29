<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 uppercase tracking-tighter">Configurações da <span class="text-[#fbbf24]">Loja</span></h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl font-medium text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('loja.config.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')

                <!-- Identidade visual -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                    <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-4">Identidade Visual</h3>
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="shrink-0 text-center">
                            <div class="w-28 h-28 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden">
                                @if($loja->logo)
                                    <img src="{{ asset('storage/'.$loja->logo) }}" alt="Logo" class="w-full h-full object-contain">
                                @else
                                    <i class="fas fa-image text-3xl text-slate-300"></i>
                                @endif
                            </div>
                            @if($loja->logo)
                                <button form="formRemoverLogo" class="text-[10px] text-rose-500 hover:text-rose-700 font-bold uppercase mt-2">Remover logo</button>
                            @endif
                        </div>
                        <div class="flex-1 w-full">
                            <label class="text-[10px] font-black uppercase text-slate-400">Logo da loja</label>
                            <input type="file" name="logo" accept="image/*" class="block w-full mt-1 text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-900 file:text-[#fbbf24] file:font-bold file:text-xs file:uppercase hover:file:bg-slate-800 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-2">JPG, PNG, WEBP ou GIF — até 5MB. Aparece no topo do sistema e nos contratos.</p>
                        </div>
                    </div>
                </div>

                <!-- Dados da loja -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider mb-2">Dados da Loja</h3>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Nome Fantasia *</label>
                        <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia', $loja->nome_fantasia) }}" required class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Razão Social</label>
                        <input type="text" name="razao_social" value="{{ old('razao_social', $loja->razao_social) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">CNPJ / CPF</label>
                        <input type="text" name="cnpj_cpf" value="{{ old('cnpj_cpf', $loja->cnpj_cpf) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Inscrição Estadual</label>
                        <input type="text" name="inscricao_estadual" value="{{ old('inscricao_estadual', $loja->inscricao_estadual) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Telefone / WhatsApp</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $loja->telefone) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">E-mail</label>
                        <input type="email" name="email_responsavel" value="{{ old('email_responsavel', $loja->email_responsavel) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black uppercase text-slate-400">Endereço</label>
                        <input type="text" name="endereco" value="{{ old('endereco', $loja->endereco) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400">Cidade</label>
                        <input type="text" name="cidade" value="{{ old('cidade', $loja->cidade) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">UF</label>
                            <input type="text" name="estado" maxlength="2" value="{{ old('estado', $loja->estado) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700 uppercase">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep', $loja->cep) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        </div>
                    </div>
                </div>

                <button class="w-full sm:w-auto bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Configurações</button>
            </form>

            <form id="formRemoverLogo" method="POST" action="{{ route('loja.config.logo.remover') }}" class="hidden">
                @csrf @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
