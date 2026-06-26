<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela ADICIONAL (não estava na lista original de migrations do
 * documento, mas é necessária para atender ao requisito "Histórico de
 * preços" do cadastro de produtos). Cada compra aprovada gera um
 * registro aqui, permitindo consultar a evolução do valor pago por produto.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_precos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->cascadeOnDelete();
            $table->decimal('valor', 12, 2);
            $table->date('data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_precos');
    }
};
