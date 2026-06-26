@extends('layouts.app')

@section('title', 'Movimentações de estoque')

@section('content')

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Produto</label>
                    <select name="produto_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($produtos as $produto)
                            <option value="{{ $produto->id }}" @selected(request('produto_id') == $produto->id)>{{ $produto->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="entrada" @selected(request('tipo') === 'entrada')>Entrada</option>
                        <option value="saida" @selected(request('tipo') === 'saida')>Saída</option>
                        <option value="ajuste" @selected(request('tipo') === 'ajuste')>Ajuste</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">De</label>
                    <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Até</label>
                    <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-gc-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="{{ route('estoque.movimentacoes') }}" class="btn btn-sm btn-light">Limpar</a>
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
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Valor unitário</th>
                        <th>Valor total</th>
                        <th>Motivo</th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimentacoes as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->data_movimentacao)->format('d/m/Y') }}</td>
                            <td>{{ $mov->produto->nome ?? '—' }}</td>
                            <td><x-situacao :value="$mov->tipo" /></td>
                            <td>{{ rtrim(rtrim(number_format($mov->quantidade, 2, ',', '.'), '0'), ',') }}</td>
                            <td>{{ $mov->valor_unitario ? 'R$ '.number_format($mov->valor_unitario, 2, ',', '.') : '—' }}</td>
                            <td>{{ $mov->valor_total ? 'R$ '.number_format($mov->valor_total, 2, ',', '.') : '—' }}</td>
                            <td class="text-muted">{{ $mov->motivo ?? '—' }}</td>
                            <td>{{ $mov->usuario->nome ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="bi-arrow-left-right" title="Nenhuma movimentação encontrada" description="Ajuste os filtros acima ou aguarde novas movimentações." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($movimentacoes, 'links'))
            <div class="card-footer bg-white">{{ $movimentacoes->appends(request()->query())->links() }}</div>
        @endif
    </div>

@endsection
