<?php

namespace App\Models;

use App\Enums\PrioriteFeedbackEnum;
use App\Enums\StatutFeedbackEnum;
use App\Enums\TypeFeedbackEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'offre_id',
        'candidate_id',
        'sujet',
        'message',
        'priorite',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeFeedbackEnum::class,
            'priorite' => PrioriteFeedbackEnum::class,
            'status' => StatutFeedbackEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offre(): BelongsTo
    {
        return $this->belongsTo(Offre::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
