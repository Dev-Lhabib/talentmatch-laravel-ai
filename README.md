# TalentMatch — Assistant IA de Présélection RH

Application Laravel de présélection automatisée de candidats, pilotée par un agent IA
(`laravel/ai`) avec sortie structurée (structured output), tools Laravel et mémoire de
conversation persistée.

## Démarrage

Voir `AGENTS.md` pour les conventions de développement et les garde-fous IA, et
`openspec/` pour la spécification complète du projet (specs par feature, taskboard,
configuration du workflow OpenSpec).

## Stack

- Laravel 13 / PHP 8.3
- MySQL 8
- Docker (toute la stack — app, db, queue — tourne en conteneurs)
- `laravel/ai` (structured output + agents + tools + conversation memory)
- Laravel Boost (MCP server de développement)

## Organisation du dépôt

```
.
├── AGENTS.md                 # règles pour les agents de code (Boost, etc.)
├── README.md                 # ce fichier
├── openspec/
│   ├── config.yaml            # configuration du workflow OpenSpec
│   ├── taskboard.md           # phases, tâches, priorités, dépendances
│   ├── README.md              # organisation du dossier openspec/
│   ├── specs/                  # 1 fichier = 1 feature
│   └── changes/                # généré par `opsx propose` (espace de travail)
├── docs/
│   ├── branching-strategy.md   # stratégie de branches Git
│   ├── workflow-commands.md    # commandes opsx + Docker
│   ├── implementation-order.md # planning par phases/jours
│   ├── architecture-decisions.md
│   └── commit-strategy.md
├── app/                        # code Laravel
├── docker-compose.yml
└── ...
```

## Workflow

1. Spec validée dans `openspec/specs/` avant tout code (`opsx propose/plan/tasks/implement`).
2. Implémentation dans la branche `feature/*` correspondante (voir `docs/branching-strategy.md`).
3. Commits quotidiens, mention `[AI-assisted]` quand pertinent (voir `docs/commit-strategy.md`).
4. Merge vers `develop`, puis `develop` → `main` aux jalons.
