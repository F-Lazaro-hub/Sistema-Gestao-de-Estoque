@extends('layouts.app')

@section('title', 'Detalhes do produto')

@section('content')
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>{{ $produto->codigo }} — {{ $produto->nome }}</strong>
                    <x-ativo :value="$produto->ativo" />
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Categoria</dt>
                        <dd class="col-sm-8">{{ $produto->categoria->nome ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Marca</dt>
                        <dd class="col-sm-8">{{ $produto->marca ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Unidade</dt>
                        <dd class="col-sm-8">{{ $produto->unidade }}</dd>

                        <dt class="col-sm-4 text-muted">Descrição</dt>
                        <dd class="col-sm-8">{{ $produto->descricao ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Último valor pago</dt>
                        <dd class="col-sm-8">R$ {{ number_format($produto->ultimo_valor_pago ?? 0, 2, ',', '.') }}</dd>

                        <dt class="col-sm-4 text-muted">Valor médio</dt>
                        <dd class="col-sm-8">R$ {{ number_format($produto->valor_medio ?? 0, 2, ',', '.') }}</dd>
                    </dl>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-gc-primary"><i class="bi bi-pencil"></i> Editar</a>
                    <a href="{{ route('produtos.historicoPrecos', $produto) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-graph-up"></i> Histórico de preços</a>
                    <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-light">Voltar</a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white"><strong><i class="bi bi-boxes"></i> Estoque</strong></div>
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
                            <span class="text-muted">Última movimentação</span>
                            <strong>{{ optional($produto->estoque->ultima_movimentacao_em)->format('d/m/Y H:i') ?? '—' }}</strong>
                        </div>
                        @if($produto->estoque->estaBaixoDoMinimo())
                            <div class="alert alert-warning mt-2 mb-0 py-2 px-3 small"><i class="bi bi-exclamation-triangle"></i> Estoque abaixo do mínimo configurado.</div>
                        @endif
                    @else
                        <x-empty-state icon="bi-boxes" title="Sem registro de estoque ainda" />
                    @endif
                </div>
                @if(auth()->user()->temAlgumPerfil('admin','almoxarife'))
                    <div class="card-footer bg-white">
                        <a href="{{ route('estoque.show', $produto) }}" class="btn btn-sm btn-outline-secondary w-100">Ver no módulo de Estoque</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
