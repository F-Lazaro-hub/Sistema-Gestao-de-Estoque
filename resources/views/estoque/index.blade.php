@extends('layouts.app')

@section('title', 'Estoque')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control form-control-sm" style="width: 220px" placeholder="Buscar por código ou nome">
            <select name="situacao" class="form-select form-select-sm" style="width: 180px">
                <option value="">Todos os produtos</option>
                <option value="baixo" @selected(request('situacao') === 'baixo')>Apenas abaixo do mínimo</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('estoque.movimentacoes') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left-right"></i> Ver movimentações
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Quantidade atual</th>
                        <th>Quantidade mínima</th>
                        <th>Última movimentação</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                        <tr>
                            <td>{{ $produto->codigo }}</td>
                            <td class="fw-semibold">{{ $produto->nome }}</td>
                            <td>{{ rtrim(rtrim(number_format($produto->estoque->quantidade_atual ?? 0, 2, ',', '.'), '0'), ',') }} {{ $produto->unidade }}</td>
                            <td>{{ rtrim(rtrim(number_format($produto->estoque->quantidade_minima ?? 0, 2, ',', '.'), '0'), ',') }} {{ $produto->unidade }}</td>
                            <td>{{ optional($produto->estoque->ultima_movimentacao_em ?? null)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                @if($produto->estoque && $produto->estoque->estaBaixoDoMinimo())
                                    <span class="badge-situacao badge-aberto">Baixo</span>
                                @else
                                    <span class="badge-situacao badge-ativo">Normal</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('estoque.show', $produto) }}" class="btn btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('estoque.ajuste', $produto) }}" class="btn btn-outline-secondary" title="Ajustar"><i class="bi bi-sliders"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty-state icon="bi-boxes" title="Nenhum produto com estoque cadastrado" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($produtos, 'links'))
            <div class="card-footer bg-white">{{ $produtos->links() }}</div>
        @endif
    </div>

@endsection
