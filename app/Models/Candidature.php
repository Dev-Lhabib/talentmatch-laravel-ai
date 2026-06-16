<?php

namespace App\Models;

use App\Enums\StatutCandidatureEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        'offre_id',
        'user_id',
        'nom_candidat',
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
        return $this->hasOne(Analyse::class);
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }
}
