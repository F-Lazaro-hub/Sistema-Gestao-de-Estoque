<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;

    protected $table = 'caixa';

    protected $fillable = [
        'saldo_atual',
    ];

    protected function casts(): array
    {
        return [
            'saldo_atual' => 'decimal:2',
        ];
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retorna o registro único de caixa.
     * Lança exceção se o registro ainda não foi criado via seeder/migration.
     */
    public static function instancia(): static
    {
        return static::firstOrFail();
    }

    /** Verifica se há saldo suficiente para cobrir o valor informado. */
    public function temSaldo(float|string $valor): bool
    {
        return bccomp((string) $this->saldo_atual, (string) $valor, 2) >= 0;
    }
}
