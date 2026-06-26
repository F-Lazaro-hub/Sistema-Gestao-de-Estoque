<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'senha' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'senha.required' => 'A senha é obrigatória.',
        ];
    }

    /**
     * Tenta autenticar o usuário com as credenciais informadas.
     * Mapeia o campo 'senha' do formulário para 'password' (chave interna do Auth).
     *
     * @throws ValidationException
     */
    public function autenticar(): void
    {
        $this->verificarLimiteTentativas();

        $autenticado = Auth::attempt(
            credentials: [
                'email'    => $this->string('email'),
                'password' => $this->string('senha'), // 'password' é a chave usada pelo EloquentUserProvider
                'ativo'    => true,                    // só permite login de usuários ativos
            ],
            remember: $this->boolean('lembrar'),
        );

        if (! $autenticado) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('E-mail ou senha inválidos. Verifique suas credenciais.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Bloqueia tentativas excessivas de login (máx. 5 por minuto).
     *
     * @throws ValidationException
     */
    private function verificarLimiteTentativas(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __("Muitas tentativas de login. Tente novamente em {$segundos} segundos."),
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
