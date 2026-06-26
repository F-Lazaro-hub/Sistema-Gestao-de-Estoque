<?php

namespace App\Http\Requests\Categoria;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->temPerfil('admin');
    }

    public function rules(): array
    {
        $id = $this->route('categoria');

        return [
            'nome'      => ['required', 'string', 'max:100', "unique:categorias,nome,{$id}"],
            'descricao' => ['nullable', 'string', 'max:500'],
            'ativo'     => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique'   => 'Já existe uma categoria com este nome.',
            'nome.max'      => 'O nome não pode ter mais de 100 caracteres.',
        ];
    }
}
