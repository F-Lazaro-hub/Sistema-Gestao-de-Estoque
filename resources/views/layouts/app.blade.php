<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') · Gestão de Compras</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>
<div class="gc-wrapper">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="gc-sidebar">
        <div class="gc-brand">
            <span class="gc-logo-dot"></span>
            <div>
                <strong>Gestão de Compras</strong><br>
                <small>Compras &amp; Estoque</small>
            </div>
        </div>

        <nav class="gc-nav">
            <a href="{{ route('dashboard') }}" class="gc-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            @auth
            @if(auth()->user()->temPerfil('admin'))
                <div class="gc-nav-section">Administração</div>
                <a href="{{ route('usuarios.index') }}" class="gc-nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Usuários
                </a>
                <a href="{{ route('categorias.index') }}" class="gc-nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Categorias
                </a>
            @endif

            @if(auth()->user()->temAlgumPerfil('admin', 'gerente'))
                <a href="{{ route('produtos.index') }}" class="gc-nav-link {{ request()->routeIs('produtos.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Produtos
                </a>
            @endif

            @if(auth()->user()->temAlgumPerfil('admin', 'almoxarife') || auth()->user()->temAlgumPerfil('admin', 'gerente'))
                <div class="gc-nav-section">Operação</div>
            @endif

            @if(auth()->user()->temAlgumPerfil('admin', 'almoxarife'))
                <a href="{{ route('estoque.index') }}" class="gc-nav-link {{ request()->routeIs('estoque.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i> Estoque
                </a>
            @endif

            @if(auth()->user()->temAlgumPerfil('admin', 'gerente', 'comprador'))
                <a href="{{ route('compras.index') }}" class="gc-nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i> Solicitações de compra
                </a>
            @endif

            @if(auth()->user()->temAlgumPerfil('admin', 'financeiro'))
                <div class="gc-nav-section">Financeiro</div>
                <a href="{{ route('financeiro.index') }}" class="gc-nav-link {{ request()->routeIs('financeiro.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i> Financeiro
                </a>
            @endif

            <div class="gc-nav-section">Geral</div>
            <a href="{{ route('alertas.index') }}" class="gc-nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Alertas
            </a>
            <a href="{{ route('notificacoes.index') }}" class="gc-nav-link {{ request()->routeIs('notificacoes.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notificações
            </a>
            @endauth
        </nav>

        @auth
        <div class="gc-sidebar-footer">
            Perfil: <strong class="text-white-50">{{ auth()->user()->perfil->nome ?? '—' }}</strong>
        </div>
        @endauth
    </aside>

    {{-- ===================== CONTEÚDO ===================== --}}
    <div class="gc-main">
        <header class="gc-topbar">
            <div class="d-flex align-items-center gap-3">
                <button id="gc-sidebar-toggle" class="btn btn-sm btn-outline-secondary d-lg-none">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <p class="gc-page-title">@yield('title', 'Dashboard')</p>
                    @hasSection('breadcrumbs')
                        <div class="gc-breadcrumb">@yield('breadcrumbs')</div>
                    @endif
                </div>
            </div>

            @auth
            <div class="d-flex align-items-center gap-2">
                @php $naoLidas = auth()->user()->notificacoes()->whereNull('lida_em')->count(); @endphp
                <a href="{{ route('notificacoes.index') }}" class="btn btn-light btn-sm gc-bell-btn" title="Notificações">
                    <i class="bi bi-bell"></i>
                    @if($naoLidas > 0)
                        <span class="gc-bell-dot"></span>
                    @endif
                </a>

                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline">{{ auth()->user()->nome }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </header>

        <main class="gc-content">
            @yield('content')
        </main>
    </div>
</div>

{{-- ===================== TOASTS (flash messages) ===================== --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
    @if(session('sucesso'))
        <div class="toast text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-check-circle me-1"></i> {{ session('sucesso') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(session('erro'))
        <div class="toast text-bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-x-circle me-1"></i> {{ session('erro') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    {{-- Erros vindos do handler global de DomainException (->withErrors(['erro' => ...])) --}}
    @if($errors->has('erro'))
        <div class="toast text-bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-x-circle me-1"></i> {{ $errors->first('erro') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>

{{-- ===================== MODAL DE CONFIRMAÇÃO GENÉRICO ===================== --}}
<div class="modal fade" id="gc-confirm-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="#">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-header">
                    <h5 class="modal-title" data-confirm-title>Confirmar ação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" data-confirm-message>Tem certeza que deseja confirmar esta ação?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
