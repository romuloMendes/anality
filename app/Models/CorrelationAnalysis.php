<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrelationAnalysis extends Model
{
    protected $fillable = [
        'hacker_attack_id',
        'news_id',
        'correlation_score',
        'analysis_reason',
        'correlation_type',
        'analysis_date',
        'is_validated',
        'pattern_data',
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'pattern_data' => 'array',
        'is_validated' => 'boolean',
    ];

    public function hackerAttack()
    {
        return $this->belongsTo(HackerAttack::class);
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
