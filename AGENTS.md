# AGENTS.md — TalentMatch

Ce fichier définit comment les agents de code (Laravel Boost / MCP, Copilot, Claude Code,
etc.) doivent travailler sur ce dépôt. Il est lu par les outils IA avant toute génération
de code et complète les specs dans `openspec/specs/`.

## 1. Contexte projet

TalentMatch est une application Laravel de présélection RH. Deux couches IA :

1. **Structured output** (`laravel/ai`) : l'analyse d'un CV contre une offre retourne un
   JSON typé strict (voir `openspec/specs/04-analyse-ia.md`).
2. **Agent conversationnel** avec **tools** Laravel et **mémoire de conversation**
   persistée (voir `openspec/specs/05-tools-laravel-ai.md`,
   `06-conversation-memory.md`, `07-agent-conversationnel.md`).

## 2. Stack & environnement

- Laravel 13, PHP 8.3, MySQL 8, Docker.
- **Toutes les commandes Laravel/Composer s'exécutent dans le conteneur `app`** :
  ```bash
  docker compose exec app php artisan ...
  docker compose exec app composer ...
  docker compose exec app php artisan queue:work
  ```
- Ne jamais exécuter `php artisan` ou `composer` directement sur la machine hôte.
- Laravel Boost est installé comme dev dependency et tourne en MCP server local — il
  fournit le schéma DB, les routes et les versions de packages à l'agent. Toujours
  l'interroger avant de générer du code touchant au schéma ou aux routes.

## 3. Workflow obligatoire — specs avant code

- Aucune ligne de code métier n'est écrite sans spec validée dans `openspec/specs/`.
- Cycle : `opsx propose` → `opsx plan` → `opsx tasks` → `opsx implement` (voir
  `docs/workflow-commands.md`).
- `openspec/taskboard.md` est la source de vérité pour les priorités et dépendances
  entre tâches.

## 4. Conventions de code

- PSR-12, types stricts (`declare(strict_types=1)` recommandé sur les classes
  service/job/tool).
- Toute création/modification de ressource passe par un **Form Request** dédié — jamais
  de `$request->all()` non validé.
- Tout champ JSON/array ou enum stocké en base **doit** avoir un cast Eloquent explicite
  (`$casts`).
- Pas de logique métier dans les contrôleurs au-delà de l'orchestration (validation →
  service/job → réponse).
- Eager loading systématique (`with()`, `withCount()`) — zéro requête N+1, vérifié avec
  Laravel Debugbar.

## 5. Règles spécifiques IA — ce que l'agent NE DOIT JAMAIS faire

- ❌ Ne **jamais** calculer le `matching_score` ou la `recommandation` via des règles
  `if/else` ou des regex. Le score est **toujours** produit par `laravel/ai` via
  structured output, contraint par le schéma défini dans
  `openspec/specs/04-analyse-ia.md`.
- ❌ Ne **jamais** exécuter l'appel IA d'analyse de façon synchrone dans une requête
  HTTP. L'analyse passe **toujours** par `AnalyseCandidatJob` sur une queue.
- ❌ L'agent conversationnel ne doit **jamais** répondre à une question factuelle
  (score, lacunes, critères d'offre, comparaison) sans appeler le tool correspondant
  (`getCandidateAnalysis`, `getJobRequirements`, `compareCandidates`). Une réponse
  factuelle sans appel de tool = hallucination = bug.
- ❌ Aucun tool ne doit retourner de données appartenant à un autre utilisateur que celui
  authentifié. Chaque tool vérifie `user_id` avant de retourner une ressource.
- ❌ Ne jamais désactiver ou contourner la validation du schéma JSON de structured
  output, même en cas d'erreur. En cas de réponse hors schéma : retry (max 3) puis
  statut `failed` + log — jamais d'insertion de données invalides en base.
- ❌ Ne jamais committer `.env`, clés API, ou tokens.
- ❌ Ne jamais modifier une migration déjà jouée — créer une nouvelle migration.

## 6. Mémoire de conversation

- Les échanges agent ↔ utilisateur sont persistés via les tables de mémoire fournies par
  le SDK `laravel/ai` (voir `openspec/specs/06-conversation-memory.md`).
- Une conversation est rattachée à une candidature/analyse précise — ne jamais partager
  le contexte de mémoire entre deux candidats ou deux utilisateurs différents.

## 7. Commits

- Commits quotidiens obligatoires, Conventional Commits + mention `[AI-assisted]` quand
  le code a été généré ou significativement assisté par un agent IA (voir
  `docs/commit-strategy.md`).
- Toujours préciser dans le message ou la description ce qui a été corrigé/modifié
  manuellement après génération, et pourquoi.

## 8. Branches

- `main` ← `develop` ← `feature/*`. Voir `docs/branching-strategy.md` pour le détail par
  branche et l'ordre de merge recommandé.

## 9. Cas limites à toujours gérer

- CV vide ou trop court → rejeté à la validation (Form Request), jamais envoyé à l'IA.
- Offre sans compétences requises → l'analyse reste possible ; le scoring se base alors
  sur la description du poste et l'expérience du candidat (le prompt l'indique
  explicitement).
- Réponse IA hors schéma → retry automatique puis échec propre (`status = failed`),
  jamais de crash ni de données corrompues en base.
