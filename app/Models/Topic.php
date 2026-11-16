<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = ['nome', 'descricao'];

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(TopicPerformance::class);
    }
}
