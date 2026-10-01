@extends('admin.layout')

@section('titulo', 'Configurações do Sistema')

@section('conteudo')
    <form method="POST" action="{{ route('admin.config.update') }}" class="max-w-2xl space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
            <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">Identidade & Suporte</h3>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Nome do sistema</label>
                <input type="text" name="saas_nome" value="{{ old('saas_nome', $config['saas_nome']) }}" placeholder="AteliêPro" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                <p class="text-[11px] text-slate-400 mt-1">Este nome aparece no topo, no login, nos títulos e nos e-mails do sistema. Ex.: <strong>Bonacci Rental</strong>.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">E-mail de suporte</label>
                    <input type="email" name="saas_email_suporte" value="{{ old('saas_email_suporte', $config['saas_email_suporte']) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">WhatsApp de suporte</label>
                    <input type="text" name="saas_whatsapp_suporte" value="{{ old('saas_whatsapp_suporte', $config['saas_whatsapp_suporte']) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
            <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">Pagamentos</h3>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Gateway ativo</label>
                <select name="gateway_ativo" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                    @php $gwAtual = $config['gateway_ativo'] ?? 'mercadopago'; @endphp
                    @foreach(\App\Support\PaymentGateways::CATALOGO as $chave => $gw)
                        <option value="{{ $chave }}" @selected($gwAtual === $chave)>{{ $gw['nome'] }}{{ $chave === 'mercadopago' ? '' : ' (em breve)' }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Hoje o <strong>Mercado Pago</strong> processa de verdade. Os outros já aparecem como selos de confiança no checkout e serão ativados conforme as credenciais forem cadastradas.</p>
            </div>
            <h4 class="font-black text-slate-700 text-xs uppercase tracking-wider pt-2 border-t border-slate-100">Credenciais do Mercado Pago</h4>
            <p class="text-xs text-slate-400">Deixe em branco para manter o valor atual. As credenciais ficam no servidor.</p>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Access Token</label>
                <input type="password" name="mercadopago_access_token" placeholder="{{ $config['mercadopago_access_token'] ? '•••••••• (configurado)' : 'APP_USR-...' }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Public Key</label>
                <input type="text" name="mercadopago_public_key" value="{{ old('mercadopago_public_key', $config['mercadopago_public_key']) }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Webhook Secret (opcional)</label>
                <input type="password" name="mercadopago_webhook_secret" placeholder="{{ $config['mercadopago_webhook_secret'] ? '•••••••• (configurado)' : '' }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700">
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
            <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">Assistente de IA</h3>
            <p class="text-xs text-slate-400">Sem uma chave configurada, o assistente funciona em <strong>modo demonstração</strong> (responde com base nos dados da loja). Com a chave, ele passa a conversar de verdade.</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Provedor</label>
                    <select name="ia_provedor" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                        @foreach(['openai' => 'OpenAI (ChatGPT)', 'anthropic' => 'Anthropic (Claude)', 'gemini' => 'Google (Gemini)'] as $val => $label)
                            <option value="{{ $val }}" @selected(($config['ia_provedor'] ?? 'openai') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400">Modelo (opcional)</label>
                    <input type="text" name="ia_modelo" value="{{ old('ia_modelo', $config['ia_modelo']) }}" placeholder="ex: gpt-4o-mini" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">API Key</label>
                <input type="password" name="ia_api_key" placeholder="{{ $config['ia_api_key'] ? '•••••••• (configurada)' : 'cole a chave do provedor' }}" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-mono text-sm text-slate-700">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400">Nome da assistente</label>
                <input type="text" name="ia_nome_assistente" value="{{ old('ia_nome_assistente', $config['ia_nome_assistente']) }}" placeholder="Valentina" class="w-full mt-1 p-3 bg-slate-50 border-none rounded-xl font-semibold text-slate-700">
                <p class="text-[11px] text-slate-400 mt-1">O nome que aparece no chat flutuante (padrão: Valentina).</p>
            </div>
        </div>

        <button class="bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Configurações</button>
    </form>
@endsection
