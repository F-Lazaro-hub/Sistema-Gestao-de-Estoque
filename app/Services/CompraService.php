<?php

namespace App\Services;

use App\Models\ItemSolicitacaoCompra;
use App\Models\SolicitacaoCompra;
use App\Repositories\SolicitacaoCompraRepository;
use Illuminate\Support\Facades\DB;

class CompraService
{
    public function __construct(
        private readonly SolicitacaoCompraRepository $solicitacaoRepo,
        private readonly EstoqueService              $estoqueService,
        private readonly FinanceiroService           $financeiroService,
    ) {}

    // -------------------------------------------------------------------------
    // Criação
    // -------------------------------------------------------------------------

    /**
     * Cria uma nova solicitação de compra com seus itens.
     *
     * @param  int    $solicitanteId
     * @param  string $observacoes
     * @param  array  $itens  [ ['produto_id' => int, 'quantidade' => float, 'valor_unitario' => float], ... ]
     *
     * @throws \DomainException  Se $itens estiver vazio.
     */
    public function criar(
        int    $solicitanteId,
        string $observacoes,
        array  $itens,
    ): SolicitacaoCompra {
        if (empty($itens)) {
            throw new \DomainException('A solicitação de compra deve conter ao menos um item.');
        }

        return DB::transaction(function () use ($solicitanteId, $observacoes, $itens) {
            $solicitacao = new SolicitacaoCompra();
            $solicitacao->solicitante_id = $solicitanteId;
            $solicitacao->situacao       = 'pendente';
            $solicitacao->observacoes    = $observacoes;
            $solicitacao->valor_total    = 0;
            $this->solicitacaoRepo->salvar($solicitacao);

            $valorTotal = 0;

            foreach ($itens as $dado) {
                $quantidade    = (float) $dado['quantidade'];
                $valorUnitario = (float) $dado['valor_unitario'];
                $valorItem     = round($quantidade * $valorUnitario, 2);

                $item = new ItemSolicitacaoCompra();
                $item->solicitacao_compra_id = $solicitacao->id;
                $item->produto_id            = (int) $dado['produto_id'];
                $item->quantidade            = $quantidade;
                $item->valor_unitario        = $valorUnitario;
                $item->valor_total           = $valorItem;
                $item->save();

                $valorTotal += $valorItem;
            }

            $solicitacao->valor_total = round($valorTotal, 2);
            $this->solicitacaoRepo->salvar($solicitacao);

            return $solicitacao->fresh('itens.produto');
        });
    }

    // -------------------------------------------------------------------------
    // Aprovação
    // -------------------------------------------------------------------------

    /**
     * Aprova uma solicitação de compra.
     *
     * Fluxo:
     *  1. Valida situação e saldo.
     *  2. Atualiza situação → 'aprovado'.
     *  3. Debita o valor do caixa (FinanceiroService).
     *  4. Dá entrada no estoque de cada item (EstoqueService).
     *
     * @throws \DomainException  Se a situação não for "pendente" ou saldo insuficiente.
     */
    public function aprovar(int $solicitacaoId, int $aprovadorId): SolicitacaoCompra
    {
        $solicitacao = $this->solicitacaoRepo->buscarPorIdOuFalhar($solicitacaoId);

        $this->garantirSituacao($solicitacao, 'pendente', 'aprovar');

        $valorTotal = (float) $solicitacao->valor_total;

        if (! $this->financeiroService->temSaldo($valorTotal)) {
            throw new \DomainException(
                'Saldo insuficiente no caixa para aprovar esta solicitação. '
                . 'Valor necessário: R$ ' . number_format($valorTotal, 2, ',', '.')
            );
        }

        return DB::transaction(function () use ($solicitacao, $aprovadorId, $valorTotal) {
            // 1. Atualiza a solicitação.
            $solicitacao->situacao     = 'aprovada';
            $solicitacao->aprovador_id = $aprovadorId;
            $solicitacao->aprovado_em  = now();
            $this->solicitacaoRepo->salvar($solicitacao);

            // 2. Lançamento financeiro (debita do caixa).
            $this->financeiroService->registrarSaida(
                valor:         $valorTotal,
                descricao:     "Compra aprovada — Solicitação #{$solicitacao->id}",
                usuarioId:     $aprovadorId,
                solicitacaoId: $solicitacao->id,
            );

            // 3. Entrada no estoque de cada item.
            foreach ($solicitacao->itens as $item) {
                $this->estoqueService->registrarEntrada(
                    produtoId:     $item->produto_id,
                    quantidade:    (float) $item->quantidade,
                    valorUnitario: (float) $item->valor_unitario,
                    motivo:        "Entrada por compra aprovada — Solicitação #{$solicitacao->id}",
                    usuarioId:     $aprovadorId,
                    solicitacaoId: $solicitacao->id,
                );
            }

            return $solicitacao->fresh(['itens.produto', 'aprovador', 'solicitante']);
        });
    }

    // -------------------------------------------------------------------------
    // Reprovação
    // -------------------------------------------------------------------------

    /**
     * Reprova uma solicitação de compra pendente.
     *
     * @throws \DomainException  Se a situação não for "pendente".
     */
    public function reprovar(int $solicitacaoId, int $aprovadorId, string $motivo = ''): SolicitacaoCompra
    {
        $solicitacao = $this->solicitacaoRepo->buscarPorIdOuFalhar($solicitacaoId);

        $this->garantirSituacao($solicitacao, 'pendente', 'reprovar');

        $solicitacao->situacao     = 'reprovada';
        $solicitacao->aprovador_id = $aprovadorId;
        $solicitacao->aprovado_em  = now(); // reutilizamos o campo como "data de decisão"
        if ($motivo) {
            $solicitacao->observacoes = trim($solicitacao->observacoes . "\n\nReprovação: " . $motivo);
        }

        $this->solicitacaoRepo->salvar($solicitacao);

        return $solicitacao->fresh(['aprovador', 'solicitante']);
    }

    // -------------------------------------------------------------------------
    // Cancelamento
    // -------------------------------------------------------------------------

    /**
     * Cancela uma solicitação que ainda está pendente.
     * Somente o próprio solicitante ou um admin pode cancelar.
     *
     * @throws \DomainException
     */
    public function cancelar(int $solicitacaoId, int $usuarioId): SolicitacaoCompra
    {
        $solicitacao = $this->solicitacaoRepo->buscarPorIdOuFalhar($solicitacaoId);

        $this->garantirSituacao($solicitacao, 'pendente', 'cancelar');

        $solicitacao->situacao = 'cancelada';
        $this->solicitacaoRepo->salvar($solicitacao);

        return $solicitacao->fresh(['solicitante']);
    }

    // -------------------------------------------------------------------------
    // Helper privado
    // -------------------------------------------------------------------------

    private function garantirSituacao(
        SolicitacaoCompra $solicitacao,
        string            $situacaoEsperada,
        string            $acao,
    ): void {
        if ($solicitacao->situacao !== $situacaoEsperada) {
            throw new \DomainException(
                "Não é possível {$acao} a solicitação #{$solicitacao->id}: "
                . "situação atual é \"{$solicitacao->situacao}\" "
                . "(esperado: \"{$situacaoEsperada}\")."
            );
        }
    }
}
