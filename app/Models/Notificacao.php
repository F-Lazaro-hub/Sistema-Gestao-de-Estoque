<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';

    const TIPO_ALERTA_ESTOQUE     = 'alerta_estoque';
    const TIPO_APROVACAO_COMPRA   = 'aprovacao_compra';
    const TIPO_REPROVACAO_COMPRA  = 'reprovacao_compra';
    const TIPO_GERAL              = 'geral';

    protected $fillable = [
        'usuario_id',
        'alerta_id',
        'titulo',
        'mensagem',
        'tipo',
        'lida_em',
    ];

    protected function casts(): array
    {
        return [
            'lida_em' => 'datetime',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function alerta(): BelongsTo
    {
        return $this->belongsTo(Alerta::class, 'alerta_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function foiLida(): bool
    {
        return $this->lida_em !== null;
    }

    public function marcarComoLida(): bool
    {
        if ($this->foiLida()) {
            return false;
        }

        return $this->update(['lida_em' => now()]);
    }
}
