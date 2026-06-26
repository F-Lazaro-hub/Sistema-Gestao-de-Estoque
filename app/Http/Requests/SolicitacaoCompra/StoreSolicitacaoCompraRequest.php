<?php

namespace App\Http\Requests\SolicitacaoCompra;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitacaoCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temAlgumPerfil('admin', 'gerente', 'comprador');
    }

    public function rules(): array
    {
        return [
            'observacoes'                  => ['nullable', 'string', 'max:1000'],
            'itens'                        => ['required', 'array', 'min:1'],
            'itens.*.produto_id'           => ['required', 'exists:produtos,id'],
            'itens.*.quantidade'           => ['required', 'integer', 'min:1'],
            'itens.*.valor_unitario'       => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'itens.required'                  => 'Adicione ao menos um item à solicitação.',
            'itens.min'                       => 'Adicione ao menos um item à solicitação.',
            'itens.*.produto_id.required'     => 'Selecione o produto do item :position.',
            'itens.*.produto_id.exists'       => 'Produto inválido no item :position.',
            'itens.*.quantidade.required'     => 'Informe a quantidade do item :position.',
            'itens.*.quantidade.min'          => 'A quantidade mínima por item é 1.',
            'itens.*.valor_unitario.required' => 'Informe o valor unitário do item :position.',
            'itens.*.valor_unitario.min'      => 'O valor unitário deve ser maior que zero.',
        ];
    }
}
