<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'login',
        'email',
        'senha',
        'perfil_id',
        'ativo',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'ativo'  => 'boolean',
            'senha'  => 'hashed',
        ];
    }

    /**
     * Retorna a senha hasheada para o sistema de autenticação do Laravel.
     * Necessário pois a coluna foi renomeada de 'password' para 'senha'.
     */
    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    /**
     * Retorna o e-mail utilizado para envio do link de redefinição de senha.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function perfil(): BelongsTo
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    public function movimentacoesEstoque(): HasMany
    {
        return $this->hasMany(MovimentacaoEstoque::class, 'usuario_id');
    }

    /** Solicitações de compra abertas por este usuário. */
    public function solicitacoesComoSolicitante(): HasMany
    {
        return $this->hasMany(SolicitacaoCompra::class, 'solicitante_id');
    }

    /** Solicitações de compra nas quais este usuário atuou como aprovador. */
    public function solicitacoesComoAprovador(): HasMany
    {
        return $this->hasMany(SolicitacaoCompra::class, 'aprovador_id');
    }

    public function movimentacoesFinanceiras(): HasMany
    {
        return $this->hasMany(MovimentacaoFinanceira::class, 'usuario_id');
    }

    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'usuario_id');
    }

    /** Notas internas emitidas por este usuário (responsável). */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'responsavel_id');
    }

    public function logsAuditoria(): HasMany
    {
        return $this->hasMany(LogAuditoria::class, 'usuario_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Verifica se o usuário possui o perfil informado pelo código. */
    public function temPerfil(string $codigoPerfil): bool
    {
        return $this->perfil?->codigo === $codigoPerfil;
    }

    /** Verifica se o usuário possui qualquer um dos perfis informados. */
    public function temAlgumPerfil(string ...$codigos): bool
    {
        return in_array($this->perfil?->codigo, $codigos, true);
    }
}
