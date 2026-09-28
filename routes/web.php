<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LocacaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedidaController;
use App\Http\Controllers\FinanceiroController; // <-- Adicionado o Controlador Financeiro
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

Route::get('/licenca-expirada', [LicenseController::class, 'index'])->name('licenca.tela');
Route::post('/licenca-ativar', [LicenseController::class, 'ativar'])->name('licenca.ativar');

Route::middleware(['auth', 'license'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de Clientes (AteliêPro)
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

    Route::get('/agenda', [AgendamentoController::class, 'index'])->name('agenda.index');
    
    Route::get('/locacoes/nova', [LocacaoController::class, 'create'])->name('locacao.create');
    Route::post('/locacoes/salvar', [LocacaoController::class, 'store'])->name('locacao.store');
    
    // ==========================================
    // MÓDULO FINANCEIRO (Onde o patrão manda)
    // ==========================================
    Route::get('/financeiro', [FinanceiroController::class, 'index'])->name('financeiro.index');
    Route::post('/financeiro/lancamento', [FinanceiroController::class, 'store'])->name('financeiro.store');
    
});

require __DIR__.'/auth.php';

use App\Http\Controllers\PagamentoController;

Route::get('/assinar/pix', [PagamentoController::class, 'gerarPix'])->name('assinar.pix');