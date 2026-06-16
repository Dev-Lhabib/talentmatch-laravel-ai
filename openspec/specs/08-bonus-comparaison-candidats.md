# Spec 08 — Bonus : Comparaison de Deux Candidats

## Statut

Bonus (P3) — à implémenter en Phase 5 si le temps le permet.

## Objectif

Permettre à l'agent RH de sélectionner deux candidats d'une même offre et d'obtenir
une comparaison argumentée via l'assistant IA (tool `compareCandidates`).

## Dans le périmètre

- UI de sélection de deux candidats dans le détail d'une offre (cases à cocher).
- Bouton « Comparer » qui ouvre la vue chat avec un message pré-rempli.
- L'agent appelle `compareCandidates(id1, id2)` et produit une synthèse argumentée.
- Accessible uniquement si les deux candidatures ont `status = completed`.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- **Jamais** comparer deux candidats d'offres différentes (`compareCandidates` retourne
  une erreur contrôlée dans ce cas — voir spec 05).
- **Jamais** inventer la comparaison sans appeler `compareCandidates`.
- Pas de nouvelle page dédiée à la comparaison — la comparaison passe par le chat
  existant (spec 07), avec le contexte des deux candidats transmis via le tool.

## Flux utilisateur

1. Sur `/offres/{offre}`, l'agent RH coche deux candidatures `completed`.
2. Clic sur « Comparer ces deux candidats ».
3. Redirection vers le chat de la **première** candidature sélectionnée avec un message
   initial pré-rempli : *« Compare ce candidat avec le candidat #{{ id2 }} »*.
4. L'agent appelle `compareCandidates(id1, id2)` et répond avec une analyse comparative.

## Critères d'acceptation

- [ ] Sélectionner exactement 2 candidats `completed` et cliquer « Comparer » →
  redirection vers le chat avec message pré-rempli.
- [ ] L'agent produit une réponse comparative en citant scores, points forts/faibles de
  chacun et donne une recommandation finale argumentée.
- [ ] Tenter de comparer un candidat `pending` → bouton « Comparer » désactivé / message
  d'erreur.
- [ ] Tenter de comparer deux candidats d'offres différentes → erreur contrôlée (même
  si le cas ne devrait pas survenir via l'UI).

## Dépendances

- Requiert : spec 05 (`compareCandidates`), spec 07 (chat).

## Branche Git

`feature/bonus-comparaison-candidats` → `develop`

## Workflow OpenSpec

```bash
opsx propose "Bonus comparaison : sélection 2 candidats, message pré-rempli, appel compareCandidates via agent"
opsx plan bonus-comparaison-candidats
opsx tasks bonus-comparaison-candidats
opsx implement bonus-comparaison-candidats
```
