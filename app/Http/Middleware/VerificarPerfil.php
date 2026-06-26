<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware VerificarPerfil
 *
 * Restringe o acesso a rotas com base no código do perfil do usuário autenticado.
 *
 * Uso nas rotas:
 *   Route::middleware(['auth', 'perfil:admin'])->group(...);
 *   Route::middleware(['auth', 'perfil:admin,gerente'])->group(...);
 *
 * Os códigos disponíveis são os valores da coluna 'codigo' da tabela 'perfis':
 *   admin, gerente, comprador, almoxarife, financeiro
 */
class VerificarPerfil
{
    public function handle(Request $request, Closure $next, string ...$perfisPermitidos): Response
    {
        // Garante que o usuário está autenticado
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $codigoPerfil = $request->user()->perfil?->codigo;

        // Verifica se o perfil do usuário está entre os permitidos
        if (! in_array($codigoPerfil, $perfisPermitidos, true)) {
            abort(403, 'Seu perfil não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
