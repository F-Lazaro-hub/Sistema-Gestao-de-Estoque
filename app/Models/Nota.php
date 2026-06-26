<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notas';

    protected $fillable = [
        'numero',
        'data',
        'responsavel_id',
        'valor_total',
    ];

    protected function casts(): array
    {
        return [
            'data'        => 'date',
            'valor_total' => 'decimal:2',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    /** Usuário responsável pela emissão da nota. */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemNota::class, 'nota_id');
    }

    // ─── Acessores ─────────────────────────────────────────────────────────────
 
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->valor_total, 2, ',', '.');
    }
 
    public function getDataFormatadaAttribute(): string
    {
        return $this->data ? $this->data->format('d/m/Y') : '-';
    }
}
