<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;

class NotificacaoController extends Controller
{
    public function index()
    {
        $notificacoes = auth()->user()
            ->notificacoes()
            ->with('alerta.produto')
            ->latest()
            ->paginate(20);

        return view('notificacoes.index', compact('notificacoes'));
    }

    public function marcarComoLida(int $id)
    {
        Notificacao::where('usuario_id', auth()->id())
            ->findOrFail($id)
            ->update(['lida_em' => now()]);

        return back()->with('sucesso', 'Notificação marcada como lida.');
    }

    public function marcarTodasComoLidas()
    {
        auth()->user()
            ->notificacoes()
            ->whereNull('lida_em')
            ->update(['lida_em' => now()]);

        return back()->with('sucesso', 'Todas as notificações foram marcadas como lidas.');
    }
}
