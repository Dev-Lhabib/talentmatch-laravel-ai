<?php

namespace App\Models;

use App\Enums\RecommandationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Analyse extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'competences_extraites',
        'annees_experience',
        'niveau_etudes',
        'langues',
        'matching_score',
        'points_forts',
        'lacunes',
        'competences_manquantes',
        'recommandation',
        'justification',
        'analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'competences_extraites' => 'array',
            'langues' => 'array',
            'points_forts' => 'array',
            'lacunes' => 'array',
            'competences_manquantes' => 'array',
            'recommandation' => RecommandationEnum::class,
            'analyzed_at' => 'datetime',
            'annees_experience' => 'integer',
            'matching_score' => 'integer',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function competences(): BelongsToMany
    {
        return $this->belongsToMany(Competence::class, 'analyse_competence');
    }
}
