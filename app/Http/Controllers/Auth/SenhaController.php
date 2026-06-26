<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SenhaController extends Controller
{
    // ─── Esqueci minha senha: formulário de e-mail ────────────────────────────

    public function create(): View
    {
        return view('auth.passwords.email');
    }

    /**
     * Envia o link de redefinição para o e-mail informado.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email'),
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __('Link de redefinição enviado para seu e-mail.'))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }

    // ─── Redefinir senha: formulário com token ────────────────────────────────

    public function edit(Request $request): View
    {
        return view('auth.passwords.reset', [
            'token' => $request->route('token'),
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Valida o token e atualiza a senha do usuário.
     * Atualiza o campo 'senha' (em vez de 'password') no registro do usuário.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token'             => ['required'],
            'email'             => ['required', 'email'],
            'senha'             => ['required', 'confirmed', 'min:8'],
            'senha_confirmation' => ['required'],
        ], [
            'senha.required'             => 'A nova senha é obrigatória.',
            'senha.confirmed'            => 'As senhas não coincidem.',
            'senha.min'                  => 'A senha deve ter no mínimo 8 caracteres.',
            'senha_confirmation.required' => 'Confirme a nova senha.',
        ]);

        $status = Password::reset(
            array_merge($request->only('email', 'token'), [
                'password' => $request->input('senha'), // mapeia para o campo interno do broker
            ]),
            function (Usuario $usuario, string $password) {
                $usuario->forceFill([
                    'senha'          => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            },
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __('Senha redefinida com sucesso. Faça login.'))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }
}
