@extends('layouts.app')

@section('title', 'Editar produto')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('produtos.update', $produto) }}">
                        @csrf
                        @method('PUT')
                        @include('produtos._form', ['produto' => $produto])
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-gc-primary"><i class="bi bi-check-lg"></i> Salvar alterações</button>
                            <a href="{{ route('produtos.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
