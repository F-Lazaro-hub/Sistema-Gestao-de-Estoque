<?php

namespace App\Services;

use App\Models\Caixa;
use App\Models\MovimentacaoFinanceira;
use Illuminate\Support\Facades\DB;

class FinanceiroService
{
    // -------------------------------------------------------------------------
    // Consulta
    // -------------------------------------------------------------------------

    /**
     * Retorna o registro singleton do caixa.
     */
    public function caixa(): Caixa
    {
        return Caixa::instancia();
    }

    /**
     * Verifica se o caixa possui saldo suficiente para um determinado valor.
     */
    public function temSaldo(float $valor): bool
    {
        return $this->caixa()->temSaldo($valor);
    }

    // -------------------------------------------------------------------------
    // Movimentações
    // -------------------------------------------------------------------------

    /**
     * Registra uma saída financeira (débito) e debita do caixa.
     *
     * Usado pela CompraService ao aprovar uma solicitação de compra.
     *
     * @throws \DomainException  Se não houver saldo suficiente.
     */
    public function registrarSaida(
        float  $valor,
        string $descricao,
        int    $usuarioId,
        ?int   $solicitacaoId = null,
    ): MovimentacaoFinanceira {
        $this->validarValorPositivo($valor);

        return DB::transaction(function () use ($valor, $descricao, $usuarioId, $solicitacaoId) {
            $caixa = Caixa::lockForUpdate()->firstOrFail();

            if (! $caixa->temSaldo($valor)) {
                throw new \DomainException(
                    "Saldo insuficiente no caixa. "
                    . "Disponível: R$ " . number_format($caixa->saldo_atual, 2, ',', '.') . " — "
                    . "Necessário: R$ " . number_format($valor, 2, ',', '.')
                );
            }

            $caixa->saldo_atual = round($caixa->saldo_atual - $valor, 2);
            $caixa->save();

            return $this->criarMovimentacao('saida', $valor, $descricao, $usuarioId, $solicitacaoId);
        });
    }

    /**
     * Registra uma entrada financeira (crédito) e credita no caixa.
     *
     * Usado para aportes iniciais, estornos ou qualquer crédito manual.
     */
    public function registrarEntrada(
        float  $valor,
        string $descricao,
        int    $usuarioId,
        ?int   $solicitacaoId = null,
    ): MovimentacaoFinanceira {
        $this->validarValorPositivo($valor);

        return DB::transaction(function () use ($valor, $descricao, $usuarioId, $solicitacaoId) {
            $caixa = Caixa::lockForUpdate()->firstOrFail();
            $caixa->saldo_atual = round($caixa->saldo_atual + $valor, 2);
            $caixa->save();

            return $this->criarMovimentacao('entrada', $valor, $descricao, $usuarioId, $solicitacaoId);
        });
    }

    /**
     * Estorna uma saída financeira previamente registrada.
     *
     * Recoloca o valor no caixa e cria um lançamento de entrada para rastreabilidade.
     */
    public function estornarSaida(
        MovimentacaoFinanceira $movimentacaoOriginal,
        int                    $usuarioId,
        string                 $motivoEstorno = 'Estorno automático',
    ): MovimentacaoFinanceira {
        if ($movimentacaoOriginal->tipo !== 'saida') {
            throw new \DomainException('Somente movimentações do tipo "saída" podem ser estornadas.');
        }

        return $this->registrarEntrada(
            valor:         (float) $movimentacaoOriginal->valor,
            descricao:     "{$motivoEstorno} — ref. mov. #{$movimentacaoOriginal->id}: {$movimentacaoOriginal->descricao}",
            usuarioId:     $usuarioId,
            solicitacaoId: $movimentacaoOriginal->solicitacao_compra_id,
        );
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    private function criarMovimentacao(
        string $tipo,
        float  $valor,
        string $descricao,
        int    $usuarioId,
        ?int   $solicitacaoId,
    ): MovimentacaoFinanceira {
        $mov = new MovimentacaoFinanceira();
        $mov->tipo                 = $tipo;
        $mov->valor                = $valor;
        $mov->descricao            = $descricao;
        $mov->usuario_id           = $usuarioId;
        $mov->solicitacao_compra_id = $solicitacaoId;
        $mov->data                 = now()->toDateString();
        $mov->save();

        return $mov;
    }

    private function validarValorPositivo(float $valor): void
    {
        if ($valor <= 0) {
            throw new \DomainException('O valor da movimentação financeira deve ser maior que zero.');
        }
    }
}
