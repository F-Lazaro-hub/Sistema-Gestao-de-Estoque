<?php

namespace App\Http\Requests\SolicitacaoCompra;

use Illuminate\Foundation\Http\FormRequest;

class ReprovarSolicitacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temAlgumPerfil('admin', 'gerente');
    }

    public function rules(): array
    {
        return [
            'motivo' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'Informe o motivo da reprovação.',
            'motivo.min'      => 'O motivo deve ter ao menos 10 caracteres.',
        ];
    }
}
