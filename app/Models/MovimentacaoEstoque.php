<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentacaoEstoque extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes_estoque';

    /**
     * Tipos de movimentação permitidos.
     * Correspondem ao enum da coluna 'tipo'.
     */
    const TIPO_ENTRADA = 'entrada';
    const TIPO_SAIDA   = 'saida';
    const TIPO_AJUSTE  = 'ajuste';

    protected $fillable = [
        'produto_id',
        'tipo',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'motivo',
        'solicitacao_compra_id',
        'usuario_id',
        'data_movimentacao',
    ];

    protected function casts(): array
    {
        return [
            'quantidade'        => 'decimal:3',
            'valor_unitario'    => 'decimal:2',
            'valor_total'       => 'decimal:2',
            'data_movimentacao' => 'date',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function solicitacaoCompra(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoCompra::class, 'solicitacao_compra_id');
    }
}
