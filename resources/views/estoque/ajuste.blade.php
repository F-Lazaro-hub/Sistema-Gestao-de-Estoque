@extends('layouts.app')

@section('title', 'Ajustar estoque')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><strong>{{ $produto->codigo }} — {{ $produto->nome }}</strong></div>
                <div class="card-body">

                    <div class="alert alert-light border d-flex justify-content-between mb-4">
                        <span>Quantidade atual em estoque</span>
                        <strong>{{ rtrim(rtrim(number_format($produto->estoque->quantidade_atual ?? 0, 2, ',', '.'), '0'), ',') }} {{ $produto->unidade }}</strong>
                    </div>

                    <form method="POST" action="{{ route('estoque.ajustar', $produto) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label gc-required">Nova quantidade</label>
                            <input type="number" step="0.01" min="0" name="quantidade"
                                   value="{{ old('quantidade', $produto->estoque->quantidade_atual ?? 0) }}"
                                   class="form-control @error('quantidade') is-invalid @enderror">
                            @error('quantidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Informe a quantidade encontrada após a contagem física. O sistema registrará a diferença como um ajuste.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label gc-required">Motivo do ajuste</label>
                            <textarea name="motivo" rows="3" class="form-control @error('motivo') is-invalid @enderror" placeholder="Ex.: contagem física, produto avariado, divergência de inventário">{{ old('motivo') }}</textarea>
                            @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-gc-primary"><i class="bi bi-check-lg"></i> Confirmar ajuste</button>
                            <a href="{{ route('estoque.show', $produto) }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
