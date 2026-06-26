<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoPreco extends Model
{
    use HasFactory;

    protected $table = 'historico_precos';

    protected $fillable = [
        'produto_id',
        'valor',
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

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
