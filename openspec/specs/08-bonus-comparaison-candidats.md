# Spec 08 — Comparaison de Deux Candidats

## Statut

Implementé — voir aussi `comparison-flow` et `comparison-storage` specs.

## Objectif

Permettre à l'agent RH de sélectionner deux candidats d'une même offre et d'obtenir
une comparaison structurée automatique via l'IA.

## Dans le périmètre

- UI de sélection de deux candidats dans le détail d'une offre (cases à cocher + Alpine.js).
- Bouton « Comparer ces 2 candidats » qui POST vers `/comparisons/create`.
- L'appel IA génère un verdict structuré (JSON) stocké dans la table `comparisons`.
- Page de résultat dédiée (`/comparisons/{id}`) en deux colonnes avec gagnant surligné.
- Accessible uniquement si les deux candidatures ont `status = completed`.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- **Jamais** comparer deux candidats d'offres différentes (le controller retourne une
  erreur contrôlée).
- **Jamais** utiliser le chat pour la comparaison — le chat reste pour les questions
  individuelles par candidat.
- Pas de file d'attente — l'appel IA est synchrone.

## Flux utilisateur

1. Sur `/offres/{offre}`, l'agent RH coche deux candidatures `completed`.
2. Clic sur « Comparer ces 2 candidats » → overlay de chargement Alpine.js.
3. POST vers `/comparisons/create` → appel IA synchrone → stockage en base.
4. Redirection vers `/comparisons/{id}` avec le résultat structuré.

## Critères d'acceptation

- [ ] Sélectionner exactement 2 candidats `completed` et cliquer « Comparer » →
  redirection vers `/comparisons/{id}` avec overlay de chargement.
- [ ] La page de résultat affiche les deux candidats en deux colonnes, gagnant surligné.
- [ ] La matrice de compétences montre ✅/❌ pour chaque compétence requise de l'offre.
- [ ] Les verdicts IA et la raison du gagnant sont affichés.
- [ ] Tenter de comparer un candidat `pending` → erreur contrôlée.
- [ ] Tenter de comparer deux candidats d'offres différentes → erreur contrôlée.
- [ ] Si l'IA échoue, la comparaison est créée avec des messages de fallback.

## Dépendances

- Requiert : `comparison-storage` (table `comparisons`), `comparison-flow` (controller).
- Requiert : spec 04 (analyses), spec 02 (offres).

## Branche Git

`feature/improve-comparison-ui` → `develop`

## Workflow OpenSpec

```bash
opsx propose improve-comparison-ui
opsx apply improve-comparison-ui
opsx sync improve-comparison-ui
opsx archive improve-comparison-ui
```
