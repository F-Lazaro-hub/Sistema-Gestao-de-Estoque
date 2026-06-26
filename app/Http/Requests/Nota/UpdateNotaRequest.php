<?php

namespace App\Http\Requests\Nota;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data'                    => ['required', 'date', 'before_or_equal:today'],
            'itens'                   => ['required', 'array', 'min:1'],
            'itens.*.produto_id'      => ['required', 'integer', 'exists:produtos,id'],
            'itens.*.quantidade'      => ['required', 'numeric', 'min:0.001', 'max:999999.999'],
            'itens.*.valor'           => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.required'                  => 'A data da nota é obrigatória.',
            'data.date'                      => 'Informe uma data válida.',
            'data.before_or_equal'           => 'A data não pode ser futura.',
            'itens.required'                 => 'Adicione pelo menos um item à nota.',
            'itens.min'                      => 'A nota deve ter pelo menos 1 item.',
            'itens.*.produto_id.required'    => 'Selecione o produto do item :position.',
            'itens.*.produto_id.exists'      => 'O produto do item :position não foi encontrado.',
            'itens.*.quantidade.required'    => 'Informe a quantidade do item :position.',
            'itens.*.quantidade.min'         => 'A quantidade do item :position deve ser maior que zero.',
            'itens.*.valor.required'         => 'Informe o valor unitário do item :position.',
            'itens.*.valor.min'              => 'O valor do item :position deve ser maior que zero.',
        ];
    }
}