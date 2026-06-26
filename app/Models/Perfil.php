<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfis';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
    ];

    // ─── Relacionamentos ───────────────────────────────────────────────────────

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'perfil_id');
    }
}
