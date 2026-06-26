<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Guard padrão de autenticação
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard'     => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'usuarios'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de autenticação
    |--------------------------------------------------------------------------
    | O guard 'web' usa sessão + o provider 'usuarios' (EloquentUserProvider
    | apontando para App\Models\Usuario).
    */
    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'usuarios',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers de usuário
    |--------------------------------------------------------------------------
    | Aponta para App\Models\Usuario em vez do padrão App\Models\User.
    */
    'providers' => [
        'usuarios' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Usuario::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Broker de redefinição de senha
    |--------------------------------------------------------------------------
    | O broker 'usuarios' usa a tabela padrão do Laravel 'password_reset_tokens'
    | (mantida com nome em inglês por ser tabela interna do framework — veja
    | Decisão 6 no plano de implementação).
    */
    'passwords' => [
        'usuarios' => [
            'provider' => 'usuarios',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,   // minutos
            'throttle' => 60,   // segundos entre reenvios
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout de confirmação de senha
    |--------------------------------------------------------------------------
    */
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
