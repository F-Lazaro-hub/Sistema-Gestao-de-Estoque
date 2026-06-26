<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unifica Entradas, Saídas e Ajustes (descritos separadamente no
 * documento) em uma única tabela, usando a coluna `tipo`. Isso simplifica
 * o histórico completo e os relatórios de movimentação exigidos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimentacoes_estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->restrictOnDelete();
            $table->enum('tipo', ['entrada', 'saida', 'ajuste']);
            $table->decimal('quantidade', 12, 2);
            $table->decimal('valor_unitario', 12, 2)->nullable();  // usado em entradas
            $table->decimal('valor_total', 12, 2)->nullable();     // usado em entradas
            $table->string('motivo')->nullable();                  // usado em saídas/ajustes
            $table->foreignId('solicitacao_compra_id')
                ->nullable()
                ->constrained('solicitacoes_compra')
                ->nullOnDelete();
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();
            $table->date('data_movimentacao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_estoque');
    }
};
