<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('tipo_acao', [
                'login',
                'logout',
                'quiz_realizado',
                'recomendacao_gerada',
                'conteudo_acessado',
                'relatorio_gerado',
            ]);
            $table->unsignedBigInteger('referencia_id')->nullable(); // ID do quiz, conteúdo, etc.
            $table->timestamp('created_at')->useCurrent();           // só created_at, sem updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_atividades');
    }
};
