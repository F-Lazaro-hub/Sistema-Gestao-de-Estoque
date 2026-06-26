<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Estoque;
use App\Models\Notificacao;
use App\Models\Produto;
use App\Models\Usuario;

class AlertaService
{
    // -------------------------------------------------------------------------
    // Verificação e geração
    // -------------------------------------------------------------------------

    /**
     * Verifica se o produto está abaixo do mínimo e, se sim, gera um alerta
     * (caso ainda não exista um alerta "aberto" para ele).
     *
     * Chamado automaticamente pelo EstoqueService após saídas e ajustes.
     */
    public function verificarEGerarAlerta(Produto $produto, Estoque $estoque): ?Alerta
    {
        if (! $estoque->estaBaixoDoMinimo()) {
            return null;
        }

        // Evita duplicata: se já existe um alerta "aberto" para este produto, não cria outro.
        $alertaExistente = Alerta::where('produto_id', $produto->id)
            ->where('situacao', 'pendente')
            ->first();

        if ($alertaExistente) {
            // Atualiza os valores registrados caso o estoque tenha caído ainda mais.
            $alertaExistente->quantidade_atual_registrada   = $estoque->quantidade_atual;
            $alertaExistente->quantidade_minima_registrada  = $estoque->quantidade_minima;
            $alertaExistente->save();

            return $alertaExistente;
        }

        return $this->criarAlerta($produto, $estoque);
    }

    /**
     * Verifica se o produto retornou ao nível mínimo e resolve alertas abertos.
     *
     * Chamado pelo EstoqueService após entradas de estoque.
     */
    public function verificarEResolverAlertas(Produto $produto, Estoque $estoque): void
    {
        if ($estoque->estaBaixoDoMinimo()) {
            return; // Ainda abaixo do mínimo — mantém alertas abertos.
        }

        Alerta::where('produto_id', $produto->id)
            ->where('situacao', 'pendente')
            ->update(['situacao' => 'resolvido']);
    }

    /**
     * Varre todos os produtos ativos e gera alertas para os que estiverem
     * abaixo do mínimo (job agendado ou chamada manual).
     *
     * @return int  Quantidade de alertas gerados.
     */
    public function verificarTodosProdutos(): int
    {
        $gerados = 0;

        $produtos = Produto::with('estoque')
            ->where('ativo', true)
            ->whereHas('estoque', fn($q) => $q->whereColumn('quantidade_atual', '<=', 'quantidade_minima'))
            ->get();

        foreach ($produtos as $produto) {
            if ($this->verificarEGerarAlerta($produto, $produto->estoque)) {
                $gerados++;
            }
        }

        return $gerados;
    }

    // -------------------------------------------------------------------------
    // Notificações
    // -------------------------------------------------------------------------

    /**
     * Cria notificações para um alerta, enviando para todos os usuários
     * dos perfis especificados.
     *
     * @param  Alerta        $alerta
     * @param  array<string> $perfis  Ex.: ['admin', 'gerente', 'comprador']
     */
    public function notificarPerfis(Alerta $alerta, array $perfis = ['admin', 'gerente', 'comprador']): void
    {
        $usuarios = Usuario::where('ativo', true)
            ->whereHas('perfil', fn($q) => $q->whereIn('codigo', $perfis))
            ->get();

        foreach ($usuarios as $usuario) {
            $this->criarNotificacao($alerta, $usuario);
        }
    }

    /**
     * Marca uma notificação como lida.
     */
    public function marcarComoLida(Notificacao $notificacao): void
    {
        $notificacao->lida_em = now();
        $notificacao->save();
    }

    /**
     * Marca todas as notificações não lidas de um usuário como lidas.
     */
    public function marcarTodasComoLidas(int $usuarioId): int
    {
        return Notificacao::where('usuario_id', $usuarioId)
            ->whereNull('lida_em')
            ->update(['lida_em' => now()]);
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    private function criarAlerta(Produto $produto, Estoque $estoque): Alerta
    {
        $alerta = new Alerta();
        $alerta->produto_id                   = $produto->id;
        $alerta->mensagem                     = "Estoque do produto \"{$produto->nome}\" está abaixo do mínimo. "
            . "Atual: {$estoque->quantidade_atual} {$produto->unidade} — "
            . "Mínimo: {$estoque->quantidade_minima} {$produto->unidade}.";
        $alerta->quantidade_atual_registrada  = $estoque->quantidade_atual;
        $alerta->quantidade_minima_registrada = $estoque->quantidade_minima;
        $alerta->situacao                     = 'pendente';
        $alerta->save();

        // Notifica os perfis relevantes imediatamente.
        $this->notificarPerfis($alerta);

        return $alerta;
    }

    private function criarNotificacao(Alerta $alerta, Usuario $usuario): Notificacao
    {
        $notificacao = new Notificacao();
        $notificacao->usuario_id = $usuario->id;
        $notificacao->alerta_id  = $alerta->id;
        $notificacao->titulo     = "⚠ Estoque baixo: {$alerta->produto->nome}";
        $notificacao->mensagem   = $alerta->mensagem;
        $notificacao->tipo       = 'estoque_baixo';
        $notificacao->lida_em    = null;
        $notificacao->save();

        return $notificacao;
    }
}
