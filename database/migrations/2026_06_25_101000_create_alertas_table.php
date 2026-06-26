<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                ->constrained('produtos')
                ->cascadeOnDelete();
            $table->text('mensagem');
            $table->decimal('quantidade_atual_registrada', 12, 2);
            $table->decimal('quantidade_minima_registrada', 12, 2);
            $table->enum('situacao', ['pendente', 'visualizado', 'resolvido'])
                ->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
