@extends('layouts.app')

@section('title', 'Nova solicitação de compra')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('compras.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" rows="2" class="form-control @error('observacoes') is-invalid @enderror" placeholder="Informações adicionais sobre esta solicitação (opcional)">{{ old('observacoes') }}</textarea>
                            @error('observacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>Itens da solicitação</strong>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="gcAdicionarItem(document.getElementById('gc-item-template'), document.getElementById('gc-itens-container'))">
                                <i class="bi bi-plus-lg"></i> Adicionar item
                            </button>
                        </div>

                        @error('itens') <div class="alert alert-danger small">{{ $message }}</div> @enderror

                        <div id="gc-itens-container">
                            {{-- Linha inicial (índice 0) --}}
                            <div class="gc-item-row row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small mb-1 gc-required">Produto</label>
                                    <select name="itens[0][produto_id]" class="form-select form-select-sm gc-item-produto" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto->id }}">{{ $produto->codigo }} — {{ $produto->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small mb-1 gc-required">Quantidade</label>
                                    <input type="number" step="0.01" min="0.01" name="itens[0][quantidade]" class="form-control form-control-sm gc-item-qtd" required oninput="gcRecalcularTotalItem(this.closest('.gc-item-row'))">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small mb-1 gc-required">Valor unitário</label>
                                    <input type="number" step="0.01" min="0.01" name="itens[0][valor_unitario]" class="form-control form-control-sm gc-item-valor" required oninput="gcRecalcularTotalItem(this.closest('.gc-item-row'))">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small mb-1">Subtotal</label>
                                    <div class="form-control form-control-sm bg-light gc-item-total">R$ 0,00</div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="gcRemoverItem(this)" title="Remover">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-3 mt-2">
                            <div class="text-end">
                                <div class="text-muted small">Total estimado</div>
                                <div class="fs-5 fw-bold" id="gc-total-geral">R$ 0,00</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-gc-primary"><i class="bi bi-check-lg"></i> Enviar solicitação</button>
                            <a href="{{ route('compras.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Template usado pelo JS para clonar novas linhas de item --}}
    <template id="gc-item-template">
        <div class="gc-item-row row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1 gc-required">Produto</label>
                <select name="itens[__INDEX__][produto_id]" class="form-select form-select-sm gc-item-produto" required>
                    <option value="">Selecione um produto</option>
                    @foreach($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->codigo }} — {{ $produto->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1 gc-required">Quantidade</label>
                <input type="number" step="0.01" min="0.01" name="itens[__INDEX__][quantidade]" class="form-control form-control-sm gc-item-qtd" required oninput="gcRecalcularTotalItem(this.closest('.gc-item-row'))">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1 gc-required">Valor unitário</label>
                <input type="number" step="0.01" min="0.01" name="itens[__INDEX__][valor_unitario]" class="form-control form-control-sm gc-item-valor" required oninput="gcRecalcularTotalItem(this.closest('.gc-item-row'))">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Subtotal</label>
                <div class="form-control form-control-sm bg-light gc-item-total">R$ 0,00</div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="gcRemoverItem(this)" title="Remover">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>
@endsection
