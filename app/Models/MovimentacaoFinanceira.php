<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentacaoFinanceira extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes_financeiras';

    const TIPO_ENTRADA = 'entrada';
    const TIPO_SAIDA   = 'saida';

    protected $fillable = [
        'tipo',
        'valor',
        'descricao',
        'solicitacao_compra_id',
        'usuario_id',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data'  => 'date',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function solicitacaoCompra(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoCompra::class, 'solicitacao_compra_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
