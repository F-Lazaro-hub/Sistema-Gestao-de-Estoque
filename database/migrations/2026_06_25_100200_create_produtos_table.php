<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dados de CATÁLOGO do produto. As quantidades (atual/mínima) ficam
 * isoladas na tabela `estoques` para manter o banco normalizado e separar
 * dados de cadastro (que mudam pouco) de dados de inventário (que mudam
 * a cada movimentação).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->restrictOnDelete();
            $table->string('marca', 100)->nullable();
            $table->string('unidade', 10); // Ex: UN, KG, CX, L
            $table->decimal('ultimo_valor_pago', 12, 2)->default(0);
            $table->decimal('valor_medio', 12, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
