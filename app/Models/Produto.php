<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produtos';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'categoria_id',
        'marca',
        'unidade',
        'ultimo_valor_pago',
        'valor_medio',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ultimo_valor_pago' => 'decimal:2',
            'valor_medio'       => 'decimal:2',
            'ativo'             => 'boolean',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /** Registro único de quantidade atual/mínima deste produto. */
    public function estoque(): HasOne
    {
        return $this->hasOne(Estoque::class, 'produto_id');
    }

    public function historicoPrecos(): HasMany
    {
        return $this->hasMany(HistoricoPreco::class, 'produto_id');
    }

    public function movimentacoesEstoque(): HasMany
    {
        return $this->hasMany(MovimentacaoEstoque::class, 'produto_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'produto_id');
    }

    public function itensSolicitacaoCompra(): HasMany
    {
        return $this->hasMany(ItemSolicitacaoCompra::class, 'produto_id');
    }

    public function itensNota(): HasMany
    {
        return $this->hasMany(ItemNota::class, 'produto_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Retorna true se a quantidade atual está abaixo do mínimo. */
    public function estaBaixoDoMinimo(): bool
    {
        return $this->estoque
            && $this->estoque->quantidade_atual <= $this->estoque->quantidade_minima;
    }
}
