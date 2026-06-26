<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de perfis de usuário (Administrador, Financeiro, Vendas, Estoque, Padrão).
 * Precisa ser criada antes da tabela usuarios, pois usuarios.perfil_id referencia esta tabela.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);            // Ex: "Administrador"
            $table->string('codigo', 30)->unique(); // Ex: "admin", "financeiro", "vendas", "estoque", "usuario"
            $table->string('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfis');
    }
};
