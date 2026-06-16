# openspec/ — Organisation

```
openspec/
├── config.yaml              # configuration globale OpenSpec (stack, workflow, garde-fous IA)
├── taskboard.md              # phases, tâches, priorités, dépendances, ordre d'implémentation
├── README.md                 # ce fichier
├── specs/                     # 1 fichier = 1 feature, spec figée avant implémentation
│   ├── 00-data-model.md
│   ├── 01-authentication.md
│   ├── 02-offres-crud.md
│   ├── 03-candidatures.md
│   ├── 04-analyse-ia.md
│   ├── 05-tools-laravel-ai.md
│   ├── 06-conversation-memory.md
│   ├── 07-agent-conversationnel.md
│   ├── 08-bonus-comparaison-candidats.md
│   └── 09-bonus-classement.md
└── changes/                   # généré par `opsx propose` — 1 dossier par changement en cours
    └── <feature-slug>/
        ├── proposal.md        # sortie de `opsx propose`
        ├── plan.md             # sortie de `opsx plan`
        └── tasks.md            # sortie de `opsx tasks`
```

## Règles

- Le préfixe numérique de `specs/*.md` reflète l'ordre de dépendance/implémentation (voir
  `taskboard.md` et `docs/implementation-order.md`), pas une priorité Jira.
- `openspec/changes/<slug>/` est un espace de travail temporaire généré par le cycle
  `opsx propose → plan → tasks → implement`. Une fois la feature implémentée et mergée,
  son contenu peut être archivé (`openspec/changes/_archive/<slug>/`) ou supprimé — la
  spec finale dans `specs/` reste la source de vérité.
- Chaque spec contient obligatoirement une section **« Hors périmètre / Ce que
  l'implémentation (et l'agent) ne doit PAS faire »** — c'est ce qui est montré en démo
  pour répondre à « Comment as-tu défini ce que l'agent ne doit PAS faire ? ».
- `docs/` (à la racine du dépôt, hors `openspec/`) contient les documents transverses :
  stratégie de branches, ordre d'implémentation, décisions d'architecture, stratégie de
  commit — référencés depuis le taskboard mais pas spécifiques à une seule feature.

## Slugs `opsx` ↔ fichiers specs

| Fichier spec                              | Slug `opsx`                     |
|--------------------------------------------|----------------------------------|
| `00-data-model.md`                          | *(pas de cycle opsx — fondation)* |
| `01-authentication.md`                      | `authentication`                  |
| `02-offres-crud.md`                         | `offres-crud`                     |
| `03-candidatures.md`                        | `candidatures`                    |
| `04-analyse-ia.md`                          | `analyse-ia`                      |
| `05-tools-laravel-ai.md`                    | `tools-laravel-ai`                |
| `06-conversation-memory.md`                 | `conversation-memory`             |
| `07-agent-conversationnel.md`               | `agent-conversationnel`           |
| `08-bonus-comparaison-candidats.md`         | `bonus-comparaison-candidats`     |
| `09-bonus-classement.md`                    | `bonus-classement`                |
