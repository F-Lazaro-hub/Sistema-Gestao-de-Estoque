<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quantidade atual e mínima de cada produto. Atualizada exclusivamente
 * pelo StockService (Fase 3) a partir dos registros de movimentacoes_estoque
 * — nunca diretamente pelo controller de produtos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                ->unique()
                ->constrained('produtos')
                ->cascadeOnDelete();
            $table->decimal('quantidade_atual', 12, 2)->default(0);
            $table->decimal('quantidade_minima', 12, 2)->default(0);
            $table->timestamp('ultima_movimentacao_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};
