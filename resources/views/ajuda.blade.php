<x-app-layout>
    <div class="py-8" x-data="{ aberto: 'fluxo' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Cabeçalho -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-[2rem] p-6 sm:p-8 text-white relative overflow-hidden">
                <i class="fas fa-graduation-cap absolute -right-3 -bottom-3 text-7xl text-white/5"></i>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#fbbf24]">Central de Ajuda</p>
                <h2 class="font-black text-2xl sm:text-3xl tracking-tight mt-1">Como usar o {{ \App\Models\Setting::nomeSistema() }}</h2>
                <p class="text-sm text-slate-300 mt-2 max-w-xl">Um guia simples e direto. Clique em cada tópico para abrir. Se ficar com qualquer dúvida, fale com a gente pelo menu <strong>Fale Conosco</strong>. 💛</p>
            </div>

            @php
                // Cada item: [id, ícone, título, subtítulo]
                $topicos = [
                    ['fluxo', 'fa-wand-magic-sparkles', 'Passo a passo: uma locação do início ao fim', 'O caminho completo — comece por aqui'],
                    ['agenda', 'fa-calendar-alt', 'Agenda', 'Veja o dia do ateliê de relance'],
                    ['clientes', 'fa-users', 'Clientes', 'Cadastro, medidas e histórico'],
                    ['acervo', 'fa-tshirt', 'Acervo / Estoque', 'Suas peças com fotos e status'],
                    ['locacoes', 'fa-file-contract', 'Locações', 'O coração do dia a dia'],
                    ['provas', 'fa-ruler-combined', 'Provas', 'Marque e registre o resultado'],
                    ['oficina', 'fa-scissors', 'Oficina', 'Ajustes, lavanderia e manutenção'],
                    ['encomendas', 'fa-pen-ruler', 'Sob Medida', 'Peças feitas do zero'],
                    ['contratos', 'fa-file-signature', 'Contratos', 'Gere a partir do seu modelo'],
                    ['financeiro', 'fa-dollar-sign', 'Financeiro', 'Entradas, saídas e calculadora'],
                    ['config', 'fa-gear', 'Configurações & Vitrine', 'Logo, equipe e divulgação'],
                    ['faq', 'fa-circle-question', 'Perguntas frequentes', 'Dúvidas rápidas'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach($topicos as [$id, $icone, $titulo, $sub])
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <button type="button" @click="aberto = (aberto === '{{ $id }}' ? '' : '{{ $id }}')"
                                class="w-full flex items-center gap-4 p-5 text-left hover:bg-slate-50/60 transition">
                            <div class="w-11 h-11 rounded-2xl bg-[#fbbf24]/15 text-[#c9a24b] flex items-center justify-center shrink-0">
                                <i class="fas {{ $icone }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-black text-slate-800">{{ $titulo }}</h3>
                                <p class="text-xs text-slate-400 font-medium">{{ $sub }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-slate-300 transition-transform" :class="aberto === '{{ $id }}' ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="aberto === '{{ $id }}'" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-5 pb-6 pt-0 text-sm text-slate-600 leading-relaxed space-y-3">
                            <div class="border-t border-slate-100 pt-4"></div>

                            @switch($id)
                                @case('fluxo')
                                    <p class="font-bold text-slate-800">É mais simples do que parece. Este é o caminho que você vai repetir no dia a dia:</p>
                                    <ol class="space-y-3">
                                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-slate-900 text-[#fbbf24] text-xs font-black flex items-center justify-center">1</span><span><strong>Cadastre a cliente</strong> em <em>Clientes → Novo Cliente</em> (nome, telefone, e já pode anotar as medidas).</span></li>
                                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-slate-900 text-[#fbbf24] text-xs font-black flex items-center justify-center">2</span><span><strong>Tenha a peça no acervo</strong> em <em>Acervo → Nova Peça</em> (com foto, isso faz diferença!).</span></li>
                                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-slate-900 text-[#fbbf24] text-xs font-black flex items-center justify-center">3</span><span><strong>Crie a locação</strong> em <em>Locações → Nova Locação</em>: escolha a cliente, a peça e as datas (retirada, evento, devolução). O sistema avisa se a peça já estiver reservada nessas datas.</span></li>
                                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-slate-900 text-[#fbbf24] text-xs font-black flex items-center justify-center">4</span><span><strong>Marque as provas</strong> e, se precisar ajuste, mande pra <em>Oficina</em>.</span></li>
                                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-slate-900 text-[#fbbf24] text-xs font-black flex items-center justify-center">5</span><span><strong>Gere o contrato</strong> (ele se preenche sozinho com os dados da locação).</span></li>
                                        <li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-slate-900 text-[#fbbf24] text-xs font-black flex items-center justify-center">6</span><span>No dia, registre a <strong>Retirada</strong> (com checklist e fotos). Depois do evento, a <strong>Devolução</strong> com a vistoria.</span></li>
                                    </ol>
                                    <p class="bg-emerald-50 text-emerald-700 rounded-xl p-3 text-xs font-medium"><i class="fas fa-lightbulb mr-1"></i> O dinheiro (sinal e saldo) entra <strong>sozinho</strong> no Financeiro quando você cria a locação e registra a retirada. Você não digita duas vezes.</p>
                                    @break

                                @case('agenda')
                                    <p>A <strong>Agenda</strong> mostra o que acontece hoje no ateliê: provas marcadas, retiradas, devoluções e ajustes pendentes. É a sua visão rápida do dia — ótima pra abrir de manhã (ou no celular antes de chegar na loja).</p>
                                    @break

                                @case('clientes')
                                    <p>Em <strong>Clientes</strong> você cadastra cada noiva/cliente com nome, contato e endereço. Ao abrir a ficha da cliente, você pode:</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Anotar as <strong>medidas</strong> — e cada vez que medir de novo, o sistema guarda o histórico (você vê a evolução).</li>
                                        <li>Ver todas as <strong>locações e provas</strong> daquela cliente num lugar só.</li>
                                    </ul>
                                    <p class="text-xs text-slate-400"><i class="fas fa-bolt mr-1"></i> Dica: dá pra cadastrar uma cliente rapidinho na hora de criar a locação, sem sair da tela.</p>
                                    @break

                                @case('acervo')
                                    <p>O <strong>Acervo</strong> é o seu estoque de peças (vestidos, ternos, acessórios). Cada peça tem código, categoria, tamanho, valor e <strong>fotos</strong>.</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Capriche nas <strong>fotos</strong>: elas aparecem no catálogo, na etiqueta e na vitrine pública.</li>
                                        <li>O <strong>status</strong> da peça muda sozinho (disponível, reservada, alugada, lavanderia...) conforme você opera.</li>
                                        <li>Dá pra gerar um <strong>catálogo em PDF</strong> e uma <strong>etiqueta com QR Code</strong> pra cada peça.</li>
                                    </ul>
                                    @break

                                @case('locacoes')
                                    <p>As <strong>Locações</strong> são o centro de tudo. Ao criar uma, você define cliente, peça e as 3 datas: <strong>retirada</strong>, <strong>evento</strong> e <strong>devolução</strong>.</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>O sistema <strong>trava datas em conflito</strong> — não deixa alugar a mesma peça pro mesmo período (inclui uns dias de lavanderia).</li>
                                        <li>Na ficha da locação você acompanha tudo: provas, ajustes, retirada e devolução.</li>
                                        <li>Informe o <strong>sinal</strong> e a <strong>caução</strong>; o restante o sistema calcula.</li>
                                    </ul>
                                    @break

                                @case('provas')
                                    <p>Em <strong>Provas</strong> você marca a data e, depois, registra o resultado: <strong>aprovada</strong> ou <strong>precisa de ajuste</strong>. Se precisar ajuste, já dá pra abrir o serviço na Oficina e marcar a próxima prova.</p>
                                    @break

                                @case('oficina')
                                    <p>A <strong>Oficina</strong> é a fila de trabalho: <strong>ajustes</strong> (costura), <strong>lavanderia</strong> e <strong>manutenção</strong>. Cada serviço tem um status (solicitado → em andamento → pronto). Quando conclui, a peça volta a ficar disponível automaticamente.</p>
                                    <p class="text-xs text-slate-400"><i class="fas fa-bolt mr-1"></i> Na devolução, se a peça voltar com mancha ou dano, o sistema já joga ela pra lavanderia/manutenção sozinho.</p>
                                    @break

                                @case('encomendas')
                                    <p>Use <strong>Sob Medida</strong> para peças feitas do zero. Você acompanha as etapas de produção (modelagem → corte → costura → prova → acabamento → pronta), guarda o <strong>croqui</strong> e o histórico de medidas a cada prova.</p>
                                    @break

                                @case('contratos')
                                    <p>Primeiro, o dono da loja cadastra um <strong>modelo de contrato</strong> (uma vez só) em <em>Configurações → Modelos de contrato</em>. Depois, em <strong>Contratos</strong>, você gera o contrato de uma locação e ele <strong>se preenche sozinho</strong> com os dados do cliente e da locação.</p>
                                    <p class="text-xs text-slate-400"><i class="fas fa-lock mr-1"></i> O contrato gerado fica "congelado": se você editar o modelo depois, os contratos antigos não mudam.</p>
                                    @break

                                @case('financeiro')
                                    <p>No <strong>Financeiro</strong> você vê tudo que entra e sai, com os totais do mês (recebido, a receber, em atraso, saldo).</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>As receitas das <strong>locações entram sozinhas</strong> (sinal, saldo, multa, caução retida) — vêm com o selo <strong>Auto</strong>.</li>
                                        <li>Em <strong>Despesas Fixas</strong>, cadastre contas que repetem todo mês (aluguel, luz, internet) e o sistema lança sozinho.</li>
                                        <li>Use a <strong>Calculadora de Preço</strong> pra descobrir quanto cobrar (material + mão de obra + despesas + sua margem).</li>
                                    </ul>
                                    @break

                                @case('config')
                                    <p>Em <strong>Configurações</strong> (só o dono da loja), você:</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Sobe a sua <strong>logo</strong> e os dados da loja (aparecem no sistema e nos contratos).</li>
                                        <li>Cadastra sua <strong>equipe</strong> (funcionárias) e define o que cada uma pode ver.</li>
                                        <li>Liga a <strong>Vitrine pública</strong>: sua loja aparece de graça na página inicial do sistema, divulgando seu acervo — quem gostar fala direto com você.</li>
                                    </ul>
                                    @break

                                @case('faq')
                                    <div class="space-y-3">
                                        <div><p class="font-bold text-slate-800">Posso usar no celular?</p><p>Sim! O sistema funciona no computador e no celular. Dá até pra instalar como aplicativo (aparece a opção de "instalar" no navegador).</p></div>
                                        <div><p class="font-bold text-slate-800">Esqueci de cadastrar a peça antes da locação.</p><p>Sem problema — dá pra cadastrar a peça e a cliente rapidinho na própria tela de nova locação.</p></div>
                                        <div><p class="font-bold text-slate-800">O financeiro está diferente do que esperava.</p><p>Lembre que as receitas da locação entram automáticas (selo <strong>Auto</strong>). Você só lança manualmente o que não vem de uma locação (ex.: uma despesa avulsa).</p></div>
                                        <div><p class="font-bold text-slate-800">Preciso de ajuda com algo que não está aqui.</p><p>Use o menu <strong>Fale Conosco</strong> — sua mensagem vai direto pro nosso WhatsApp. 💬</p></div>
                                    </div>
                                    @break
                            @endswitch
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Rodapé: CTA pro suporte -->
            <div class="bg-white rounded-2xl border border-slate-100 p-6 text-center">
                <p class="text-slate-500 text-sm">Não achou o que procurava?</p>
                <a href="{{ route('fale-conosco') }}" class="inline-flex items-center gap-2 mt-3 bg-slate-900 text-[#fbbf24] px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition">
                    <i class="fas fa-comment-dots"></i> Falar com o suporte
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
