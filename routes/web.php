<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LocacaoController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedidaController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPlanoController;
use App\Http\Controllers\AdminConfigController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\LojaBloqueioController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\DespesaRecorrenteController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ModeloContratoController;
use App\Http\Controllers\AssistenteController;
use App\Http\Controllers\LojaConfigController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProvaController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Legado: tela de licença por máquina (mantida, mas fora do fluxo SaaS) ---
Route::get('/licenca-expirada', [LicenseController::class, 'index'])->name('licenca.tela');
Route::post('/licenca-ativar', [LicenseController::class, 'ativar'])->name('licenca.ativar');

// Tela de aviso quando a LOJA está inadimplente/bloqueada (modelo SaaS).
Route::middleware('auth')->get('/loja-bloqueada', [LojaBloqueioController::class, 'index'])->name('loja.bloqueada');

// ==========================================================================
// PAINEL DO SUPER ADMIN (dono do SaaS) — só super_admin
// ==========================================================================
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/acessos', [AdminController::class, 'acessos'])->name('acessos');
    Route::get('/relatorios', [AdminController::class, 'relatorios'])->name('relatorios');
    Route::get('/relatorios/imprimir', [AdminController::class, 'relatoriosImprimir'])->name('relatorios.imprimir');
    Route::get('/relatorios/exportar', [AdminController::class, 'relatoriosExportar'])->name('relatorios.exportar');
    Route::get('/lojas/{loja}', [AdminController::class, 'show'])->name('lojas.show');
    Route::put('/lojas/{loja}/plano', [AdminController::class, 'updatePlano'])->name('lojas.plano');
    Route::put('/lojas/{loja}/status', [AdminController::class, 'updateStatus'])->name('lojas.status');
    Route::post('/lojas/{loja}/estender', [AdminController::class, 'estenderVencimento'])->name('lojas.estender');
    Route::post('/lojas/{loja}/redefinir-senha', [AdminController::class, 'redefinirSenha'])->name('lojas.senha');
    Route::get('/lojas/{loja}/entrar', [AdminController::class, 'entrarComo'])->name('lojas.entrar');

    // Gestão de Planos do SaaS
    Route::get('/planos', [AdminPlanoController::class, 'index'])->name('planos.index');
    Route::get('/planos/novo', [AdminPlanoController::class, 'create'])->name('planos.create');
    Route::post('/planos', [AdminPlanoController::class, 'store'])->name('planos.store');
    Route::get('/planos/{plano}/editar', [AdminPlanoController::class, 'edit'])->name('planos.edit');
    Route::put('/planos/{plano}', [AdminPlanoController::class, 'update'])->name('planos.update');
    Route::delete('/planos/{plano}', [AdminPlanoController::class, 'destroy'])->name('planos.destroy');

    // Configurações globais do SaaS
    Route::get('/config', [AdminConfigController::class, 'edit'])->name('config.edit');
    Route::put('/config', [AdminConfigController::class, 'update'])->name('config.update');

    // Minha Conta do super admin (nome/email/senha)
    Route::get('/conta', [AdminController::class, 'conta'])->name('conta');
    Route::put('/conta', [AdminController::class, 'contaUpdate'])->name('conta.update');

    // Cadastros recentes de lojas
    Route::get('/cadastros', [AdminController::class, 'cadastros'])->name('cadastros');
});

// ==========================================================================
// ÁREA DA LOJA — exige login + loja ativa (verificação por loja, não por máquina)
// ==========================================================================
Route::middleware(['auth', 'lojaativa'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/novo', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');

    // Salvar Medidas
    Route::post('/clientes/{id}/medidas', [MedidaController::class, 'store'])->name('medidas.store');

    // Módulo de Acervo
    Route::get('/acervo', [ProdutoController::class, 'index'])->name('acervo.index');
    Route::get('/acervo/novo', [ProdutoController::class, 'create'])->name('produto.create');
    Route::post('/acervo', [ProdutoController::class, 'store'])->name('produto.store');
    Route::get('/acervo/{peca}', [ProdutoController::class, 'show'])->name('acervo.show');

    Route::get('/agenda', [AgendamentoController::class, 'index'])->name('agenda.index');

    // Módulo de Locações (central de operação)
    Route::get('/locacoes', [LocacaoController::class, 'index'])->name('locacao.index');
    Route::get('/locacoes/nova', [LocacaoController::class, 'create'])->name('locacao.create');
    Route::post('/locacoes/salvar', [LocacaoController::class, 'store'])->name('locacao.store');
    Route::get('/locacoes/{locacao}', [LocacaoController::class, 'show'])->name('locacao.show');
    Route::put('/locacoes/{locacao}/status', [LocacaoController::class, 'updateStatus'])->name('locacao.status');
    Route::post('/locacoes/{locacao}/retirar', [LocacaoController::class, 'retirar'])->name('locacao.retirar');
    Route::post('/locacoes/{locacao}/devolver', [LocacaoController::class, 'devolver'])->name('locacao.devolver');

    // Provas
    Route::get('/provas', [ProvaController::class, 'index'])->name('provas.index');
    Route::get('/provas/nova', [ProvaController::class, 'create'])->name('provas.create');
    Route::post('/provas', [ProvaController::class, 'store'])->name('provas.store');
    Route::put('/provas/{prova}/registrar', [ProvaController::class, 'registrar'])->name('provas.registrar');
    Route::delete('/provas/{prova}', [ProvaController::class, 'destroy'])->name('provas.destroy');

    // Oficina: ajustes / lavanderia / manutenção
    Route::get('/oficina', [ServicoController::class, 'index'])->name('servicos.index');
    Route::get('/oficina/novo', [ServicoController::class, 'create'])->name('servicos.create');
    Route::post('/oficina', [ServicoController::class, 'store'])->name('servicos.store');
    Route::put('/oficina/{servico}/status', [ServicoController::class, 'updateStatus'])->name('servicos.status');
    Route::delete('/oficina/{servico}', [ServicoController::class, 'destroy'])->name('servicos.destroy');

    // Encomendas / Sob Medida (peças criadas do zero)
    Route::get('/encomendas', [EncomendaController::class, 'index'])->name('encomendas.index');
    Route::get('/encomendas/nova', [EncomendaController::class, 'create'])->name('encomendas.create');
    Route::post('/encomendas', [EncomendaController::class, 'store'])->name('encomendas.store');
    Route::get('/encomendas/{encomenda}', [EncomendaController::class, 'show'])->name('encomendas.show');
    Route::put('/encomendas/{encomenda}', [EncomendaController::class, 'update'])->name('encomendas.update');
    Route::put('/encomendas/{encomenda}/etapa', [EncomendaController::class, 'updateEtapa'])->name('encomendas.etapa');
    Route::post('/encomendas/{encomenda}/medida', [EncomendaController::class, 'registrarMedida'])->name('encomendas.medida');

    // Módulo Financeiro
    Route::get('/financeiro', [FinanceiroController::class, 'index'])->name('financeiro.index');
    Route::post('/financeiro/lancamento', [FinanceiroController::class, 'store'])->name('financeiro.store');

    // Despesas recorrentes / fixas
    Route::get('/financeiro/recorrentes', [DespesaRecorrenteController::class, 'index'])->name('recorrentes.index');
    Route::post('/financeiro/recorrentes', [DespesaRecorrenteController::class, 'store'])->name('recorrentes.store');
    Route::put('/financeiro/recorrentes/{recorrente}', [DespesaRecorrenteController::class, 'update'])->name('recorrentes.update');
    Route::delete('/financeiro/recorrentes/{recorrente}', [DespesaRecorrenteController::class, 'destroy'])->name('recorrentes.destroy');
    Route::post('/financeiro/recorrentes/gerar', [DespesaRecorrenteController::class, 'gerarAgora'])->name('recorrentes.gerar');

    // Relatórios da loja
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/exportar', [RelatorioController::class, 'exportar'])->name('relatorios.exportar');
    Route::get('/relatorios/imprimir', [RelatorioController::class, 'imprimir'])->name('relatorios.imprimir');

    // Assistente de IA
    Route::get('/assistente', [AssistenteController::class, 'index'])->name('assistente.index');
    Route::post('/assistente/enviar', [AssistenteController::class, 'enviar'])->name('assistente.enviar');
    Route::post('/assistente/limpar', [AssistenteController::class, 'limpar'])->name('assistente.limpar');
    Route::post('/assistente/conversar', [AssistenteController::class, 'conversar'])->name('assistente.conversar');
    Route::post('/assistente/whatsapp', [AssistenteController::class, 'gerarWhatsapp'])->name('assistente.whatsapp');

    // Contratos (gerar, listar, visualizar) — disponível para a equipe da loja
    Route::get('/contratos', [ContratoController::class, 'index'])->name('contratos.index');
    Route::get('/contratos/novo', [ContratoController::class, 'create'])->name('contratos.create');
    Route::post('/contratos/preview', [ContratoController::class, 'preview'])->name('contratos.preview');
    Route::post('/contratos', [ContratoController::class, 'store'])->name('contratos.store');
    Route::get('/contratos/{contrato}', [ContratoController::class, 'show'])->name('contratos.show');
    Route::get('/contratos/{contrato}/imprimir', [ContratoController::class, 'imprimir'])->name('contratos.imprimir');
    Route::delete('/contratos/{contrato}', [ContratoController::class, 'destroy'])->name('contratos.destroy');

    // Gestão de Funcionários (apenas admin_loja / super_admin)
    Route::middleware('adminloja')->group(function () {
        // Configurações da própria loja (dados + logo)
        Route::get('/config-loja', [LojaConfigController::class, 'edit'])->name('loja.config.edit');
        Route::put('/config-loja', [LojaConfigController::class, 'update'])->name('loja.config.update');
        Route::delete('/config-loja/logo', [LojaConfigController::class, 'removerLogo'])->name('loja.config.logo.remover');

        // Modelos de contrato (só o admin da loja gerencia os modelos)
        Route::get('/contratos-modelos', [ModeloContratoController::class, 'index'])->name('contratos.modelos.index');
        Route::get('/contratos-modelos/novo', [ModeloContratoController::class, 'create'])->name('contratos.modelos.create');
        Route::post('/contratos-modelos', [ModeloContratoController::class, 'store'])->name('contratos.modelos.store');
        Route::get('/contratos-modelos/{modelo}/editar', [ModeloContratoController::class, 'edit'])->name('contratos.modelos.edit');
        Route::put('/contratos-modelos/{modelo}', [ModeloContratoController::class, 'update'])->name('contratos.modelos.update');
        Route::delete('/contratos-modelos/{modelo}', [ModeloContratoController::class, 'destroy'])->name('contratos.modelos.destroy');

        Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
        Route::get('/funcionarios/novo', [FuncionarioController::class, 'create'])->name('funcionarios.create');
        Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
        Route::get('/funcionarios/{user}/editar', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
        Route::put('/funcionarios/{user}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
        Route::delete('/funcionarios/{user}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
    });

});

require __DIR__.'/auth.php';

// ==========================================================================
// CHECKOUT / ASSINATURA (Mercado Pago)
// ==========================================================================
// Escolha de plano -> gera PIX. Exige login (mas NÃO exige loja ativa, pois é
// justamente aqui que uma loja inadimplente vem renovar).
Route::middleware('auth')->group(function () {
    Route::get('/assinar', [CheckoutController::class, 'escolher'])->name('assinar.escolher');
    Route::get('/assinar/{slug}', [CheckoutController::class, 'plano'])->name('checkout.plano');
    Route::get('/assinatura/{assinatura}/retorno', [CheckoutController::class, 'retorno'])->name('checkout.retorno');
    Route::get('/assinatura/{assinatura}/status', [CheckoutController::class, 'status'])->name('checkout.status');
});

// Webhook do Mercado Pago — público e isento de CSRF (ver bootstrap/app.php).
Route::post('/webhooks/mercadopago', [WebhookController::class, 'mercadopago'])->name('webhook.mercadopago');

// Voltar da personificação ("Entrar como") para o super_admin original.
Route::middleware('auth')->post('/admin/voltar-personificacao', [AdminController::class, 'voltarPersonificacao'])->name('admin.voltar');
