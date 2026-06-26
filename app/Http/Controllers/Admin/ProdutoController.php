<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Produto\StoreProdutoRequest;
use App\Http\Requests\Produto\UpdateProdutoRequest;
use App\Models\Categoria;
use App\Models\Produto;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::with(['categoria', 'estoque'])
            ->orderBy('nome')
            ->paginate(15);
        
        $categorias = Categoria::withCount('produtos')
            ->orderBy('nome')
            ->paginate(15);

        return view('produtos.index', compact('produtos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();

        return view('produtos.create', compact('categorias'));
    }

    public function store(StoreProdutoRequest $request)
    {
        $produto = Produto::create([
            'nome'              => $request->nome,
            'codigo'            => $request->codigo,
            'descricao'         => $request->descricao,
            'categoria_id'      => $request->categoria_id,
            'marca'             => $request->marca,
            'unidade'           => $request->unidade,
            'valor_medio'       => 0,
            'ultimo_valor_pago' => 0,
            'ativo'             => true,
        ]);

        // Cria o registro de estoque vinculado ao produto
        $produto->estoque()->create([
            'quantidade_atual'  => 0,
            'quantidade_minima' => $request->quantidade_minima ?? 0,
        ]);

        return redirect()->route('produtos.show', $produto)
            ->with('sucesso', 'Produto criado com sucesso.');
    }

    public function show(int $id)
    {
        $produto = Produto::with([
            'categoria',
            'estoque',
            'historicoPrecos' => fn ($q) => $q->latest()->limit(10),
        ])->findOrFail($id);

        return view('produtos.show', compact('produto'));

    }

    public function edit(int $id)
    {
        $produto    = Produto::with('estoque')->findOrFail($id);
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();

        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(UpdateProdutoRequest $request, int $id)
    {
        $produto = Produto::with('estoque')->findOrFail($id);

        $produto->update([
            'nome'         => $request->nome,
            'codigo'       => $request->codigo,
            'descricao'    => $request->descricao,
            'categoria_id' => $request->categoria_id,
            'marca'        => $request->marca,
            'unidade'      => $request->unidade,
            'ativo'        => $request->boolean('ativo', true),
        ]);

        if ($request->filled('quantidade_minima')) {
            $produto->estoque?->update([
                'quantidade_minima' => $request->quantidade_minima,
            ]);
        }

        return redirect()->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado com sucesso.');
    }

    /**
     * Soft-delete lógico: desativa o produto em vez de excluir,
     * preservando o histórico de movimentações e compras.
     */
    public function destroy(int $id)
    {
        $produto = Produto::findOrFail($id);
        $produto->update(['ativo' => false]);

        return redirect()->route('produtos.index')
            ->with('sucesso', 'Produto desativado com sucesso.');
    }

    public function historicoPrecos(int $id)
    {
        $produto    = Produto::findOrFail($id);
        $historicos = $produto->historicoPrecos()->latest()->paginate(20);

        return view('produtos.historico-precos', compact('produto', 'historicos'));
    }
}
