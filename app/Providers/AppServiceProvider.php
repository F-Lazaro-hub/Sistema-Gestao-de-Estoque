<?php

namespace App\Providers;

use App\Repositories\EstoqueRepository;
use App\Repositories\ProdutoRepository;
use App\Repositories\SolicitacaoCompraRepository;
use App\Services\AlertaService;
use App\Services\CompraService;
use App\Services\EstoqueService;
use App\Services\FinanceiroService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // -----------------------------------------------------------------
        // Repositories — singletons (uma instância por request)
        // -----------------------------------------------------------------
        $this->app->singleton(ProdutoRepository::class);
        $this->app->singleton(EstoqueRepository::class);
        $this->app->singleton(SolicitacaoCompraRepository::class);

        // -----------------------------------------------------------------
        // Services
        // -----------------------------------------------------------------

        // AlertaService não tem dependências de outros services.
        $this->app->singleton(AlertaService::class);

        // EstoqueService depende de AlertaService.
        $this->app->singleton(EstoqueService::class, function ($app) {
            return new EstoqueService(
                estoqueRepo:    $app->make(EstoqueRepository::class),
                produtoRepo:    $app->make(ProdutoRepository::class),
                alertaService:  $app->make(AlertaService::class),
            );
        });

        // FinanceiroService sem dependências de outros services.
        $this->app->singleton(FinanceiroService::class);

        // CompraService depende de EstoqueService e FinanceiroService.
        $this->app->singleton(CompraService::class, function ($app) {
            return new CompraService(
                solicitacaoRepo:  $app->make(SolicitacaoCompraRepository::class),
                estoqueService:   $app->make(EstoqueService::class),
                financeiroService: $app->make(FinanceiroService::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

    }
}
