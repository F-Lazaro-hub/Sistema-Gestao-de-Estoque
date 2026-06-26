<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Models\Perfil;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('perfil')
            ->orderBy('nome')
            ->paginate(15);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $perfis = Perfil::orderBy('nome')->get();

        return view('usuarios.create', compact('perfis'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        Usuario::create([
            'nome'      => $request->nome,
            'email'     => $request->email,
            'senha'     => Hash::make($request->senha),
            'perfil_id' => $request->perfil_id,
            'ativo'     => true,
        ]);

        return redirect()->route('usuarios.index')
            ->with('sucesso', 'Usuário criado com sucesso.');
    }

    public function show(int $id)
    {
        $usuario = Usuario::with('perfil')->findOrFail($id);

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(int $id)
    {
        $usuario = Usuario::findOrFail($id);
        $perfis  = Perfil::orderBy('nome')->get();

        return view('usuarios.edit', compact('usuario', 'perfis'));
    }

    public function update(UpdateUsuarioRequest $request, int $id)
    {
        $usuario = Usuario::findOrFail($id);

        $dados = [
            'nome'      => $request->nome,
            'email'     => $request->email,
            'perfil_id' => $request->perfil_id,
        ];

        if ($request->filled('senha')) {
            $dados['senha'] = Hash::make($request->senha);
        }

        $usuario->update($dados);

        return redirect()->route('usuarios.index')
            ->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return back()->with('erro', 'Você não pode excluir seu próprio usuário.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('sucesso', 'Usuário removido com sucesso.');
    }

    /** Ativa ou desativa o usuário sem excluí-lo. */
    public function toggleAtivo(int $id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return back()->with('erro', 'Você não pode desativar seu próprio usuário.');
        }

        $usuario->update(['ativo' => !$usuario->ativo]);

        $status = $usuario->ativo ? 'ativado' : 'desativado';

        return back()->with('sucesso', "Usuário {$status} com sucesso.");
    }
}
