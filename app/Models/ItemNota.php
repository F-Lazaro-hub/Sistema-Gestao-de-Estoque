<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemNota extends Model
{
    use HasFactory;

    protected $table = 'itens_nota';

    protected $fillable = [
        'nota_id',
        'produto_id',
        'quantidade',
        'valor',
        'valor_total',
    ];

    protected function casts(): array
    {
        return [
            'quantidade'  => 'decimal:3',
            'valor'       => 'decimal:2',
            'valor_total' => 'decimal:2',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function nota(): BelongsTo
    {
        return $this->belongsTo(Nota::class, 'nota_id');
    }
 
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
 
    // ─── Acessores ─────────────────────────────────────────────────────────────
 
    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->valor, 2, ',', '.');
    }
 
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->valor_total, 2, ',', '.');
    }
 
    public function getQuantidadeFormatadaAttribute(): string
    {
        return number_format((float) $this->quantidade, 3, ',', '.');
    }
}
