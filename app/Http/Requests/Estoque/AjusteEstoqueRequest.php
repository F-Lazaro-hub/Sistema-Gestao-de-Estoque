<?php

namespace App\Http\Requests\Estoque;

use Illuminate\Foundation\Http\FormRequest;

class AjusteEstoqueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temAlgumPerfil('admin', 'almoxarife');
    }

    public function rules(): array
    {
        return [
            'quantidade' => ['required', 'integer', 'min:0'],
            'motivo'     => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantidade.required' => 'Informe a nova quantidade em estoque.',
            'quantidade.min'      => 'A quantidade não pode ser negativa.',
            'motivo.required'     => 'Informe o motivo do ajuste.',
            'motivo.min'          => 'O motivo deve ter ao menos 10 caracteres.',
        ];
    }
}
