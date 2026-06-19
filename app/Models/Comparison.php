<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comparison extends Model
{
    protected $fillable = [
        'offre_id',
        'application1_id',
        'application2_id',
        'candidate1_verdict',
        'candidate2_verdict',
        'winner_id',
        'winner_reason',
    ];

    protected function casts(): array
    {
        return [
            'winner_id' => 'integer',
        ];
    }

    public function offre(): BelongsTo
    {
        return $this->belongsTo(Offre::class);
    }

    public function application1(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application1_id');
    }

    public function application2(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'winner_id');
    }
}
