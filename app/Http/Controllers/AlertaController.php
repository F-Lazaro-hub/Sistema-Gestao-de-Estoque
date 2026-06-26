<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Services\AlertaService;

class AlertaController extends Controller
{
    public function __construct(
        private readonly AlertaService $alertaService,
    ) {}

    public function index()
    {
        $alertas = Alerta::with('produto')
            ->orderByRaw("FIELD(situacao, 'pendente', 'em_andamento', 'resolvido')")
            ->latest()
            ->paginate(15);

        return view('alertas.index', compact('alertas'));
    }

    public function resolver(int $id)
    {
        try {
            $this->alertaService->resolverManualmente($id, auth()->id());

            return back()->with('sucesso', 'Alerta marcado como resolvido.');
        } catch (\DomainException $e) {
            return back()->with('erro', $e->getMessage());
        }
    }
}
