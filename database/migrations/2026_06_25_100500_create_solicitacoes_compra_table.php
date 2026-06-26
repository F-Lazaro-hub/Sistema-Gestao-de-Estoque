<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')
                ->constrained('usuarios')
                ->restrictOnDelete();
            $table->foreignId('aprovador_id')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();
            $table->enum('situacao', ['pendente', 'aprovada', 'reprovada', 'cancelada'])
                ->default('pendente');
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_compra');
    }
};
