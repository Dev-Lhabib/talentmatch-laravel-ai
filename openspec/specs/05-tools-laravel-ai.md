# Spec 05 — Tools Laravel AI

## User Story

US11 — L'assistant appelle des tools.

## Objectif

Définir les trois tools Laravel que l'agent conversationnel (spec 07) peut appeler pour
récupérer des données réelles depuis la base de données — plutôt que d'inventer des
réponses (hallucination).

## Dans le périmètre

- `getCandidateAnalysis(int $candidatureId)` — retourne l'analyse complète d'une candidature.
- `getJobRequirements(int $offreId)` — retourne les critères d'une offre.
- `compareCandidates(int $id1, int $id2)` — retourne les deux analyses pour comparaison.
- Chaque tool vérifie que la ressource demandée appartient à `auth()->id()`.
- Les tools sont enregistrés auprès de l'agent `laravel/ai` (voir spec 07).

## Hors périmètre / Ce que les tools ne doivent PAS faire

- **Jamais** retourner des données appartenant à un autre utilisateur que celui
  authentifié — le scoping `user_id` est obligatoire dans chaque tool.
- **Jamais** effectuer d'écriture en base (lecture seule).
- **Jamais** appeler l'IA directement — les tools sont des wrappers Eloquent, pas des
  sous-agents.
- L'agent ne doit **jamais** répondre à une question factuelle (score, lacunes, critères)
  sans avoir préalablement appelé le tool correspondant — une réponse sans appel de tool
  = hallucination = comportement interdit (voir `AGENTS.md`).
- `compareCandidates` ne doit **pas** être appelé si l'une des deux candidatures
  appartient à une offre différente (ou à un autre utilisateur) — retourner une erreur
  contrôlée.

## Définition des Tools

### Tool 1 — `getCandidateAnalysis`

**Signature**

```php
getCandidateAnalysis(int $candidatId): array|string
```

**Description fournie à l'agent**

> Récupère l'analyse complète d'une candidature depuis la base de données.
> Retourne le score, les points forts, les lacunes, les compétences manquantes, la
> recommandation et la justification. Appelle ce tool avant toute réponse sur le profil
> d'un candidat spécifique.

**Implémentation**

```php
public function getCandidateAnalysis(int $candidatureId): array|string
{
    $application = Application::with('candidate', 'analyse', 'offre')
        ->where('id', $candidatureId)
        ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
        ->whereHas('analyse')
        ->latest()
        ->first();

    if (! $application) {
        $latestApp = Application::with('offre')
            ->where('id', $candidatureId)
            ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->first();

        $status = $latestApp?->status?->value ?? 'inconnu';

        return "L'analyse de ce candidat n'est pas encore disponible "
             . "(statut : {$status}).";
    }

    $a = $application->analyse;

    return [
        'candidat'               => $application->candidate->name,
        'offre'                  => $application->offre->titre,
        'matching_score'         => $a->matching_score,
        'recommandation'         => $a->recommandation->value,
        'justification'          => $a->justification,
        'points_forts'           => $a->points_forts,
        'lacunes'                => $a->lacunes,
        'competences_manquantes' => $a->competences_manquantes,
        'competences_extraites'  => $a->competences_extraites,
        'annees_experience'      => $a->annees_experience,
        'niveau_etudes'          => $a->niveau_etudes,
        'langues'                => $a->langues,
    ];
}
```

---

### Tool 2 — `getJobRequirements`

**Signature**

```php
getJobRequirements(int $offreId): array|string
```

**Description fournie à l'agent**

> Récupère les critères d'une offre d'emploi (titre, description, compétences requises,
> expérience minimum). Appelle ce tool quand l'utilisateur demande des informations sur
> le poste ou veut contextualiser l'évaluation d'un candidat.

**Implémentation**

```php
public function getJobRequirements(int $offreId): array|string
{
    $offre = Offre::where('id', $offreId)
        ->where('user_id', auth()->id())
        ->first();

    if (! $offre) {
        return "Offre introuvable ou accès non autorisé.";
    }

    return [
        'titre'                   => $offre->titre,
        'description'             => $offre->description,
        'competences_requises'    => $offre->competences_requises ?? [],
        'niveau_experience_min'   => $offre->niveau_experience_min,
    ];
}
```

---

### Tool 3 — `compareCandidates`

**Signature**

```php
compareCandidates(int $id1, int $id2): array|string
```

**Description fournie à l'agent**

> Compare les analyses de deux candidatures soumises sur la même offre. Retourne les
> données des deux analyses côte à côte pour permettre une comparaison argumentée.
> Les paramètres sont les IDs des candidatures (applications). Appelle ce tool quand
> l'utilisateur demande de comparer deux profils.

**Implémentation**

```php
public function compareCandidates(int $id1, int $id2): array|string
{
    $apps = Application::with('candidate', 'analyse', 'offre')
        ->whereIn('id', [$id1, $id2])
        ->whereHas('analyse')
        ->whereHas('offre', fn ($q) => $q->where('user_id', auth()->id()))
        ->get();

    if ($apps->count() !== 2) {
        return "Une ou plusieurs candidatures sont introuvables ou non autorisées.";
    }

    $app1 = $apps->firstWhere('id', $id1);
    $app2 = $apps->firstWhere('id', $id2);

    if ($app1->offre_id !== $app2->offre_id) {
        return "Les deux candidatures doivent appartenir à la même offre d'emploi.";
    }

    if (! $app1->analyse || ! $app2->analyse) {
        return "L'une des deux analyses n'est pas encore disponible.";
    }

    return [
        'offre'      => $app1->offre->titre,
        'candidat_1' => $this->formatAnalyse($app1),
        'candidat_2' => $this->formatAnalyse($app2),
    ];
}

private function formatAnalyse(Application $app): array
{
    $a = $app->analyse;
    return [
        'id'                     => $app->id,
        'nom'                    => $app->candidate->name,
        'matching_score'         => $a->matching_score,
        'recommandation'         => $a->recommandation->value,
        'points_forts'           => $a->points_forts,
        'lacunes'                => $a->lacunes,
        'competences_manquantes' => $a->competences_manquantes,
        'annees_experience'      => $a->annees_experience,
        'justification'          => $a->justification,
    ];
}
```

---

## Enregistrement auprès de l'Agent

Les tools sont enregistrés dans la définition de l'agent (spec 07). Schéma général avec
`laravel/ai` :

```php
// Dans la classe de l'agent ou le service qui le construit
protected array $tools = [
    GetCandidateAnalysisTool::class,
    GetJobRequirementsTool::class,
    CompareCandidatesTool::class,
];
```

Chaque `*Tool` est une classe invocable (ou équivalent SDK) qui encapsule la logique
ci-dessus et expose sa description à l'agent.

## Pourquoi des Tools plutôt que des réponses directes de l'IA ?

> Sans tools, l'agent génère des réponses plausibles mais inventées (hallucination) — le
> score, les lacunes et les recommandations seraient fabriqués, ce qui est inacceptable
> dans un contexte RH. Les tools forcent l'agent à consulter la base réelle avant de
> répondre, rendant chaque réponse factuelle et traçable. En démo, cela se voit : on
> peut observer l'appel de tool dans les logs avant la réponse finale.

## Critères d'acceptation

- [ ] `getCandidateAnalysis(id_valide)` retourne toutes les données de l'`Analyse`
  correspondante.
- [ ] `getCandidateAnalysis(id_autre_utilisateur)` retourne un message d'erreur contrôlé
  (pas de 500).
- [ ] `getJobRequirements(id_valide)` retourne les critères de l'offre.
- [ ] `compareCandidates(id1, id2)` — si offres différentes → erreur contrôlée.
- [ ] Aucun des tools n'effectue d'écriture en base (vérifiable par log des requêtes SQL).
- [ ] Chaque tool peut être testé indépendamment de l'agent (via tinker ou test unitaire).

```bash
docker compose exec app php artisan tinker
# > app(GetCandidateAnalysisTool::class)(1)
```

## Dépendances

- Requiert : spec 03 (candidatures), spec 04 (analyses), spec 02 (offres).
- Requis par : spec 07 (agent conversationnel — les tools lui sont injectés).

## Branche Git

`feature/tools-laravel-ai` → `develop`

## Workflow OpenSpec

```bash
opsx propose tools-larave-ia
opsx apply tools-larave-ia
opsx sync tools-larave-ia
opsx archive tools-larave-ia
```
