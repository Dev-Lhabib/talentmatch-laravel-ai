<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'cv_text',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeOrderedByScore(Builder $query): Builder
    {
        return $query
            ->leftJoin('applications', 'applications.candidate_id', '=', 'candidates.id')
            ->leftJoin('analyses', 'analyses.application_id', '=', 'applications.id')
            ->orderByDesc('analyses.matching_score')
            ->orderBy('candidates.created_at')
            ->select('candidates.*');
    }
}
