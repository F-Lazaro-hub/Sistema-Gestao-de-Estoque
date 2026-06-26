<?php

namespace App\Repositories;

use App\Models\Estoque;
use App\Models\MovimentacaoEstoque;
use Illuminate\Database\Eloquent\Collection;

class EstoqueRepository
{
    /**
     * Busca o registro de estoque de um produto.
     */
    public function buscarPorProduto(int $produtoId): ?Estoque
    {
        return Estoque::where('produto_id', $produtoId)->first();
    }

    /**
     * Busca o registro de estoque de um produto ou lança exceção.
     */
    public function buscarPorProdutoOuFalhar(int $produtoId): Estoque
    {
        return Estoque::where('produto_id', $produtoId)->firstOrFail();
    }

    /**
     * Lista movimentações de um produto, da mais recente para a mais antiga.
     */
    public function listarMovimentacoesPorProduto(int $produtoId, int $limite = 50): Collection
    {
        return MovimentacaoEstoque::with('usuario')
            ->where('produto_id', $produtoId)
            ->orderByDesc('data_movimentacao')
            ->orderByDesc('id')
            ->limit($limite)
            ->get();
    }

    /**
     * Lista movimentações de uma solicitação de compra.
     */
    public function listarMovimentacoesPorSolicitacao(int $solicitacaoId): Collection
    {
        return MovimentacaoEstoque::with(['produto', 'usuario'])
            ->where('solicitacao_compra_id', $solicitacaoId)
            ->orderByDesc('data_movimentacao')
            ->get();
    }

    /**
     * Salva (cria ou atualiza) um registro de estoque.
     */
    public function salvar(Estoque $estoque): Estoque
    {
        $estoque->save();
        return $estoque;
    }

    /**
     * Registra uma movimentação de estoque.
     */
    public function registrarMovimentacao(MovimentacaoEstoque $movimentacao): MovimentacaoEstoque
    {
        $movimentacao->save();
        return $movimentacao;
    }
}
