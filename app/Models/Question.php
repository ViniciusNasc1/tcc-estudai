<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['enunciado', 'quiz_id', 'content_id'];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function alternatives(): HasMany
    {
        return $this->hasMany(Alternative::class, 'question_id');
    }

    /**
     * Conteúdo específico vinculado a esta questão.
     * Quando o aluno errar essa questão, esse conteúdo será recomendado.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
