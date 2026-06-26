<?php

namespace App\Http\Requests\Financeiro;

use Illuminate\Foundation\Http\FormRequest;

class AporteFinanceiroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temAlgumPerfil('admin', 'financeiro');
    }

    public function rules(): array
    {
        return [
            'valor'     => ['required', 'numeric', 'min:0.01'],
            'descricao' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'valor.required'     => 'Informe o valor do aporte.',
            'valor.min'          => 'O valor deve ser maior que zero.',
            'descricao.required' => 'Informe uma descrição para o aporte.',
            'descricao.min'      => 'A descrição deve ter ao menos 5 caracteres.',
        ];
    }
}
