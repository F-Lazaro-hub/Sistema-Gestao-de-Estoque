<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens_nota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')
                ->constrained('notas')
                ->cascadeOnDelete();
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->restrictOnDelete();
            $table->decimal('quantidade', 12, 2);
            $table->decimal('valor', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_nota');
    }
};
