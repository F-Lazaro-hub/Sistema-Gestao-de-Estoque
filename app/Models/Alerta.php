<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alerta extends Model
{
    use HasFactory;

    protected $table = 'alertas';

    const SITUACAO_PENDENTE  = 'pendente';
    const SITUACAO_LIDO      = 'lido';
    const SITUACAO_RESOLVIDO = 'resolvido';

    protected $fillable = [
        'produto_id',
        'mensagem',
        'quantidade_atual_registrada',
        'quantidade_minima_registrada',
        'situacao',
    ];

    protected function casts(): array
    {
        return [
            'quantidade_atual_registrada'  => 'decimal:3',
            'quantidade_minima_registrada' => 'decimal:3',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'alerta_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPendente(): bool
    {
        return $this->situacao === self::SITUACAO_PENDENTE;
    }
}
