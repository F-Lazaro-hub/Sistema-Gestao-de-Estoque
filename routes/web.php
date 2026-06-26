<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SenhaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ProdutoController;
use App\Http\Controllers\Estoque\EstoqueController;
use App\Http\Controllers\Vendas\SolicitacaoCompraController;
use App\Http\Controllers\Financeiro\FinanceiroController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\NotaController;

Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ─── Rotas públicas (somente para visitantes não autenticados) ───────────────
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.post');

    // Esqueci minha senha
    Route::get('/esqueci-senha', [SenhaController::class, 'create'])->name('password.request');
    Route::post('/esqueci-senha', [SenhaController::class, 'store'])->name('password.email');

    // Redefinir senha
    Route::get('/redefinir-senha/{token}', [SenhaController::class, 'edit'])->name('password.reset');
    Route::post('/redefinir-senha', [SenhaController::class, 'update'])->name('password.update');

});

// Logout (precisa estar autenticado para encerrar a sessão)
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

});

// ─── Rotas autenticadas ───────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // ── Notificações (todos os perfis autenticados) ──────────────────────────
    Route::middleware('auth')->group(function () {
        Route::get('notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
        Route::patch('notificacoes/{notificacao}/lida', [NotificacaoController::class, 'marcarLida'])->name('notificacoes.marcarLida');
        Route::patch('notificacoes/marcar-todas-lidas', [NotificacaoController::class, 'marcarTodasLidas'])->name('notificacoes.marcarTodasLidas');
    });

    // ── Alertas ──────────────────────────────────────────────────────────────
    Route::prefix('alertas')->name('alertas.')->controller(AlertaController::class)->group(function () {
        Route::get('/', 'index')->name('index');

        Route::middleware('perfil:admin,gerente')->group(function () {
            Route::patch('{id}/resolver', 'resolver')->name('resolver');
        });
    });

    // ── Solicitações de compra ────────────────────────────────────────────────
    Route::middleware('perfil:comprador,gerente,admin')->group(function () {
        Route::resource('compras', SolicitacaoCompraController::class)
            ->except(['edit', 'update']);

        Route::patch('compras/{id}/cancelar', [SolicitacaoCompraController::class, 'cancelar'])
            ->name('compras.cancelar');
    });

    // Aprovar / reprovar (apenas gerente e admin)
    Route::middleware('perfil:gerente,admin')->group(function () {
        Route::patch('compras/{id}/aprovar',  [SolicitacaoCompraController::class, 'aprovar'])->name('compras.aprovar');
        Route::patch('compras/{id}/reprovar', [SolicitacaoCompraController::class, 'reprovar'])->name('compras.reprovar');
    });

    // ── Estoque ───────────────────────────────────────────────────────────────
    Route::middleware(['auth', 'perfil:admin,almoxarife'])->group(function () {
        Route::get('estoque', [EstoqueController::class, 'index'])->name('estoque.index');
        Route::get('estoque/movimentacoes', [EstoqueController::class, 'movimentacoes'])->name('estoque.movimentacoes');
        Route::get('estoque/{produto}', [EstoqueController::class, 'show'])->name('estoque.show');
        Route::get('estoque/{produto}/ajuste', [EstoqueController::class, 'ajuste'])->name('estoque.ajuste');
        Route::post('estoque/{produto}/ajuste', [EstoqueController::class, 'ajustar'])->name('estoque.ajustar');
    });

    // ── Financeiro ────────────────────────────────────────────────────────────
    Route::middleware(['auth', 'perfil:admin,financeiro'])->group(function () {
        Route::get('financeiro', [FinanceiroController::class, 'index'])->name('financeiro.index');
        Route::get('financeiro/aporte', [FinanceiroController::class, 'aporte'])->name('financeiro.aporte');
        Route::post('financeiro/aporte', [FinanceiroController::class, 'registrarAporte'])->name('financeiro.aporte.store');
        Route::get('financeiro/extrato', [FinanceiroController::class, 'extrato'])->name('financeiro.extrato');
    });

    // ── Produtos (admin e gerente) ────────────────────────────────────────────
    Route::middleware('perfil:admin,gerente')->group(function () {
        Route::resource('produtos', ProdutoController::class);
        Route::get('produtos/{id}/historicoPrecos', [ProdutoController::class, 'historicoPrecos'])
            ->name('produtos.historicoPrecos');
        
    });

    // ── Categorias (somente admin) ────────────────────────────────────────────
    Route::middleware('perfil:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class);
    });

    // ── Usuários (somente admin) ──────────────────────────────────────────────
    Route::middleware('perfil:admin')->group(function () {
        Route::resource('usuarios', UsuarioController::class);
        Route::patch('usuarios/{id}/toggle-ativo', [UsuarioController::class, 'toggleAtivo'])
            ->name('usuarios.toggle-ativo');
        Route::patch('usuarios/{usuario}/ativar', [UsuarioController::class, 'ativar'])->name('usuarios.ativar');
    });

    Route::prefix('notas')->name('notas.')->middleware(['auth', 'verificar.perfil:admin,comprador,gestor'])->group(function () {
 
        Route::get('/',              [NotaController::class, 'index'])   ->name('index');
        Route::get('/nova',          [NotaController::class, 'create'])  ->name('create');
        Route::post('/',             [NotaController::class, 'store'])   ->name('store');
        Route::get('/{nota}',        [NotaController::class, 'show'])    ->name('show');
        Route::get('/{nota}/editar', [NotaController::class, 'edit'])    ->name('edit');
        Route::put('/{nota}',        [NotaController::class, 'update'])  ->name('update');
        Route::delete('/{nota}',     [NotaController::class, 'destroy']) ->name('destroy');
    
        // Exportação e impressão
        Route::get('/{nota}/pdf',      [NotaController::class, 'pdf'])      ->name('pdf');
        Route::get('/{nota}/imprimir', [NotaController::class, 'imprimir']) ->name('imprimir');
    });
});
