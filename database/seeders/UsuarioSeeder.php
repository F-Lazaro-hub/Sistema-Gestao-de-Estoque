<?php

namespace Database\Seeders;

use App\Models\Perfil;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Cria um usuário inicial para cada um dos 5 perfis.
     *
     * ┌────────────────────────────┬────────────────────────────┬──────────────────┬───────────────┐
     * │ Nome                       │ E-mail                     │ Login            │ Senha inicial │
     * ├────────────────────────────┼────────────────────────────┼──────────────────┼───────────────┤
     * │ Administrador              │ admin@sistema.local        │ admin            │ Admin@1234    │
     * │ Gerente                    │ gerente@sistema.local      │ gerente          │ Gerente@1234  │
     * │ Comprador                  │ comprador@sistema.local    │ comprador        │ Compra@1234   │
     * │ Almoxarife                 │ almoxarife@sistema.local   │ almoxarife       │ Almoxa@1234   │
     * │ Financeiro                 │ financeiro@sistema.local   │ financeiro       │ Financ@1234   │
     * └────────────────────────────┴────────────────────────────┴──────────────────┴───────────────┘
     *
     * ⚠ ATENÇÃO: altere as senhas imediatamente após o primeiro acesso em produção.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'nome'      => 'Administrador',
                'login'     => 'admin',
                'email'     => 'admin@sistema.local',
                'senha'     => 'Admin@1234',
                'perfil'    => 'admin',
            ],
            [
                'nome'      => 'Gerente',
                'login'     => 'gerente',
                'email'     => 'gerente@sistema.local',
                'senha'     => 'Gerente@1234',
                'perfil'    => 'gerente',
            ],
            [
                'nome'      => 'Comprador',
                'login'     => 'comprador',
                'email'     => 'comprador@sistema.local',
                'senha'     => 'Compra@1234',
                'perfil'    => 'comprador',
            ],
            [
                'nome'      => 'Almoxarife',
                'login'     => 'almoxarife',
                'email'     => 'almoxarife@sistema.local',
                'senha'     => 'Almoxa@1234',
                'perfil'    => 'almoxarife',
            ],
            [
                'nome'      => 'Financeiro',
                'login'     => 'financeiro',
                'email'     => 'financeiro@sistema.local',
                'senha'     => 'Financ@1234',
                'perfil'    => 'financeiro',
            ],
        ];

        foreach ($usuarios as $dados) {
            $perfil = Perfil::where('codigo', $dados['perfil'])->firstOrFail();

            Usuario::firstOrCreate(
                ['email' => $dados['email']],
                [
                    'nome'      => $dados['nome'],
                    'login'     => $dados['login'],
                    'senha'     => $dados['senha'], // o cast 'hashed' hasheia automaticamente
                    'perfil_id' => $perfil->id,
                    'ativo'     => true,
                ],
            );

            $this->command->info("✔ Usuário criado: {$dados['nome']} ({$dados['email']})");
        }
    }
}
