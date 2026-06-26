<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Http\Requests\SolicitacaoCompra\StoreSolicitacaoCompraRequest;
use App\Http\Requests\SolicitacaoCompra\ReprovarSolicitacaoRequest;
use App\Models\Produto;
use App\Models\SolicitacaoCompra;
use App\Services\CompraService;

class SolicitacaoCompraController extends Controller
{
    public function __construct(
        private readonly CompraService $compraService,
    ) {}

    public function index()
    {
        $solicitacoes = SolicitacaoCompra::with(['solicitante', 'aprovador'])
            ->latest()
            ->paginate(15);

        return view('compras.index', compact('solicitacoes'));
    }

    public function create()
    {
        $produtos = Produto::where('ativo', true)->orderBy('nome')->get();

        return view('compras.create', compact('produtos'));
    }

    public function store(StoreSolicitacaoCompraRequest $request)
    {
        try {
            $solicitacao = $this->compraService->criar(
                solicitanteId: auth()->id(),
                observacoes:   $request->observacoes ?? '',
                itens:         $request->itens,
            );

            return redirect()->route('compras.show', $solicitacao->id)
                ->with('sucesso', 'Solicitação de compra criada com sucesso.');
        } catch (\DomainException $e) {
            return back()->withInput()->with('erro', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $solicitacao = SolicitacaoCompra::with([
            'solicitante',
            'aprovador',
            'itens.produto',
        ])->findOrFail($id);

        return view('compras.show', compact('solicitacao'));
    }

    public function destroy(int $id)
    {
        try {
            $this->compraService->cancelar($id, auth()->id());

            return redirect()->route('compras.index')
                ->with('sucesso', 'Solicitação cancelada com sucesso.');
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }

    public function aprovar(int $id)
    {
        try {
            $this->compraService->aprovar($id, auth()->id());

            return back()->with('sucesso', 'Solicitação aprovada com sucesso.');
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }

    public function reprovar(int $id, ReprovarSolicitacaoRequest $request)
    {
        try {
            $this->compraService->reprovar($id, auth()->id(), $request->motivo);
            
            return back()->with('sucesso', 'Solicitação reprovada.');
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }

    public function cancelar(int $id)
    {
        try {
            $this->compraService->cancelar($id, auth()->id());

            return redirect()->route('compras.index')
                ->with('sucesso', 'Solicitação cancelada com sucesso.');
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }
}
