@extends('layouts.app')

@section('title', 'Alertas')

@section('content')

    <div class="d-flex justify-content-end mb-3">
        <form method="GET" class="d-flex gap-2">
            <select name="situacao" class="form-select form-select-sm" style="width: 180px">
                <option value="">Todas as situações</option>
                <option value="aberto" @selected(request('situacao') === 'pendente')>Aberto</option>
                <option value="resolvido" @selected(request('situacao') === 'resolvido')>Resolvido</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-funnel"></i></button>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Mensagem</th>
                        <th>Qtd. atual</th>
                        <th>Qtd. mínima</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alertas as $alerta)
                        <tr>
                            <td class="fw-semibold">{{ $alerta->produto->nome ?? '—' }}</td>
                            <td class="text-muted">{{ $alerta->mensagem }}</td>
                            <td>{{ rtrim(rtrim(number_format($alerta->quantidade_atual_registrada, 2, ',', '.'), '0'), ',') }}</td>
                            <td>{{ rtrim(rtrim(number_format($alerta->quantidade_minima_registrada, 2, ',', '.'), '0'), ',') }}</td>
                            <td><x-situacao :value="$alerta->situacao" /></td>
                            <td class="text-end">
                                @if($alerta->situacao === 'pendente' && auth()->user()->temAlgumPerfil('admin','gerente'))
                                    <form method="POST" action="{{ route('alertas.resolver', $alerta) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-check2"></i> Resolver
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="bi-check2-circle" title="Nenhum alerta encontrado" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($alertas, 'links'))
            <div class="card-footer bg-white">{{ $alertas->appends(request()->query())->links() }}</div>
        @endif
    </div>

@endsection
