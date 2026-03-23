<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HackerAttack extends Model
{
    protected $fillable = [
        'title',
        'description',
        'attack_type',
        'severity',
        'affected_entity',
        'attack_date',
        'source_url',
        'source_name',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'attack_date' => 'date',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    public function correlationAnalyses()
    {
        return $this->hasMany(CorrelationAnalysis::class);
    }
}
