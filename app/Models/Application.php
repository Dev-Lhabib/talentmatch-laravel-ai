<?php

namespace App\Models;

use App\Enums\StatutCandidatureEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'offre_id',
        'status',
        'cv_text',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatutCandidatureEnum::class,
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function offre(): BelongsTo
    {
        return $this->belongsTo(Offre::class);
    }

    public function analyse(): HasOne
    {
        return $this->hasOne(Analyse::class, 'application_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'application_id');
    }
}
