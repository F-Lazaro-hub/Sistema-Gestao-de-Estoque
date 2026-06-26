<?php

namespace App\Http\Requests\Produto;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temAlgumPerfil('admin', 'gerente');
    }

    public function rules(): array
    {
        return [
            'nome'              => ['required', 'string', 'max:255'],
            'codigo'            => ['required', 'string', 'max:50', 'unique:produtos,codigo'],
            'descricao'         => ['nullable', 'string', 'max:1000'],
            'categoria_id'      => ['required', 'exists:categorias,id'],
            'marca'             => ['nullable', 'string', 'max:100'],
            'unidade'           => ['required', 'string', 'max:20'],
            'quantidade_minima' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'              => 'O nome do produto é obrigatório.',
            'codigo.required'            => 'O código do produto é obrigatório.',
            'codigo.unique'              => 'Este código já está cadastrado.',
            'categoria_id.required'      => 'Selecione uma categoria.',
            'categoria_id.exists'        => 'Categoria inválida.',
            'unidade.required'           => 'A unidade de medida é obrigatória.',
            'quantidade_minima.required' => 'Informe a quantidade mínima de estoque.',
            'quantidade_minima.min'      => 'A quantidade mínima não pode ser negativa.',
        ];
    }
}
