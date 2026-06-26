@extends('layouts.app')

@section('title', 'Notificações')

@section('content')

    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="{{ route('notificacoes.marcarTodasLidas') }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-check2-all"></i> Marcar todas como lidas</button>
        </form>
    </div>

    <div class="card">
        <div class="list-group list-group-flush">
            @forelse($notificacoes as $notificacao)
                <div class="list-group-item d-flex justify-content-between align-items-start gap-3 {{ $notificacao->lida_em ? '' : 'bg-soft-teal' }}">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-bell{{ $notificacao->lida_em ? '' : '-fill' }}"></i>
                            <strong>{{ $notificacao->titulo }}</strong>
                            @if(!$notificacao->lida_em)
                                <span class="badge-situacao badge-aberto">Não lida</span>
                            @endif
                        </div>
                        <p class="mb-1 text-muted small">{{ $notificacao->mensagem }}</p>
                        <span class="text-muted small">{{ optional($notificacao->created_at)->diffForHumans() }}</span>
                    </div>

                    @if(!$notificacao->lida_em)
                        <form method="POST" action="{{ route('notificacoes.marcarLida', $notificacao) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-secondary text-nowrap">
                                <i class="bi bi-check2"></i> Marcar como lida
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <x-empty-state icon="bi-bell" title="Você não tem notificações" />
            @endforelse
        </div>
        @if(method_exists($notificacoes, 'links'))
            <div class="card-footer bg-white">{{ $notificacoes->links() }}</div>
        @endif
    </div>

@endsection
