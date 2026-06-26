<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAuditoria extends Model
{
    use HasFactory;

    protected $table = 'logs_auditoria';

    /**
     * Logs de auditoria são imutáveis — desabilita updated_at.
     * O created_at ainda é usado para registrar o momento da ação.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'acao',
        'tabela',
        'registro_id',
        'valores_antigos',
        'valores_novos',
    ];

    protected function casts(): array
    {
        return [
            'valores_antigos' => 'array',
            'valores_novos'   => 'array',
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // ─── Factory method ───────────────────────────────────────────────────────

    /**
     * Registra uma entrada de auditoria de forma conveniente.
     *
     * @param  string      $acao           ex.: 'criar', 'atualizar', 'excluir'
     * @param  string      $tabela         nome da tabela afetada
     * @param  int         $registroId     ID do registro afetado
     * @param  array|null  $valoresAntigos estado anterior (null para criações)
     * @param  array|null  $valoresNovos   estado posterior (null para exclusões)
     */
    public static function registrar(
        string $acao,
        string $tabela,
        int $registroId,
        ?array $valoresAntigos = null,
        ?array $valoresNovos = null
    ): static {
        return static::create([
            'usuario_id'      => auth()->id(),
            'acao'            => $acao,
            'tabela'          => $tabela,
            'registro_id'     => $registroId,
            'valores_antigos' => $valoresAntigos,
            'valores_novos'   => $valoresNovos,
        ]);
    }
}
