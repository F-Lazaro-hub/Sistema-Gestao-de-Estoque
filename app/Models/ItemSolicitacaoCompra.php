<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSolicitacaoCompra extends Model
{
    use HasFactory;

    protected $table = 'itens_solicitacao_compra';

    protected $fillable = [
        'solicitacao_compra_id',
        'produto_id',
        'quantidade',
        'valor_unitario',
        'valor_total',
    ];

    protected function casts(): array
    {
        return [
            'quantidade'     => 'decimal:3',
            'valor_unitario' => 'decimal:2',
            'valor_total'    => 'decimal:2',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function solicitacaoCompra(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoCompra::class, 'solicitacao_compra_id');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
