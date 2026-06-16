# Spec 00 — Modèle de données (MCD / MLD)

## Lié à

Toutes les User Stories (US1-US11) — ce document est la référence pour toutes les
migrations, relations et `Eloquent Casts` du projet.

## MCD — Entités et relations

- **Utilisateur** (1) ── possède ── (N) **Offre**
- **Utilisateur** (1) ── possède ── (N) **Candidature**
- **Offre** (1) ── reçoit ── (N) **Candidature**
- **Candidature** (1) ── génère ── (0..1) **Analyse**
- **Candidature** (1) ── possède ── (0..1) **Conversation** *(table mémoire SDK)*
- **Conversation** (1) ── contient ── (N) **Message** *(table mémoire SDK)*

Diagramme simplifié :

```
Utilisateur ──1:N── Offre ──1:N── Candidature ──1:0..1── Analyse
     │                                  │
     └──────────────1:N─────────────────┘
                                         │
                                      1:0..1
                                         │
                                   Conversation ──1:N── Message
```

## MLD — Tables

### `users` (Laravel par défaut)

- `id` (PK)
- `name`, `email` (unique), `password`
- timestamps

### `offres`

- `id` (PK)
- `user_id` (FK → `users.id`)
- `titre` (string)
- `description` (text)
- `competences_requises` (json) — peut être un tableau vide (cas limite « offre sans
  compétences requises »)
- `niveau_experience_min` (integer, default 0)
- timestamps

### `candidatures`

- `id` (PK)
- `offre_id` (FK → `offres.id`)
- `user_id` (FK → `users.id`) — dénormalisé pour le scoping rapide des tools
- `nom_candidat` (string)
- `cv_text` (text)
- `status` (enum: `pending` | `processing` | `completed` | `failed`, default `pending`)
- timestamps

### `analyses`

- `id` (PK)
- `candidature_id` (FK → `candidatures.id`, unique — relation 1:0..1)
- `competences_extraites` (json → array)
- `annees_experience` (integer)
- `niveau_etudes` (string)
- `langues` (json → array)
- `matching_score` (integer, 0-100)
- `points_forts` (json → array)
- `lacunes` (json → array)
- `competences_manquantes` (json → array)
- `recommandation` (string → cast `RecommandationEnum`: `convoquer` | `attente` | `rejeter`)
- `justification` (text)
- timestamps

### Tables de mémoire de conversation (fournies par `laravel/ai`)

- Migrations publiées par le SDK (ex. `ai_conversations`, `ai_messages` ou équivalent
  selon la version installée — vérifier avec Boost).
- `ai_conversations` : ajouter une colonne `candidature_id` (FK nullable →
  `candidatures.id`) pour rattacher chaque conversation à un candidat analysé.
- `ai_messages` : structure gérée par le SDK (role, content, tool_calls,
  conversation_id, timestamps).

## Eloquent Casts requis

| Modèle        | Champ                     | Cast                            |
|---------------|----------------------------|----------------------------------|
| `Offre`       | `competences_requises`       | `array`                            |
| `Candidature` | `status`                     | enum (`StatutCandidatureEnum`)     |
| `Analyse`     | `competences_extraites`       | `array`                            |
| `Analyse`     | `langues`                     | `array`                             |
| `Analyse`     | `points_forts`                | `array`                             |
| `Analyse`     | `lacunes`                     | `array`                             |
| `Analyse`     | `competences_manquantes`      | `array`                             |
| `Analyse`     | `recommandation`              | enum (`RecommandationEnum`)        |

## Hors périmètre

- Pas de gestion de rôles multiples (un seul rôle « agent RH »).
- Pas de suppression en cascade automatique sans confirmation — soft deletes recommandés
  sur `offres` et `candidatures`.
- Pas de stockage de fichiers binaires : le CV est uniquement du texte (conforme à US5).
