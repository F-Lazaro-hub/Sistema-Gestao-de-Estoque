@extends('layouts.app')

@section('title', 'Extrato financeiro')

@section('content')

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="entrada" @selected(request('tipo') === 'entrada')>Entrada</option>
                        <option value="saida" @selected(request('tipo') === 'saida')>Saída</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">De</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Até</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-gc-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="{{ route('financeiro.extrato') }}" class="btn btn-sm btn-light">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Solicitação</th>
                        <th>Usuário</th>
                        <th class="text-end">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimentacoes as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->data)->format('d/m/Y') }}</td>
                            <td><x-situacao :value="$mov->tipo" /></td>
                            <td>{{ $mov->descricao }}</td>
                            <td>
                                @if($mov->solicitacao_compra_id)
                                    <a href="{{ route('compras.show', $mov->solicitacao_compra_id) }}">#{{ $mov->solicitacao_compra_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $mov->usuario->nome ?? '—' }}</td>
                            <td class="text-end {{ $mov->tipo === 'saida' ? 'text-danger' : 'text-success' }}">
                                {{ $mov->tipo === 'saida' ? '-' : '+' }} R$ {{ number_format($mov->valor, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="bi-receipt" title="Nenhuma movimentação encontrada para o período" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($movimentacoes, 'links'))
            <div class="card-footer bg-white">{{ $movimentacoes->appends(request()->query())->links() }}</div>
        @endif
    </div>

@endsection
