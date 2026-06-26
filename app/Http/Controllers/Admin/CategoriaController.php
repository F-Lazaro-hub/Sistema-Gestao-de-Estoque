<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('produtos')
            ->orderBy('nome')
            ->paginate(15);

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create([
            'nome'      => $request->nome,
            'descricao' => $request->descricao,
            'ativo'     => true,
        ]);

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria criada com sucesso.');
    }

    public function show(int $id)
    {
        $categoria = Categoria::with('produtos')->findOrFail($id);

        return view('categorias.show', compact('categoria'));
    }

    public function edit(int $id)
    {
        $categoria = Categoria::findOrFail($id);

        return view('categorias.edit', compact('categoria'));
    }

    public function update(StoreCategoriaRequest $request, int $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update([
            'nome'      => $request->nome,
            'descricao' => $request->descricao,
            'ativo'     => $request->boolean('ativo', true),
        ]);

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria atualizada com sucesso.');
    }

    public function destroy(int $id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->produtos()->exists()) {
            return back()->with('erro', 'Não é possível excluir uma categoria com produtos vinculados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria removida com sucesso.');
    }
}
