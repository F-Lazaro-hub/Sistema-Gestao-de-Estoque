@extends('layouts.app')

@section('title', 'Detalhes da categoria')

@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>{{ $categoria->nome }}</strong>
                    <x-ativo :value="$categoria->ativo" />
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $categoria->descricao ?? 'Sem descrição cadastrada.' }}</p>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-gc-primary"><i class="bi bi-pencil"></i> Editar</a>
                    <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-light">Voltar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
