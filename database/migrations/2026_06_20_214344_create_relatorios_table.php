<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['desempenho', 'progresso', 'geral'])->default('geral');
            $table->json('dados');                             // snapshot dos dados no momento da geração
            $table->foreignId('gerado_por')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();    // só created_at, sem updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
};
