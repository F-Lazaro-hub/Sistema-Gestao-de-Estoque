@extends('layouts.app')

@section('title', 'Detalhes do usuário')

@section('content')
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>{{ $usuario->nome }}</strong>
                    <x-ativo :value="$usuario->ativo" />
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Login</dt>
                        <dd class="col-sm-8">{{ $usuario->login }}</dd>

                        <dt class="col-sm-4 text-muted">E-mail</dt>
                        <dd class="col-sm-8">{{ $usuario->email }}</dd>

                        <dt class="col-sm-4 text-muted">Perfil</dt>
                        <dd class="col-sm-8">{{ $usuario->perfil->nome ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">Cadastrado em</dt>
                        <dd class="col-sm-8">{{ optional($usuario->created_at)->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-gc-primary"><i class="bi bi-pencil"></i> Editar</a>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-light">Voltar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
