<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha — Gestão de Compras</title>
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
        .card {
            width: 100%;
            max-width: 440px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }
        .card-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border-radius: 12px 12px 0 0;
            padding: 1.75rem 2rem;
            color: #fff;
        }
        .card-body { padding: 2rem; }
        .form-label { font-weight: 500; font-size: .875rem; }
        .btn-redefinir {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border: none;
            font-weight: 600;
            padding: .65rem;
        }
        .btn-redefinir:hover { opacity: .9; }
        .requisitos { font-size: .78rem; }
    </style>
</head>
<body>

<div class="card">

    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-shield-lock me-2"></i>Redefinir senha
        </h5>
        <small class="opacity-75">Escolha uma senha forte para sua conta.</small>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            {{-- Token (oculto) --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- E-mail (preenchido via URL) --}}
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $email ?? '') }}"
                        required
                        readonly
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Nova senha --}}
            <div class="mb-3">
                <label for="senha" class="form-label">Nova senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control @error('senha') is-invalid @enderror"
                        placeholder="Mínimo 8 caracteres"
                        autocomplete="new-password"
                        required
                    >
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleCampo('senha','icone-senha')">
                        <i class="bi bi-eye" id="icone-senha"></i>
                    </button>
                    @error('senha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <p class="requisitos text-muted mt-1 mb-0">
                    <i class="bi bi-info-circle me-1"></i>Mínimo 8 caracteres.
                </p>
            </div>

            {{-- Confirmar nova senha --}}
            <div class="mb-4">
                <label for="senha_confirmation" class="form-label">Confirmar nova senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input
                        type="password"
                        id="senha_confirmation"
                        name="senha_confirmation"
                        class="form-control @error('senha_confirmation') is-invalid @enderror"
                        placeholder="Repita a senha"
                        autocomplete="new-password"
                        required
                    >
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleCampo('senha_confirmation','icone-conf')">
                        <i class="bi bi-eye" id="icone-conf"></i>
                    </button>
                    @error('senha_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-redefinir w-100 text-white mb-3">
                <i class="bi bi-check-circle me-1"></i> Redefinir senha
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none text-muted" style="font-size:.875rem">
                    <i class="bi bi-arrow-left me-1"></i>Voltar ao login
                </a>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleCampo(inputId, iconeId) {
        const input = document.getElementById(inputId);
        const icone = document.getElementById(iconeId);
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
