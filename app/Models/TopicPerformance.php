<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicPerformance extends Model
{
    protected $table = 'topic_performances';

    protected $fillable = ['user_id', 'topic_id', 'percentual_acertos', 'classificacao'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
