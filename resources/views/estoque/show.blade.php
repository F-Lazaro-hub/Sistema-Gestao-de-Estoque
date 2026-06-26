@extends('layouts.app')

@section('title', 'Detalhes do estoque')

@section('content')
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white"><strong>{{ $produto->codigo }} — {{ $produto->nome }}</strong></div>
                <div class="card-body">
                    @if($produto->estoque)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Quantidade atual</span>
                            <strong>{{ rtrim(rtrim(number_format($produto->estoque->quantidade_atual, 2, ',', '.'), '0'), ',') }} {{ $produto->unidade }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Quantidade mínima</span>
                            <strong>{{ rtrim(rtrim(number_format($produto->estoque->quantidade_minima, 2, ',', '.'), '0'), ',') }} {{ $produto->unidade }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Situação</span>
                            @if($produto->estoque->estaBaixoDoMinimo())
                                <span class="badge-situacao badge-aberto">Baixo</span>
                            @else
                                <span class="badge-situacao badge-ativo">Normal</span>
                            @endif
                        </div>
                    @else
                        <x-empty-state icon="bi-boxes" title="Sem registro de estoque" />
                    @endif
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('estoque.ajuste', $produto) }}" class="btn btn-sm btn-gc-primary"><i class="bi bi-sliders"></i> Ajustar estoque</a>
                    <a href="{{ route('estoque.index') }}" class="btn btn-sm btn-light">Voltar</a>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-arrow-left-right"></i> Últimas movimentações</strong>
                    <a href="{{ route('estoque.movimentacoes', ['produto_id' => $produto->id]) }}" class="small">Ver todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-gc mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($movimentacoes ?? []) as $mov)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mov->data_movimentacao)->format('d/m/Y') }}</td>
                                    <td><x-situacao :value="$mov->tipo" /></td>
                                    <td>{{ rtrim(rtrim(number_format($mov->quantidade, 2, ',', '.'), '0'), ',') }}</td>
                                    <td class="text-muted">{{ $mov->motivo ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><x-empty-state icon="bi-arrow-left-right" title="Nenhuma movimentação registrada" /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
