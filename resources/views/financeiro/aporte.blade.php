@extends('layouts.app')

@section('title', 'Novo aporte')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <strong>Saldo atual: R$ {{ number_format($caixa->saldo_atual ?? 0, 2, ',', '.') }}</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('financeiro.aporte.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label gc-required">Valor do aporte</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" min="0.01" name="valor" value="{{ old('valor') }}"
                                       class="form-control @error('valor') is-invalid @enderror">
                                @error('valor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label gc-required">Descrição</label>
                            <input type="text" name="descricao" value="{{ old('descricao') }}" placeholder="Ex.: aporte de sócio, transferência bancária"
                                   class="form-control @error('descricao') is-invalid @enderror">
                            @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-gc-primary"><i class="bi bi-check-lg"></i> Registrar aporte</button>
                            <a href="{{ route('financeiro.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
