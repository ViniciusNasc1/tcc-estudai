<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoAtividade extends Model
{
    public $timestamps = false; // só tem created_at

    protected $table = 'historico_atividades';

    protected $fillable = ['usuario_id', 'tipo_acao', 'referencia_id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Usuário dono da atividade.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Cria automaticamente o created_at na inserção.
     */
    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    /**
     * Helper estático para registrar uma ação com uma linha.
     */
    public static function registrar(int $usuarioId, string $tipoAcao, ?int $referenciaId = null): void
    {
        static::create([
            'usuario_id'   => $usuarioId,
            'tipo_acao'    => $tipoAcao,
            'referencia_id'=> $referenciaId,
        ]);
    }
}
