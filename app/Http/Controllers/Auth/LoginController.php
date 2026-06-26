<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    // ─── Exibe o formulário de login ──────────────────────────────────────────

    public function create(): View
    {
        return view('auth.login');
    }

    // ─── Processa as credenciais e autentica o usuário ────────────────────────

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // ─── Encerra a sessão do usuário ──────────────────────────────────────────

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
