@extends('layouts.app')

@section('title', 'Produtos')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control form-control-sm" style="width: 220px" placeholder="Buscar por código ou nome">
            <select name="categoria_id" class="form-select form-select-sm" style="width: 180px">
                <option value="">Todas as categorias</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>{{ $categoria->nome }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('produtos.create') }}" class="btn btn-gc-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Novo produto
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Valor médio</th>
                        <th>Estoque atual</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                        <tr>
                            <td>{{ $produto->codigo }}</td>
                            <td class="fw-semibold">{{ $produto->nome }}</td>
                            <td>{{ $produto->categoria->nome ?? '—' }}</td>
                            <td>{{ $produto->marca ?? '—' }}</td>
                            <td>R$ {{ number_format($produto->valor_medio ?? 0, 2, ',', '.') }}</td>
                            <td>
                                @if($produto->estoque)
                                    {{ rtrim(rtrim(number_format($produto->estoque->quantidade_atual, 2, ',', '.'), '0'), ',') }} {{ $produto->unidade }}
                                    @if($produto->estoque->estaBaixoDoMinimo())
                                        <span class="badge-situacao badge-aberto ms-1">baixo</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><x-ativo :value="$produto->ativo" /></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('produtos.show', $produto) }}" class="btn btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <a href="{{ route('produtos.historicoPrecos', $produto) }}" class="btn btn-outline-secondary" title="Histórico de preços"><i class="bi bi-graph-up"></i></a>
                                    <button type="button" class="btn btn-outline-danger" title="Excluir"
                                        data-confirm-delete
                                        data-url="{{ route('produtos.destroy', $produto) }}"
                                        data-title="Excluir produto"
                                        data-message="Excluir o produto &quot;{{ $produto->nome }}&quot;? Essa ação não pode ser desfeita.">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="bi-box-seam" title="Nenhum produto cadastrado" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($produtos, 'links'))
            <div class="card-footer bg-white">{{ $produtos->links() }}</div>
        @endif
    </div>

@endsection
