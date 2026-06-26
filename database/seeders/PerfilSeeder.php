<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    /**
     * Os 5 perfis de acesso do sistema.
     *
     * Código  | Perfil        | Responsabilidades principais
     * ---------|---------------|---------------------------------------------
     * admin    | Administrador | Acesso total: usuários, configurações, relatórios
     * gerente  | Gerente       | Aprovação de compras, relatórios gerenciais
     * comprador| Comprador     | Abertura e acompanhamento de solicitações
     * almoxarife| Almoxarife   | Gestão de estoque e movimentações
     * financeiro| Financeiro   | Gestão do caixa e movimentações financeiras
     */
    public function run(): void
    {
        $perfis = [
            [
                'nome'     => 'Administrador',
                'codigo'   => 'admin',
                'descricao' => 'Acesso total ao sistema, incluindo gestão de usuários e configurações.',
            ],
            [
                'nome'     => 'Gerente',
                'codigo'   => 'gerente',
                'descricao' => 'Aprovação de solicitações de compra e acesso a relatórios gerenciais.',
            ],
            [
                'nome'     => 'Comprador',
                'codigo'   => 'comprador',
                'descricao' => 'Abertura, edição e acompanhamento de solicitações de compra.',
            ],
            [
                'nome'     => 'Almoxarife',
                'codigo'   => 'almoxarife',
                'descricao' => 'Gestão do estoque: entradas, saídas, ajustes e emissão de notas internas.',
            ],
            [
                'nome'     => 'Financeiro',
                'codigo'   => 'financeiro',
                'descricao' => 'Gestão do caixa e visualização de movimentações financeiras.',
            ],
        ];

        foreach ($perfis as $perfil) {
            Perfil::firstOrCreate(
                ['codigo' => $perfil['codigo']],
                $perfil,
            );
        }

        $this->command->info('✔ Perfis criados: ' . implode(', ', array_column($perfis, 'codigo')));
    }
}
