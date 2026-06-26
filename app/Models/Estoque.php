<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoques';

    protected $fillable = [
        'produto_id',
        'quantidade_atual',
        'quantidade_minima',
        'ultima_movimentacao_em',
    ];

    protected function casts(): array
    {
        return [
            'quantidade_atual'       => 'decimal:3',
            'quantidade_minima'      => 'decimal:3',
            'ultima_movimentacao_em' => 'datetime',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Retorna true se a quantidade atual está no nível de alerta. */
    public function estaBaixoDoMinimo(): bool
    {
        return $this->quantidade_atual <= $this->quantidade_minima;
    }
}
