@extends('layouts.app')

@section('title', 'Financeiro')

@section('content')

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <x-stat-card icon="bi-cash-stack" label="Saldo atual do caixa" :value="'R$ ' . number_format($caixa->saldo_atual ?? 0, 2, ',', '.')" tone="navy" />
        </div>
        <div class="col-md-8 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <strong>Precisa registrar um novo aporte?</strong>
                        <p class="text-muted mb-0 small">Lance entradas de caixa para liberar a aprovação de novas solicitações de compra.</p>
                    </div>
                    <a href="{{ route('financeiro.aporte') }}" class="btn btn-gc-primary text-nowrap"><i class="bi bi-plus-lg"></i> Novo aporte</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-receipt"></i> Últimas movimentações</strong>
            <a href="{{ route('financeiro.extrato') }}" class="small">Ver extrato completo</a>
        </div>
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
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
                            <td>{{ $mov->usuario->nome ?? '—' }}</td>
                            <td class="text-end {{ $mov->tipo === 'saida' ? 'text-danger' : 'text-success' }}">
                                {{ $mov->tipo === 'saida' ? '-' : '+' }} R$ {{ number_format($mov->valor, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state icon="bi-receipt" title="Nenhuma movimentação financeira registrada" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
