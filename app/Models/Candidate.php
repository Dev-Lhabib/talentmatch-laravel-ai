<?php

namespace App\Models;

use App\Enums\StatutCandidatureEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'offre_id',
        'user_id',
        'name',
        'email',
        'phone',
        'cv_text',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatutCandidatureEnum::class,
        ];
    }

    public function offre(): BelongsTo
    {
        return $this->belongsTo(Offre::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analyse(): HasOne
    {
        return $this->hasOne(Analyse::class, 'candidate_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'candidate_id');
    }

    public function scopeOrderedByScore(Builder $query): Builder
    {
        return $query
            ->leftJoin('analyses', 'analyses.candidate_id', '=', 'candidates.id')
            ->orderByDesc('analyses.matching_score')
            ->orderBy('candidates.created_at')
            ->select('candidates.*');
    }
}
