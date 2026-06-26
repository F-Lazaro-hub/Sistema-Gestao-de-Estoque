<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique();
            $table->date('data');
            $table->foreignId('responsavel_id')
                ->constrained('usuarios')
                ->restrictOnDelete();
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
