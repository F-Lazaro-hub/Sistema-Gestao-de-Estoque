@extends('layouts.app')

@section('title', 'Categorias')

@section('content')

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('categorias.create') }}" class="btn btn-gc-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nova categoria
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                        <tr>
                            <td class="fw-semibold">{{ $categoria->nome }}</td>
                            <td class="text-muted">{{ $categoria->descricao ?? '—' }}</td>
                            <td><x-ativo :value="$categoria->ativo" /></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-outline-danger" title="Excluir"
                                        data-confirm-delete
                                        data-url="{{ route('categorias.destroy', $categoria) }}"
                                        data-title="Excluir categoria"
                                        data-message="Excluir a categoria &quot;{{ $categoria->nome }}&quot;? Produtos vinculados podem ser afetados.">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-empty-state icon="bi-tags" title="Nenhuma categoria cadastrada" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($categorias, 'links'))
            <div class="card-footer bg-white">{{ $categorias->links() }}</div>
        @endif
    </div>

@endsection
