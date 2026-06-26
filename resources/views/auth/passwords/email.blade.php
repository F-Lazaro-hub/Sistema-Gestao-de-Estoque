<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha — Gestão de Compras</title>
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
            max-width: 420px;
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
        .btn-enviar {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border: none;
            font-weight: 600;
            padding: .65rem;
        }
        .btn-enviar:hover { opacity: .9; }
    </style>
</head>
<body>

<div class="card">

    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-key me-2"></i>Recuperar senha
        </h5>
        <small class="opacity-75">Enviaremos um link de redefinição para seu e-mail.</small>
    </div>

    <div class="card-body">

        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                <small>{{ session('status') }}</small>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">E-mail cadastrado</label>
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
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-enviar w-100 text-white mb-3">
                <i class="bi bi-send me-1"></i> Enviar link de redefinição
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
</body>
</html>
