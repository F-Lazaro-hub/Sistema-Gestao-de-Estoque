<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacaoCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitacoes_compra';

    /**
     * Situações possíveis do fluxo de compra.
     * Correspondem ao enum da coluna 'situacao'.
     */
    const SITUACAO_RASCUNHO  = 'rascunho';
    const SITUACAO_PENDENTE  = 'pendente';
    const SITUACAO_APROVADA  = 'aprovada';
    const SITUACAO_REPROVADA = 'reprovada';
    const SITUACAO_CANCELADA = 'cancelada';

    protected $fillable = [
        'solicitante_id',
        'aprovador_id',
        'situacao',
        'valor_total',
        'observacoes',
        'aprovado_em',
    ];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'aprovado_em' => 'datetime',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    /** Usuário que criou a solicitação. */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    /** Usuário que aprovou ou reprovou a solicitação. */
    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprovador_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemSolicitacaoCompra::class, 'solicitacao_compra_id');
    }

    public function movimentacoesEstoque(): HasMany
    {
        return $this->hasMany(MovimentacaoEstoque::class, 'solicitacao_compra_id');
    }

    public function movimentacoesFinanceiras(): HasMany
    {
        return $this->hasMany(MovimentacaoFinanceira::class, 'solicitacao_compra_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPendente(): bool
    {
        return $this->situacao === self::SITUACAO_PENDENTE;
    }

    public function isAprovada(): bool
    {
        return $this->situacao === self::SITUACAO_APROVADA;
    }

    public function isReprovada(): bool
    {
        return $this->situacao === self::SITUACAO_REPROVADA;
    }

    public function isCancelada(): bool
    {
        return $this->situacao === self::SITUACAO_CANCELADA;
    }

    public function podeSerAprovada(): bool
    {
        return $this->situacao === self::SITUACAO_PENDENTE;
    }

    public function podeSerCancelada(): bool
    {
        return in_array($this->situacao, [
            self::SITUACAO_RASCUNHO,
            self::SITUACAO_PENDENTE,
        ], true);
    }
}
