<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /*
        |----------------------------------------------------------------------
        | Alias do middleware de perfil
        |----------------------------------------------------------------------
        | Registra o alias 'perfil' para uso nas definições de rotas:
        |   Route::middleware(['auth', 'perfil:admin,gerente'])->group(...)
        */
        $middleware->alias([
            'perfil' => \App\Http\Middleware\VerificarPerfil::class,
        ]);

        /*
        |----------------------------------------------------------------------
        | Redirecionamentos de autenticação
        |----------------------------------------------------------------------
        | Visitantes não autenticados → /login
        | Usuários já autenticados tentando acessar rotas guest → /dashboard
        */
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
