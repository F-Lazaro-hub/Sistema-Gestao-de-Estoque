@extends('layouts.app')

@section('title', 'Solicitações de compra')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex gap-2">
            <select name="situacao" class="form-select form-select-sm" style="width: 180px">
                <option value="">Todas as situações</option>
                <option value="pendente" @selected(request('situacao') === 'pendente')>Pendente</option>
                <option value="aprovada" @selected(request('situacao') === 'aprovada')>Aprovada</option>
                <option value="reprovada" @selected(request('situacao') === 'reprovada')>Reprovada</option>
                <option value="cancelada" @selected(request('situacao') === 'cancelada')>Cancelada</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-funnel"></i></button>
        </form>

        @if(auth()->user()->temAlgumPerfil('admin','gerente','comprador'))
            <a href="{{ route('compras.create') }}" class="btn btn-gc-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nova solicitação
            </a>
        @endif
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Data</th>
                        <th>Valor total</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitacoes as $solicitacao)
                        <tr>
                            <td>#{{ $solicitacao->id }}</td>
                            <td>{{ $solicitacao->solicitante->nome ?? '—' }}</td>
                            <td>{{ optional($solicitacao->created_at)->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format($solicitacao->valor_total, 2, ',', '.') }}</td>
                            <td><x-situacao :value="$solicitacao->situacao" /></td>
                            <td class="text-end">
                                <a href="{{ route('compras.show', $solicitacao) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="bi-cart-check" title="Nenhuma solicitação encontrada" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($solicitacoes, 'links'))
            <div class="card-footer bg-white">{{ $solicitacoes->appends(request()->query())->links() }}</div>
        @endif
    </div>

@endsection
