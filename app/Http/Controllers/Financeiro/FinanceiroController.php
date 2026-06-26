<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financeiro\AporteFinanceiroRequest;
use App\Models\Caixa;
use App\Models\MovimentacaoFinanceira;
use App\Services\FinanceiroService;

class FinanceiroController extends Controller
{
    public function __construct(
        private readonly FinanceiroService $financeiroService,
    ) {}

    public function index()
    {
        $caixa               = Caixa::instancia();
        $movimentacoes = MovimentacaoFinanceira::with('usuario')
            ->latest()
            ->limit(10)
            ->get();

        return view('financeiro.index', compact('caixa', 'movimentacoes'));
    }

    public function aporte()
    {
        return view('financeiro.aporte');
    }

    public function registrarAporte(AporteFinanceiroRequest $request)
    {
        try {
            $this->financeiroService->registrarEntrada(
                valor:     $request->valor,
                descricao: $request->descricao,
                usuarioId: auth()->id(),
            );

            $valorFormatado = 'R$ ' . number_format($request->valor, 2, ',', '.');

            return redirect()->route('financeiro.index')
                ->with('sucesso', "Aporte de {$valorFormatado} registrado com sucesso.");
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }

    public function extrato()
    {
        $caixa         = Caixa::instancia();
        $movimentacoes = MovimentacaoFinanceira::with('usuario')
            ->latest()
            ->paginate(20);

        return view('financeiro.extrato', compact('caixa', 'movimentacoes'));
    }
}
