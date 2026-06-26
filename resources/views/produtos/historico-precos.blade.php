@extends('layouts.app')

@section('title', 'Histórico de preços')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="gc-heading h5 mb-0">{{ $produto->codigo }} — {{ $produto->nome }}</h2>
        <a href="{{ route('produtos.show', $produto) }}" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i> Voltar ao produto</a>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-white"><strong><i class="bi bi-graph-up"></i> Evolução do valor</strong></div>
        <div class="card-body">
            @if(($historicos->count() ?? 0) > 0)
                <canvas id="gc-grafico-precos" height="90"></canvas>
            @else
                <x-empty-state icon="bi-graph-up" title="Ainda não há histórico de preços para este produto" />
            @endif
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Valor pago</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historicos as $historico)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($historico->data)->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format($historico->valor, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2"><x-empty-state icon="bi-graph-up" title="Nenhum registro de preço" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($historicos, 'links'))
            <div class="card-footer bg-white">{{ $historicos->links() }}</div>
        @endif
    </div>

@endsection

@push('scripts')
@if(($historicos->count() ?? 0) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('gc-grafico-precos');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [@foreach($historicos->sortBy('data') as $h){{ "'" . \Carbon\Carbon::parse($h->data)->format('d/m/Y') . "'" }},@endforeach],
            datasets: [{
                label: 'Valor pago (R$)',
                data: [@foreach($historicos->sortBy('data') as $h){{ $h->valor }},@endforeach],
                borderColor: '#2EC4B6',
                backgroundColor: 'rgba(46,196,182,.15)',
                tension: .3,
                fill: true,
                pointBackgroundColor: '#16243B'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { ticks: { callback: (v) => 'R$ ' + v } } }
        }
    });
</script>
@endif
@endpush
