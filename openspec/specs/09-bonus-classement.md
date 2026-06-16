# Spec 09 — Bonus : Classement Automatique des Candidats

## Statut

Bonus (P3) — à implémenter en Phase 5 si le temps le permet.

## Objectif

Trier les candidats d'une offre par `matching_score` décroissant pour permettre une
priorisation rapide.

## Dans le périmètre

- Tri par défaut sur la liste des candidatures d'une offre (vue détail offre).
- Indicateur de position/rang visible sur chaque ligne.
- Compatible avec la pagination existante.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- **Jamais** recalculer ou modifier le score — tri uniquement sur la valeur stockée dans
  `analyses.matching_score`.
- Pas de tri côté client (JavaScript) — le tri se fait en base via `orderBy`.
- Les candidatures `pending`/`failed` (sans analyse) apparaissent en fin de liste, pas
  supprimées.

## Implémentation

Modifier le scope de la requête dans `OffreController::show()` :

```php
$candidatures = $offre->candidatures()
    ->with('analyse')
    ->leftJoin('analyses', 'analyses.candidature_id', '=', 'candidatures.id')
    ->orderByDesc('analyses.matching_score')
    ->orderBy('candidatures.created_at')  // tie-breaker
    ->select('candidatures.*')
    ->paginate(20);
```

Ou via un scope Eloquent sur `Candidature` :

```php
public function scopeOrderedByScore(Builder $query): Builder
{
    return $query
        ->leftJoin('analyses', 'analyses.candidature_id', '=', 'candidatures.id')
        ->orderByDesc('analyses.matching_score')
        ->select('candidatures.*');
}
```

## Critères d'acceptation

- [ ] La liste des candidats d'une offre est triée par score décroissant par défaut.
- [ ] Les candidatures sans score (`pending`/`failed`) apparaissent en fin de liste.
- [ ] Debugbar confirme 0 N+1 — la jointure remplace le chargement lazy.
- [ ] Le rang est affiché visuellement (1er, 2e, 3e…) avec mise en évidence du top 3.

## Dépendances

- Requiert : spec 04 (analyses avec `matching_score`), spec 02 (vue détail offre).

## Branche Git

`feature/bonus-classement` → `develop`

## Workflow OpenSpec

```bash
opsx propose bonus-classement
opsx apply bonus-classement
opsx sync bonus-classement
opsx archive bonus-classement
```
