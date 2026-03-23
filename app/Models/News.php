<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'content',
        'source_name',
        'source_url',
        'published_date',
        'category',
        'keywords',
        'summary',
        'relevance_score',
        'metadata',
    ];

    protected $casts = [
        'published_date' => 'date',
        'keywords' => 'array',
        'metadata' => 'array',
    ];

    public function correlationAnalyses()
    {
        return $this->hasMany(CorrelationAnalysis::class);
    }
}
