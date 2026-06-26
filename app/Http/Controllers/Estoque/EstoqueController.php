<?php

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estoque\AjusteEstoqueRequest;
use App\Models\Produto;
use App\Services\EstoqueService;

class EstoqueController extends Controller
{
    public function __construct(
        private readonly EstoqueService $estoqueService,
    ) {}

    public function index()
    {
        $produtos = Produto::with(['categoria', 'estoque'])
            ->where('ativo', true)
            ->orderBy('nome')
            ->paginate(15);

        return view('estoque.index', compact('produtos'));
    }

    public function show(int $produtoId)
    {
        $produto = Produto::with(['estoque', 'categoria'])->findOrFail($produtoId);

        return view('estoque.show', compact('produto'));
    }

    public function ajuste(int $produtoId)
    {
        $produto = Produto::with('estoque')->findOrFail($produtoId);

        return view('estoque.ajuste', compact('produto'));
    }

    public function ajustar(AjusteEstoqueRequest $request, int $produtoId)
    {
        try {
            $this->estoqueService->registrarAjuste(
                produtoId:      $produtoId,
                novaQuantidade: $request->quantidade,
                motivo:         $request->motivo,
                usuarioId:      auth()->id(),
            );

            return redirect()->route('estoque.show', $produtoId)
                ->with('sucesso', 'Estoque ajustado com sucesso.');
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }

    public function movimentacoes(int $produtoId)
    {
        $produto       = Produto::with('estoque')->findOrFail($produtoId);
        $movimentacoes = $produto->estoque
            ->movimentacoes()
            ->with('usuario')
            ->latest()
            ->paginate(20);

        return view('estoque.movimentacoes', compact('produto', 'movimentacoes'));
    }
}
