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
            <h3 class="font-black text-slate-800 uppercase text-sm tracking-wider">Mercado Pago</h3>
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

        <button class="bg-slate-900 text-[#fbbf24] px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">Salvar Configurações</button>
    </form>
@endsection
