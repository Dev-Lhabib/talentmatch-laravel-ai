# Spec 04 — Analyse IA Structurée (Structured Output + Queue)

## User Stories

- US6 — Analyse IA structurée (structured output)
- US7 — Voir l'analyse d'un candidat
- US8 — Recommandation typée

## Objectif

Quand un CV est soumis contre une offre, un `Job` Laravel exécuté en arrière-plan
soumet le texte à `laravel/ai` avec un schéma de sortie contraint. Le résultat est
persisité en base de façon typée (casts Eloquent). Aucun score n'est calculé par le code
applicatif.

## Dans le périmètre

- `AnalyseCandidatJob` (dispatchable, serializable) exécuté sur la queue `default`.
- Appel `laravel/ai` avec structured output — schéma JSON strict.
- Validation de la conformité du JSON retourné avant toute écriture en base.
- Retry x3 en cas de réponse hors schéma ; `status = failed` + log si 3 échecs.
- Persistance de l'`Analyse` et mise à jour du `status` de la `Candidature`.
- Vue détail candidat : score, points forts, lacunes, compétences manquantes,
  recommandation, justification.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- **Jamais** calculer `matching_score`, `recommandation` ou toute autre sortie via
  `if/else`, regex ou règles codées en dur — ces valeurs viennent **exclusivement** de
  la réponse IA structurée.
- **Jamais** appeler l'IA de façon synchrone dans le contrôleur ou la requête HTTP.
- **Jamais** écrire en base des données ne respectant pas le schéma JSON défini
  ci-dessous (même partiellement).
- **Jamais** laisser une `Candidature` bloquée à `status = processing` sans mise à jour
  en cas d'erreur finale.

## Contrat JSON — Structured Output Schema

```json
{
  "competences_extraites":    ["string"],
  "annees_experience":        "integer",
  "niveau_etudes":            "string",
  "langues":                  ["string"],
  "matching_score":           "integer (0-100)",
  "points_forts":             ["string"],
  "lacunes":                  ["string"],
  "competences_manquantes":   ["string"],
  "recommandation":           "enum: convoquer | attente | rejeter",
  "justification":            "string"
}
```

Ce schéma est imposé au modèle IA via `laravel/ai` structured output — le SDK garantit
la conformité ou retourne une erreur.

## `AnalyseCandidatJob`

```php
class AnalyseCandidatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5; // secondes entre retries

    public function __construct(public Candidature $candidature) {}

    public function handle(AnalyseCandidatService $service): void
    {
        $this->candidature->update(['status' => StatutCandidatureEnum::Processing]);

        try {
            $result = $service->analyser($this->candidature);
            // $result est un objet/array conforme au schéma ci-dessus
            Analyse::create([
                'candidature_id'          => $this->candidature->id,
                'competences_extraites'   => $result->competences_extraites,
                'annees_experience'       => $result->annees_experience,
                'niveau_etudes'           => $result->niveau_etudes,
                'langues'                 => $result->langues,
                'matching_score'          => $result->matching_score,
                'points_forts'            => $result->points_forts,
                'lacunes'                 => $result->lacunes,
                'competences_manquantes'  => $result->competences_manquantes,
                'recommandation'          => $result->recommandation,
                'justification'           => $result->justification,
            ]);
            $this->candidature->update(['status' => StatutCandidatureEnum::Completed]);
        } catch (Throwable $e) {
            // Sur le dernier retry, passer à failed
            if ($this->attempts() >= $this->tries) {
                $this->candidature->update(['status' => StatutCandidatureEnum::Failed]);
                Log::error('AnalyseCandidatJob failed', [
                    'candidature_id' => $this->candidature->id,
                    'error'          => $e->getMessage(),
                ]);
            }
            throw $e; // laisser le système de retry de Laravel faire son travail
        }
    }
}
```

## `AnalyseCandidatService`

Responsable de la construction du prompt et de l'appel `laravel/ai` :

```php
class AnalyseCandidatService
{
    public function analyser(Candidature $candidature): AnalyseResultDTO
    {
        $offre = $candidature->offre;

        $prompt = <<<EOT
        Tu es un expert RH. Analyse le CV suivant par rapport à l'offre d'emploi.

        ## Offre d'emploi
        Titre : {$offre->titre}
        Description : {$offre->description}
        Compétences requises : {$this->formatCompetences($offre->competences_requises)}
        Expérience minimum : {$offre->niveau_experience_min} an(s)

        ## CV du candidat
        Nom : {$candidature->nom_candidat}
        {$candidature->cv_text}

        Retourne une analyse structurée conforme au schéma fourni.
        EOT;

        // Appel laravel/ai avec structured output (AnalyseResultDTO ou équivalent SDK)
        return AI::structured($prompt, AnalyseResultDTO::class);
    }

    private function formatCompetences(?array $competences): string
    {
        if (empty($competences)) {
            return 'Non spécifiées — base ton évaluation sur la description du poste et '
                 . "l'expérience générale du candidat.";
        }
        return implode(', ', $competences);
    }
}
```

## `RecommandationEnum`

```php
enum RecommandationEnum: string
{
    case Convoquer = 'convoquer';
    case Attente   = 'attente';
    case Rejeter   = 'rejeter';
}
```

## Modèle Eloquent `Analyse`

```php
protected $fillable = [
    'candidature_id', 'competences_extraites', 'annees_experience',
    'niveau_etudes', 'langues', 'matching_score', 'points_forts',
    'lacunes', 'competences_manquantes', 'recommandation', 'justification',
];

protected $casts = [
    'competences_extraites'  => 'array',
    'langues'                => 'array',
    'points_forts'           => 'array',
    'lacunes'                => 'array',
    'competences_manquantes' => 'array',
    'recommandation'         => RecommandationEnum::class,
];

public function candidature(): BelongsTo { ... }
```

## Vue détail candidat

```
GET /offres/{offre}/candidatures/{candidature}
```

Sections :
1. **Infos candidat** : nom, score (badge coloré 0-100), badge recommandation.
2. **Points forts** : liste à puces.
3. **Lacunes** : liste à puces.
4. **Compétences manquantes** : tags.
5. **Profil extrait** : années d'expérience, niveau d'études, langues, compétences extraites.
6. **Justification IA** : paragraphe.
7. **Lien vers le chat** (spec 07).

## Commandes Docker utiles

```bash
# Lancer le worker de queue
docker compose exec app php artisan queue:work --queue=default --tries=3

# Rejouer les jobs échoués
docker compose exec app php artisan queue:retry all

# Vider les jobs en attente (dev)
docker compose exec app php artisan queue:clear
```

## Pourquoi Queue et non synchrone ?

> La réponse IA peut prendre entre 3 et 15 secondes. Une exécution synchrone bloquerait
> le thread PHP, dégraderait l'expérience utilisateur et risquerait un timeout HTTP.
> La queue découple la soumission (instantanée) de l'analyse (asynchrone), permet le
> retry automatique et offre une observabilité complète via `failed_jobs`.

## Pourquoi Structured Output et non regex/if-else ?

> Le scoring est subjectif, multi-dimensionnel et contextuel — il dépend de la
> combinaison des compétences, de l'expérience, de la description du poste et même du
> ton du CV. Un `if/else` ne peut pas capter cette nuance. Le structured output contraint
> le modèle IA à retourner un JSON conforme à un schéma strict, éliminant le parsing
> fragile tout en préservant la qualité de l'évaluation.

## Critères d'acceptation

- [ ] Soumettre un CV déclenche le job et retourne immédiatement (sans attendre l'IA).
- [ ] Après exécution du job, `analyses` contient une ligne conforme au contrat JSON.
- [ ] Tous les champs array/enum sont castés — aucun `json_decode` manuel dans les vues.
- [ ] Une réponse IA simulée hors schéma déclenche le retry puis `status = failed` — pas
  de donnée invalide en base.
- [ ] La vue détail affiche score, recommandation, points forts, lacunes, manquantes et
  justification de façon lisible.
- [ ] Offre sans `competences_requises` → analyse cohérente produite.

## Dépendances

- Requiert : spec 03 (candidatures — le job est dispatché par le contrôleur de
  soumission).
- Requis par : spec 05 (tools lisent `analyses`), spec 07 (agent répond sur l'analyse).

## Branche Git

`feature/analyse-ia` → `develop`

## Workflow OpenSpec

```bash
opsx propose "Analyse IA structurée : job queue, structured output laravel/ai, casts Eloquent, retry/fail"
opsx plan analyse-ia
opsx tasks analyse-ia
opsx implement analyse-ia
```
