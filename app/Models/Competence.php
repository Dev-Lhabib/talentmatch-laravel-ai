<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competence extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];

    public function offres(): BelongsToMany
    {
        return $this->belongsToMany(Offre::class, 'offre_competence');
    }

    public function analyses(): BelongsToMany
    {
        return $this->belongsToMany(Analyse::class, 'analyse_competence');
    }
}
