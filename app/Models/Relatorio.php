<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relatorio extends Model
{
    public $timestamps = false; // só tem created_at

    protected $table = 'relatorios';

    protected $fillable = ['titulo', 'tipo', 'dados', 'gerado_por'];

    protected $casts = [
        'dados'      => 'array',      // serializa/deserializa JSON automaticamente
        'created_at' => 'datetime',
    ];

    /**
     * Administrador que gerou o relatório.
     */
    public function gerador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerado_por');
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
}
