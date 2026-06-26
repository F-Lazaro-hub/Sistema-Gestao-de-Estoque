<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ATENÇÃO: este arquivo SUBSTITUI o arquivo padrão gerado pelo Laravel
 * em database/migrations/0001_01_01_000000_create_users_table.php
 *
 * A tabela `usuarios` segue exatamente os campos pedidos no documento
 * (Nome, Email, Login, Senha, Perfil, Status, datas de criação/atualização),
 * mais soft delete (exigido pela arquitetura) e o relacionamento com perfis.
 *
 * As tabelas `password_reset_tokens` e `sessions` são tabelas INTERNAS do
 * framework Laravel (usadas pelo mecanismo de "recuperação de senha" e pelo
 * driver de sessão em banco de dados). Seus nomes e colunas foram mantidos
 * em inglês de propósito, pois o próprio código-fonte do Laravel referencia
 * esses nomes diretamente; renomeá-las exigiria sobrescrever várias classes
 * internas do framework sem nenhum ganho para o negócio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('login', 50)->unique();
            $table->string('email')->unique();
            $table->string('senha');
            $table->foreignId('perfil_id')
                ->constrained('perfis')
                ->restrictOnDelete(); // não permite excluir um perfil em uso
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela interna do Laravel (recuperação de senha) — nome mantido em inglês.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabela interna do Laravel (sessões em banco de dados) — nome mantido em inglês.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
