<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temPerfil('admin');
    }

    public function rules(): array
    {
        // Ignora o próprio registro na verificação de e-mail único
        $id = $this->route('usuario');

        return [
            'nome'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', "unique:usuarios,email,{$id}"],
            'senha'     => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'perfil_id' => ['required', 'exists:perfis,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'      => 'O nome é obrigatório.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.unique'       => 'Este e-mail já está em uso por outro usuário.',
            'senha.confirmed'    => 'A confirmação de senha não confere.',
            'perfil_id.required' => 'Selecione um perfil.',
            'perfil_id.exists'   => 'Perfil inválido.',
        ];
    }
}
