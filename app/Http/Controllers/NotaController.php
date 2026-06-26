<?php

namespace App\Http\Controllers;

use App\Http\Requests\Nota\StoreNotaRequest;
use App\Http\Requests\Nota\UpdateNotaRequest;
use App\Models\Nota;
use App\Models\Produto;
use App\Services\NotaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NotaController extends Controller
{
    public function __construct(private readonly NotaService $notaService) {}

    /**
     * Listagem de notas internas com filtros e paginação.
     */
    public function index(Request $request): View
    {
        $notas = Nota::with('responsavel')
            ->when(
                $request->filled('busca'),
                fn ($q) => $q->where('numero', 'like', "%{$request->busca}%")
            )
            ->when(
                $request->filled('data_inicio'),
                fn ($q) => $q->whereDate('data', '>=', $request->date('data_inicio'))
            )
            ->when(
                $request->filled('data_fim'),
                fn ($q) => $q->whereDate('data', '<=', $request->date('data_fim'))
            )
            ->when(
                $request->filled('responsavel'),
                fn ($q) => $q->whereHas(
                    'responsavel',
                    fn ($u) => $u->where('nome', 'like', "%{$request->responsavel}%")
                )
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notas.index', compact('notas'));
    }

    /**
     * Formulário de criação de nova nota.
     */
    public function create(): View
    {
        $produtos = Produto::where('ativo', true)
            ->with('estoques')
            ->orderBy('nome')
            ->get();

        return view('notas.create', compact('produtos'));
    }

    /**
     * Persiste a nova nota.
     */
    public function store(StoreNotaRequest $request): RedirectResponse
    {
        $nota = $this->notaService->criar($request->validated());

        return redirect()
            ->route('notas.show', $nota)
            ->with('sucesso', "Nota {$nota->numero} emitida com sucesso.");
    }

    /**
     * Exibe os detalhes de uma nota.
     */
    public function show(Nota $nota): View
    {
        $nota->load('itens.produto.categoria', 'responsavel');

        return view('notas.show', compact('nota'));
    }

    /**
     * Formulário de edição de nota.
     */
    public function edit(Nota $nota): View
    {
        $nota->load('itens.produto');

        $produtos = Produto::where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('notas.edit', compact('nota', 'produtos'));
    }

    /**
     * Atualiza a nota.
     */
    public function update(UpdateNotaRequest $request, Nota $nota): RedirectResponse
    {
        $nota = $this->notaService->atualizar($nota, $request->validated());

        return redirect()
            ->route('notas.show', $nota)
            ->with('sucesso', "Nota {$nota->numero} atualizada com sucesso.");
    }

    /**
     * Exclui a nota (soft delete).
     */
    public function destroy(Nota $nota): RedirectResponse
    {
        $numero = $nota->numero;
        $this->notaService->excluir($nota);

        return redirect()
            ->route('notas.index')
            ->with('sucesso', "Nota {$numero} excluída com sucesso.");
    }

    /**
     * Gera e faz o download da nota em PDF via DomPDF.
     */
    public function pdf(Nota $nota): Response
    {
        $nota->load('itens.produto.categoria', 'responsavel');

        $pdf = Pdf::loadView('notas.pdf', compact('nota'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("nota-{$nota->numero}.pdf");
    }

    /**
     * Abre a nota em uma aba do navegador para impressão direta.
     */
    public function imprimir(Nota $nota): View
    {
        $nota->load('itens.produto.categoria', 'responsavel');

        return view('notas.imprimir', compact('nota'));
    }
}