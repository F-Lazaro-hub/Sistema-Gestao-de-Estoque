<?php

namespace App\Repositories;

use App\Models\SolicitacaoCompra;
use Illuminate\Database\Eloquent\Collection;

class SolicitacaoCompraRepository
{
    /**
     * Busca uma solicitação por ID (com itens e produtos carregados).
     */
    public function buscarPorId(int $id): ?SolicitacaoCompra
    {
        return SolicitacaoCompra::with([
            'itens.produto.estoque',
            'solicitante',
            'aprovador',
        ])->find($id);
    }

    /**
     * Busca uma solicitação por ID ou lança exceção.
     */
    public function buscarPorIdOuFalhar(int $id): SolicitacaoCompra
    {
        return SolicitacaoCompra::with([
            'itens.produto.estoque',
            'solicitante',
            'aprovador',
        ])->findOrFail($id);
    }

    /**
     * Lista solicitações por situação.
     *
     * @param  string|array  $situacao
     */
    public function listarPorSituacao(string|array $situacao): Collection
    {
        return SolicitacaoCompra::with(['solicitante', 'aprovador'])
            ->when(is_array($situacao), fn($q) => $q->whereIn('situacao', $situacao))
            ->when(is_string($situacao), fn($q) => $q->where('situacao', $situacao))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Lista solicitações pendentes de aprovação.
     */
    public function listarPendentes(): Collection
    {
        return $this->listarPorSituacao('pendente');
    }

    /**
     * Lista solicitações de um solicitante específico.
     */
    public function listarPorSolicitante(int $usuarioId): Collection
    {
        return SolicitacaoCompra::with(['aprovador'])
            ->where('solicitante_id', $usuarioId)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Salva (cria ou atualiza) uma solicitação de compra.
     */
    public function salvar(SolicitacaoCompra $solicitacao): SolicitacaoCompra
    {
        $solicitacao->save();
        return $solicitacao;
    }
}
