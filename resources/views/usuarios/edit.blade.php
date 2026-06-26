@extends('layouts.app')

@section('title', 'Editar usuário')

@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
                        @csrf
                        @method('PUT')
                        @include('usuarios._form', ['usuario' => $usuario])

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-gc-primary"><i class="bi bi-check-lg"></i> Salvar alterações</button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
