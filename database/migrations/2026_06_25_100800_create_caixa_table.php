<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro único (singleton) com o saldo atual do caixa. O histórico de
 * entradas/saídas que compõem esse saldo fica em movimentacoes_financeiras.
 * O Seeder da Fase 2 cria a única linha desta tabela.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caixa', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo_atual', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caixa');
    }
};
