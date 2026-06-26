<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gestão de Compras e Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }
        .login-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border-radius: 12px 12px 0 0;
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .login-header .sistema-nome {
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: .5px;
            margin: 0;
        }
        .login-header .sistema-sub {
            font-size: .8rem;
            opacity: .8;
            margin: 0;
        }
        .card-body { padding: 2rem; }
        .form-label { font-weight: 500; font-size: .875rem; }
        .btn-entrar {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border: none;
            font-weight: 600;
            padding: .65rem;
        }
        .btn-entrar:hover { opacity: .9; }
        .forgot-link { font-size: .82rem; }
    </style>
</head>
<body>

<div class="login-card card">

    {{-- Cabeçalho --}}
    <div class="login-header">
        <i class="bi bi-box-seam fs-2 mb-2 d-block"></i>
        <p class="sistema-nome">Gestão de Compras e Estoque</p>
        <p class="sistema-sub">Faça login para continuar</p>
    </div>

    <div class="card-body">

        {{-- Mensagem de status (ex.: senha redefinida com sucesso) --}}
        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <small>{{ session('status') }}</small>
            </div>
        @endif

        {{-- Formulário de login --}}
        <form method="POST" action="{{ route('login.post') }}" novalidate>
            @csrf

            {{-- E-mail --}}
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="seu@email.com"
                        autofocus
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Senha --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="senha" class="form-label mb-0">Senha</label>
                    <a href="{{ route('password.request') }}" class="forgot-link text-decoration-none text-muted">
                        Esqueci minha senha
                    </a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control @error('senha') is-invalid @enderror"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        title="Mostrar/ocultar senha"
                        onclick="toggleSenha()"
                    >
                        <i class="bi bi-eye" id="iconeSenha"></i>
                    </button>
                    @error('senha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Lembrar-me --}}
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="lembrar" name="lembrar">
                <label class="form-check-label text-muted" for="lembrar" style="font-size:.875rem">
                    Manter conectado
                </label>
            </div>

            {{-- Botão --}}
            <button type="submit" class="btn btn-primary btn-entrar w-100 text-white">
                <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
            </button>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSenha() {
        const input = document.getElementById('senha');
        const icone = document.getElementById('iconeSenha');
        if (input.type === 'password') {
            input.type = 'text';
            icone.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icone.className = 'bi bi-eye';
        }
    }
</script>
</body>
</html>
