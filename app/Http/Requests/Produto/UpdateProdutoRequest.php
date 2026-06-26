<?php

namespace App\Http\Requests\Produto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temAlgumPerfil('admin', 'gerente');
    }

    public function rules(): array
    {
        $id = $this->route('produto');

        return [
            'nome'              => ['required', 'string', 'max:255'],
            'codigo'            => ['required', 'string', 'max:50', "unique:produtos,codigo,{$id}"],
            'descricao'         => ['nullable', 'string', 'max:1000'],
            'categoria_id'      => ['required', 'exists:categorias,id'],
            'marca'             => ['nullable', 'string', 'max:100'],
            'unidade'           => ['required', 'string', 'max:20'],
            'quantidade_minima' => ['nullable', 'integer', 'min:0'],
            'ativo'             => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'         => 'O nome do produto é obrigatório.',
            'codigo.required'       => 'O código do produto é obrigatório.',
            'codigo.unique'         => 'Este código já está em uso por outro produto.',
            'categoria_id.required' => 'Selecione uma categoria.',
            'categoria_id.exists'   => 'Categoria inválida.',
            'unidade.required'      => 'A unidade de medida é obrigatória.',
            'quantidade_minima.min' => 'A quantidade mínima não pode ser negativa.',
        ];
    }
}
