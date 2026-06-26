@extends('layouts.app')

@section('title', 'Usuários')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="busca" value="{{ request('busca') }}" class="form-control form-control-sm" style="width: 240px" placeholder="Buscar por nome, login ou e-mail">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('usuarios.create') }}" class="btn btn-gc-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Novo usuário
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-gc mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Login</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td class="fw-semibold">{{ $usuario->nome }}</td>
                            <td>{{ $usuario->login }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->perfil->nome ?? '—' }}</td>
                            <td><x-ativo :value="$usuario->ativo" /></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-secondary" title="Editar"><i class="bi bi-pencil"></i></a>

                                    <form method="POST" action="{{ route('usuarios.ativar', $usuario) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-secondary" title="{{ $usuario->ativo ? 'Desativar' : 'Ativar' }}">
                                            <i class="bi {{ $usuario->ativo ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                        </button>
                                    </form>

                                    @if($usuario->id !== auth()->id())
                                        <button type="button" class="btn btn-outline-danger" title="Excluir"
                                            data-confirm-delete
                                            data-url="{{ route('usuarios.destroy', $usuario) }}"
                                            data-title="Excluir usuário"
                                            data-message="Excluir o usuário &quot;{{ $usuario->nome }}&quot;? Essa ação não pode ser desfeita.">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="bi-people" title="Nenhum usuário cadastrado" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($usuarios, 'links'))
            <div class="card-footer bg-white">{{ $usuarios->links() }}</div>
        @endif
    </div>

@endsection
