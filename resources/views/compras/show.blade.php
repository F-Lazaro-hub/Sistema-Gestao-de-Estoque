@extends('layouts.app')

@section('title', 'Solicitação #' . $solicitacao->id)

@section('content')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Solicitação #{{ $solicitacao->id }}</strong>
                    <x-situacao :value="$solicitacao->situacao" />
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3 text-muted">Solicitante</dt>
                        <dd class="col-sm-9">{{ $solicitacao->solicitante->nome ?? '—' }}</dd>

                        <dt class="col-sm-3 text-muted">Data</dt>
                        <dd class="col-sm-9">{{ optional($solicitacao->created_at)->format('d/m/Y H:i') }}</dd>

                        @if($solicitacao->aprovador)
                            <dt class="col-sm-3 text-muted">{{ $solicitacao->situacao === 'reprovada' ? 'Reprovado por' : 'Aprovado por' }}</dt>
                            <dd class="col-sm-9">{{ $solicitacao->aprovador->nome }} em {{ optional($solicitacao->aprovado_em)->format('d/m/Y H:i') }}</dd>
                        @endif

                        @if($solicitacao->observacoes)
                            <dt class="col-sm-3 text-muted">Observações</dt>
                            <dd class="col-sm-9">{{ $solicitacao->observacoes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white"><strong>Itens</strong></div>
                <div class="table-responsive">
                    <table class="table table-gc mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitacao->itens as $item)
                                <tr>
                                    <td>{{ $item->produto->nome ?? '—' }}</td>
                                    <td>{{ rtrim(rtrim(number_format($item->quantidade, 2, ',', '.'), '0'), ',') }}</td>
                                    <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th>R$ {{ number_format($solicitacao->valor_total, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><strong>Ações</strong></div>
                <div class="card-body d-flex flex-column gap-2">

                    @if($solicitacao->situacao === 'pendente' && auth()->user()->temAlgumPerfil('admin','gerente'))
                        <form method="POST" action="{{ route('compras.aprovar', $solicitacao) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-gc-primary w-100"><i class="bi bi-check-circle"></i> Aprovar solicitação</button>
                        </form>

                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#gc-reprovar-modal">
                            <i class="bi bi-x-circle"></i> Reprovar solicitação
                        </button>
                    @endif

                    @if($solicitacao->situacao === 'pendente' && ($solicitacao->solicitante_id === auth()->id() || auth()->user()->temPerfil('admin')))
                        <button type="button" class="btn btn-outline-secondary w-100"
                            data-confirm-delete
                            data-method="patch"
                            data-url="{{ route('compras.cancelar', $solicitacao) }}"
                            data-title="Cancelar solicitação"
                            data-message="Cancelar esta solicitação de compra? Essa ação não pode ser desfeita.">
                            <i class="bi bi-slash-circle"></i> Cancelar solicitação
                        </button>
                    @endif

                    <a href="{{ route('compras.index') }}" class="btn btn-light w-100">Voltar à lista</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de reprovação (exige motivo) --}}
    <div class="modal fade" id="gc-reprovar-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('compras.reprovar', $solicitacao) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Reprovar solicitação #{{ $solicitacao->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label gc-required">Motivo da reprovação</label>
                        <textarea name="motivo" rows="3" class="form-control @error('motivo') is-invalid @enderror" required>{{ old('motivo') }}</textarea>
                        @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar reprovação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
