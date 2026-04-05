<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AttackImportBatch extends Model
{
    protected $fillable = [
        'filename',
        'file_size',
        'total_records',
        'imported_count',
        'failed_count',
        'status',
        'reverted_at',
    ];

    protected $casts = [
        'reverted_at' => 'datetime',
    ];

    public function attacks(): HasMany
    {
        return $this->hasMany(HackerAttack::class, 'import_batch_id');
    }

    public function isReverted(): bool
    {
        return $this->status === 'reverted';
    }

    public function revert(): void
    {
        $this->attacks()->delete();

        $this->update([
            'status'      => 'reverted',
            'reverted_at' => now(),
        ]);
    }
}
