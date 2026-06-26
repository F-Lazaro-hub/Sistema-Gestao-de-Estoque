<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * A ordem importa: PerfilSeeder deve rodar antes de UsuarioSeeder
     * pois o usuário requer que o perfil já exista.
     */
    public function run(): void
    {
        $this->call([
            PerfilSeeder::class,
            UsuarioSeeder::class,
        ]);
    }
}
