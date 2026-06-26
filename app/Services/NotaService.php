<?php

namespace App\Services;

use App\Models\ItemNota;
use App\Models\Nota;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotaService
{
    /**
     * Gera o próximo número sequencial de nota interna.
     * Formato: NI-YYYY-NNNN  (ex.: NI-2025-0007)
     */
    public function gerarNumero(): string
    {
        $ano    = date('Y');
        $ultimo = Nota::whereYear('created_at', $ano)->withTrashed()->count();

        return 'NI-' . $ano . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cria uma nova nota com seus itens dentro de uma transação.
     */
    public function criar(array $dados): Nota
    {
        return DB::transaction(function () use ($dados) {
            $valorTotal = $this->calcularTotal($dados['itens']);

            $nota = Nota::create([
                'numero'         => $this->gerarNumero(),
                'data'           => $dados['data'],
                'responsavel_id' => Auth::id(),
                'valor_total'    => $valorTotal,
            ]);

            $this->salvarItens($nota, $dados['itens']);

            return $nota->load('itens.produto', 'responsavel');
        });
    }

    /**
     * Atualiza uma nota existente e seus itens.
     */
    public function atualizar(Nota $nota, array $dados): Nota
    {
        return DB::transaction(function () use ($nota, $dados) {
            $valorTotal = $this->calcularTotal($dados['itens']);

            $nota->update([
                'data'        => $dados['data'],
                'valor_total' => $valorTotal,
            ]);

            // Remove itens antigos e recria
            $nota->itens()->delete();
            $this->salvarItens($nota, $dados['itens']);

            return $nota->fresh(['itens.produto', 'responsavel']);
        });
    }

    /**
     * Exclui a nota e seus itens (soft delete).
     */
    public function excluir(Nota $nota): void
    {
        DB::transaction(function () use ($nota) {
            $nota->itens()->delete();
            $nota->delete();
        });
    }

    // ─── Helpers privados ──────────────────────────────────────────────────────

    private function calcularTotal(array $itens): float
    {
        return collect($itens)->sum(
            fn($item) => (float) $item['quantidade'] * (float) $item['valor']
        );
    }

    private function salvarItens(Nota $nota, array $itens): void
    {
        foreach ($itens as $item) {
            ItemNota::create([
                'nota_id'     => $nota->id,
                'produto_id'  => $item['produto_id'],
                'quantidade'  => $item['quantidade'],
                'valor'       => $item['valor'],
                'valor_total' => (float) $item['quantidade'] * (float) $item['valor'],
            ]);
        }
    }
}