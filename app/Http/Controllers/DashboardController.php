<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Caixa;
use App\Models\Estoque;
use App\Models\SolicitacaoCompra;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard com os indicadores exigidos:
     * saldo do caixa, alertas abertos, produtos abaixo do mínimo
     * e solicitações de compra pendentes.
     */
    public function index()
    {
        $saldoAtual = Caixa::instancia()->saldo_atual;
        
        $alertasAbertos = Alerta::where('situacao', 'pendente')->count();

        $produtosBaixoEstoque = Estoque::whereColumn('quantidade_atual', '<=', 'quantidade_minima')->count();

        $solicitacoesPendentes = SolicitacaoCompra::where('situacao', 'pendente')->count();

        $ultimosAlertas = Alerta::with('produto')
            ->where('situacao', 'pendente')
            ->latest()
            ->take(5)
            ->get();

        $ultimasSolicitacoes = SolicitacaoCompra::with('solicitante')
            ->where('situacao', 'pendente')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'saldoAtual',
            'alertasAbertos',
            'produtosBaixoEstoque',
            'solicitacoesPendentes',
            'ultimosAlertas',
            'ultimasSolicitacoes',
        ));
    }
}
