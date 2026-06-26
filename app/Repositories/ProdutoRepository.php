<?php

namespace App\Repositories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Collection;

class ProdutoRepository
{
    /**
     * Busca produto por ID (com estoque carregado).
     */
    public function buscarPorId(int $id): ?Produto
    {
        return Produto::with('estoque')->find($id);
    }

    /**
     * Busca produto por ID ou lança exceção.
     */
    public function buscarPorIdOuFalhar(int $id): Produto
    {
        return Produto::with('estoque')->findOrFail($id);
    }

    /**
     * Busca produto por código.
     */
    public function buscarPorCodigo(string $codigo): ?Produto
    {
        return Produto::with('estoque')->where('codigo', $codigo)->first();
    }

    /**
     * Lista todos os produtos ativos.
     */
    public function listarAtivos(): Collection
    {
        return Produto::with(['estoque', 'categoria'])
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
    }

    /**
     * Lista produtos com estoque abaixo do mínimo.
     */
    public function listarComEstoqueBaixo(): Collection
    {
        return Produto::with(['estoque', 'categoria'])
            ->where('ativo', true)
            ->whereHas('estoque', function ($q) {
                $q->whereColumn('quantidade_atual', '<=', 'quantidade_minima');
            })
            ->orderBy('nome')
            ->get();
    }

    /**
     * Salva (cria ou atualiza) um produto.
     */
    public function salvar(Produto $produto): Produto
    {
        $produto->save();
        return $produto;
    }
}
