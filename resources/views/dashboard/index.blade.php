@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-stat-card
                icon="bi-cash-stack"
                label="Saldo do caixa"
                :value="'R$ ' . number_format($saldoAtual ?? 0, 2, ',', '.')"
                tone="navy"
                :href="auth()->user()->temAlgumPerfil('admin','financeiro') ? route('financeiro.index') : null"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card
                icon="bi-exclamation-triangle"
                label="Alertas abertos"
                :value="$alertasAbertos ?? 0"
                tone="red"
                :href="route('alertas.index')"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card
                icon="bi-box-seam"
                label="Produtos abaixo do mínimo"
                :value="$produtosBaixoEstoque ?? 0"
                tone="amber"
                :href="auth()->user()->temAlgumPerfil('admin','almoxarife') ? route('estoque.index') : null"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card
                icon="bi-cart-check"
                label="Solicitações pendentes"
                :value="$solicitacoesPendentes ?? 0"
                tone="teal"
                :href="auth()->user()->temAlgumPerfil('admin','gerente','comprador') ? route('compras.index') : null"
            />
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-exclamation-triangle text-danger"></i> Alertas em aberto</strong>
                    <a href="{{ route('alertas.index') }}" class="small">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-gc mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Atual</th>
                                <th>Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($ultimosAlertas ?? []) as $alerta)
                                <tr>
                                    <td>{{ $alerta->produto->nome ?? '—' }}</td>
                                    <td>{{ rtrim(rtrim(number_format($alerta->quantidade_atual_registrada, 2, ',', '.'), '0'), ',') }}</td>
                                    <td>{{ rtrim(rtrim(number_format($alerta->quantidade_minima_registrada, 2, ',', '.'), '0'), ',') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><x-empty-state icon="bi-check2-circle" title="Nenhum alerta em aberto" /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-cart-check text-warning"></i> Solicitações pendentes</strong>
                    <a href="{{ route('compras.index') }}" class="small">Ver todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-gc mb-0">
                        <thead>
                            <tr>
                                <th>Solicitante</th>
                                <th>Valor</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($ultimasSolicitacoes ?? []) as $solicitacao)
                                <tr>
                                    <td><a href="{{ route('compras.show', $solicitacao) }}">{{ $solicitacao->solicitante->nome ?? '—' }}</a></td>
                                    <td>R$ {{ number_format($solicitacao->valor_total, 2, ',', '.') }}</td>
                                    <td><x-situacao :value="$solicitacao->situacao" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><x-empty-state icon="bi-check2-circle" title="Nenhuma solicitação pendente" /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
