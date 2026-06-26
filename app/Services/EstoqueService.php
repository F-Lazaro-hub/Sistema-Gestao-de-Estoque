<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use App\Repositories\EstoqueRepository;
use App\Repositories\ProdutoRepository;
use Illuminate\Support\Facades\DB;

class EstoqueService
{
    public function __construct(
        private readonly EstoqueRepository $estoqueRepo,
        private readonly ProdutoRepository $produtoRepo,
        private readonly AlertaService     $alertaService,
    ) {}

    // -------------------------------------------------------------------------
    // API pública
    // -------------------------------------------------------------------------

    /**
     * Registra uma entrada de estoque (ex.: recebimento de compra aprovada).
     *
     * @param  int         $produtoId
     * @param  float       $quantidade       Deve ser positiva.
     * @param  float       $valorUnitario    Valor pago por unidade.
     * @param  string      $motivo           Descrição da origem.
     * @param  int         $usuarioId        Quem registrou.
     * @param  int|null    $solicitacaoId    Vínculo com SolicitacaoCompra (opcional).
     */
    public function registrarEntrada(
        int    $produtoId,
        float  $quantidade,
        float  $valorUnitario,
        string $motivo,
        int    $usuarioId,
        ?int   $solicitacaoId = null,
    ): MovimentacaoEstoque {
        $this->validarQuantidadePositiva($quantidade);

        return DB::transaction(function () use (
            $produtoId, $quantidade, $valorUnitario, $motivo, $usuarioId, $solicitacaoId
        ) {
            $estoque = $this->estoqueRepo->buscarPorProdutoOuFalhar($produtoId);
            $produto = $this->produtoRepo->buscarPorIdOuFalhar($produtoId);

            $estoque->quantidade_atual     += $quantidade;
            $estoque->ultima_movimentacao_em = now();
            $this->estoqueRepo->salvar($estoque);

            $this->atualizarPrecosProduto($produto, $valorUnitario, $quantidade);

            $mov = $this->criarMovimentacao(
                produtoId:      $produtoId,
                tipo:           'entrada',
                quantidade:     $quantidade,
                valorUnitario:  $valorUnitario,
                motivo:         $motivo,
                usuarioId:      $usuarioId,
                solicitacaoId:  $solicitacaoId,
            );

            // Uma entrada pode resolver alertas abertos — o serviço decide.
            $this->alertaService->verificarEResolverAlertas($produto, $estoque);

            return $mov;
        });
    }

    /**
     * Registra uma saída de estoque.
     *
     * @throws \DomainException  Se não houver saldo suficiente.
     */
    public function registrarSaida(
        int    $produtoId,
        float  $quantidade,
        float  $valorUnitario,
        string $motivo,
        int    $usuarioId,
        ?int   $solicitacaoId = null,
    ): MovimentacaoEstoque {
        $this->validarQuantidadePositiva($quantidade);

        return DB::transaction(function () use (
            $produtoId, $quantidade, $valorUnitario, $motivo, $usuarioId, $solicitacaoId
        ) {
            $estoque = $this->estoqueRepo->buscarPorProdutoOuFalhar($produtoId);
            $produto = $this->produtoRepo->buscarPorIdOuFalhar($produtoId);

            if ($estoque->quantidade_atual < $quantidade) {
                throw new \DomainException(
                    "Saldo insuficiente para o produto \"{$produto->nome}\". "
                    . "Disponível: {$estoque->quantidade_atual} — Solicitado: {$quantidade}."
                );
            }

            $estoque->quantidade_atual     -= $quantidade;
            $estoque->ultima_movimentacao_em = now();
            $this->estoqueRepo->salvar($estoque);

            $mov = $this->criarMovimentacao(
                produtoId:      $produtoId,
                tipo:           'saida',
                quantidade:     $quantidade,
                valorUnitario:  $valorUnitario,
                motivo:         $motivo,
                usuarioId:      $usuarioId,
                solicitacaoId:  $solicitacaoId,
            );

            // Verifica se estoque ficou abaixo do mínimo após a saída.
            $estoque->refresh();
            $this->alertaService->verificarEGerarAlerta($produto, $estoque);

            return $mov;
        });
    }

    /**
     * Realiza um ajuste manual de estoque (inventário, correção de divergência).
     *
     * @param  float  $novaQuantidade  Quantidade real contada; pode ser zero.
     */
    public function registrarAjuste(
        int    $produtoId,
        float  $novaQuantidade,
        string $motivo,
        int    $usuarioId,
    ): MovimentacaoEstoque {
        if ($novaQuantidade < 0) {
            throw new \DomainException('A quantidade ajustada não pode ser negativa.');
        }

        return DB::transaction(function () use ($produtoId, $novaQuantidade, $motivo, $usuarioId) {
            $estoque = $this->estoqueRepo->buscarPorProdutoOuFalhar($produtoId);
            $produto = $this->produtoRepo->buscarPorIdOuFalhar($produtoId);

            $diferenca               = $novaQuantidade - $estoque->quantidade_atual;
            $estoque->quantidade_atual     = $novaQuantidade;
            $estoque->ultima_movimentacao_em = now();
            $this->estoqueRepo->salvar($estoque);

            // Valor unitário do ajuste: usamos o valor médio do produto como referência.
            $valorRef = (float) ($produto->valor_medio ?? $produto->ultimo_valor_pago ?? 0);

            $mov = $this->criarMovimentacao(
                produtoId:     $produtoId,
                tipo:          'ajuste',
                quantidade:    abs($diferenca),
                valorUnitario: $valorRef,
                motivo:        $motivo . " (diferença: {$diferenca})",
                usuarioId:     $usuarioId,
            );

            $estoque->refresh();
            $this->alertaService->verificarEGerarAlerta($produto, $estoque);

            return $mov;
        });
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    private function criarMovimentacao(
        int    $produtoId,
        string $tipo,
        float  $quantidade,
        float  $valorUnitario,
        string $motivo,
        int    $usuarioId,
        ?int   $solicitacaoId = null,
    ): MovimentacaoEstoque {
        $mov = new MovimentacaoEstoque();
        $mov->produto_id           = $produtoId;
        $mov->tipo                 = $tipo;
        $mov->quantidade           = $quantidade;
        $mov->valor_unitario       = $valorUnitario;
        $mov->valor_total          = round($quantidade * $valorUnitario, 2);
        $mov->motivo               = $motivo;
        $mov->usuario_id           = $usuarioId;
        $mov->solicitacao_compra_id = $solicitacaoId;
        $mov->data_movimentacao    = now()->toDateString();

        return $this->estoqueRepo->registrarMovimentacao($mov);
    }

    /**
     * Atualiza `ultimo_valor_pago` e recalcula `valor_medio` (média ponderada).
     */
    private function atualizarPrecosProduto(Produto $produto, float $valorUnitario, float $quantidade): void
    {
        $produto->ultimo_valor_pago = $valorUnitario;

        // Média ponderada: (estoque_anterior * valor_medio_atual + quantidade_nova * valor_novo)
        //                  ÷ (estoque_anterior + quantidade_nova)
        $estoqueAtual = (float) ($produto->estoque?->quantidade_atual ?? 0);
        $valorMedioAtual = (float) ($produto->valor_medio ?? $valorUnitario);

        if (($estoqueAtual + $quantidade) > 0) {
            $produto->valor_medio = round(
                ($estoqueAtual * $valorMedioAtual + $quantidade * $valorUnitario)
                / ($estoqueAtual + $quantidade),
                4
            );
        }

        $this->produtoRepo->salvar($produto);

        // Registra histórico de preço.
        $produto->historicoPrecos()->create([
            'valor' => $valorUnitario,
            'data'  => now()->toDateString(),
        ]);
    }

    private function validarQuantidadePositiva(float $quantidade): void
    {
        if ($quantidade <= 0) {
            throw new \DomainException('A quantidade deve ser maior que zero.');
        }
    }
}
