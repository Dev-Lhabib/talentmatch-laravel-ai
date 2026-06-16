# Spec 00 — Modèle de données (MCD / MLD)

## Lié à

Toutes les User Stories (US1-US11) — ce document est la référence pour toutes les
migrations, relations et Eloquent Casts du projet.

## MCD — Entités et relations

- **User** (1) ── possède ── (N) **Offre**
- **User** (1) ── possède ── (N) **Candidature**
- **Offre** (1) ── reçoit ── (N) **Candidature**
- **Candidature** (1) ── génère ── (0..1) **Analyse**
- **Candidature** (1) ── possède ── (0..1) **Conversation**
- **Conversation** (1) ── contient ── (N) **Message**
- **Offre** (N) ── requiert ── (N) **Compétence** (table pivot `offre_competence`)
- **Analyse** (N) ── extrait ── (N) **Compétence** (table pivot `analyse_competence`)

## MLD — Tables

### `users`

| Colonne    | Type         | Contraintes              |
|------------|--------------|--------------------------|
| id         | bigint PK    | auto-increment           |
| nom        | string       | required                 |
| email      | string       | unique, required         |
| password   | string       | hashed (bcrypt)          |
| created_at | timestamp    |                          |
| updated_at | timestamp    |                          |

### `offres`

| Colonne        | Type         | Contraintes                        |
|----------------|--------------|------------------------------------|
| id             | bigint PK    | auto-increment                     |
| user_id        | bigint FK    | → users.id, cascade delete         |
| titre          | string       | required                           |
| description    | text         | required                           |
| experience_min | integer      | default 0, min 0                   |
| created_at     | timestamp    |                                    |
| updated_at     | timestamp    |                                    |

### `candidatures`

| Colonne       | Type         | Contraintes                              |
|---------------|--------------|------------------------------------------|
| id            | bigint PK    | auto-increment                           |
| offre_id      | bigint FK    | → offres.id, cascade delete              |
| user_id       | bigint FK    | → users.id (dénormalisé pour scoping)    |
| nom_candidat  | string       | required                                 |
| cv_text       | text         | required, min 50 chars                   |
| status        | enum         | pending/processing/completed/failed      |
| created_at    | timestamp    |                                          |
| updated_at    | timestamp    |                                          |

**Enum `StatutCandidatureEnum`** : `pending` · `processing` · `completed` · `failed`

### `analyses`

| Colonne                | Type         | Contraintes                              |
|------------------------|--------------|------------------------------------------|
| id                     | bigint PK    | auto-increment                           |
| candidature_id         | bigint FK    | → candidatures.id, unique, cascade delete|
| competences_extraites  | json         | cast → array                             |
| annees_experience      | integer      |                                          |
| niveau_etudes          | string       |                                          |
| langues                | json         | cast → array                             |
| matching_score         | integer      | 0-100                                    |
| points_forts           | json         | cast → array                             |
| lacunes                | json         | cast → array                             |
| competences_manquantes | json         | cast → array                             |
| recommandation         | enum         | convoquer/attente/rejeter                |
| justification          | text         |                                          |
| analyzed_at            | timestamp    | nullable — set when analysis completes   |
| created_at             | timestamp    |                                          |
| updated_at             | timestamp    |                                          |

**Enum `RecommandationEnum`** : `convoquer` · `attente` · `rejeter`

### `competences`

| Colonne    | Type         | Contraintes              |
|------------|--------------|--------------------------|
| id         | bigint PK    | auto-increment           |
| nom        | string       | unique                   |
| created_at | timestamp    |                          |
| updated_at | timestamp    |                          |

### `offre_competence` (pivot)

| Colonne       | Type      | Contraintes           |
|---------------|-----------|-----------------------|
| offre_id      | bigint FK | → offres.id           |
| competence_id | bigint FK | → competences.id      |

### `analyse_competence` (pivot — compétences extraites du CV)

| Colonne       | Type      | Contraintes           |
|---------------|-----------|-----------------------|
| analyse_id    | bigint FK | → analyses.id         |
| competence_id | bigint FK | → competences.id      |

### `conversations`

| Colonne         | Type         | Contraintes                              |
|-----------------|--------------|------------------------------------------|
| id              | bigint PK    | auto-increment                           |
| candidature_id  | bigint FK    | → candidatures.id, unique, cascade delete|
| user_id         | bigint FK    | → users.id                               |
| title           | string       | nullable — résumé auto de la conversation|
| created_at      | timestamp    |                                          |
| updated_at      | timestamp    |                                          |

### `messages`

| Colonne         | Type         | Contraintes                              |
|-----------------|--------------|------------------------------------------|
| id              | bigint PK    | auto-increment                           |
| conversation_id | bigint FK    | → conversations.id, cascade delete       |
| role            | enum         | user/assistant/tool                      |
| content         | text         | required                                 |
| created_at      | timestamp    |                                          |
| updated_at      | timestamp    |                                          |

**Enum `MessageRoleEnum`** : `user` · `assistant` · `tool`

## Diagramme des relations

```
users ──1:N── offres ──1:N── candidatures ──1:1── analyses
  │                               │                    │
  └──────────────1:N──────────────┘                    │
                                  │                    N:M
                               1:0..1             competences
                                  │                    │
                            conversations         N:M──┘
                                  │
                               1:N
                                  │
                              messages
```

## Eloquent Casts requis

| Modèle        | Champ                    | Cast                          |
|---------------|--------------------------|-------------------------------|
| Candidature   | status                   | StatutCandidatureEnum::class  |
| Analyse       | competences_extraites    | 'array'                       |
| Analyse       | langues                  | 'array'                       |
| Analyse       | points_forts             | 'array'                       |
| Analyse       | lacunes                  | 'array'                       |
| Analyse       | competences_manquantes   | 'array'                       |
| Analyse       | recommandation           | RecommandationEnum::class     |
| Analyse       | analyzed_at              | 'datetime'                    |
| Message       | role                     | MessageRoleEnum::class        |

## Relations Eloquent

```php
// User
public function offres(): HasMany       // user → offres
public function candidatures(): HasMany // user → candidatures

// Offre
public function user(): BelongsTo
public function candidatures(): HasMany
public function competences(): BelongsToMany  // via offre_competence

// Candidature
public function offre(): BelongsTo
public function user(): BelongsTo
public function analyse(): HasOne
public function conversation(): HasOne

// Analyse
public function candidature(): BelongsTo
public function competences(): BelongsToMany  // via analyse_competence

// Conversation
public function candidature(): BelongsTo
public function user(): BelongsTo
public function messages(): HasMany

// Message
public function conversation(): BelongsTo
```

## Requirements

### Requirement: Users table schema
The system SHALL create a `users` table with columns: `id` (bigint PK auto-increment), `nom` (string, required), `email` (string, unique, required), `password` (string, hashed bcrypt), `created_at`, `updated_at`.

#### Scenario: Users table exists with correct schema
- **WHEN** the migration `create_users_table` is executed
- **THEN** the `users` table SHALL exist with columns `id`, `nom`, `email`, `password`, `created_at`, `updated_at`

#### Scenario: Email uniqueness enforced
- **WHEN** a user is created with an email that already exists
- **THEN** the database SHALL reject the insert with a unique constraint violation

### Requirement: Offres table schema
The system SHALL create an `offres` table with columns: `id` (bigint PK auto-increment), `user_id` (bigint FK → users.id, cascade delete), `titre` (string, required), `description` (text, required), `experience_min` (integer, default 0, min 0), `created_at`, `updated_at`.

#### Scenario: Offre belongs to a user
- **WHEN** an offre is created with `user_id = 5`
- **THEN** the offre SHALL be linked to user 5 via foreign key

#### Scenario: Cascade delete on user removal
- **WHEN** a user with offres is deleted
- **THEN** all associated offres SHALL be deleted automatically

### Requirement: Candidatures table schema
The system SHALL create a `candidatures` table with columns: `id` (bigint PK auto-increment), `offre_id` (bigint FK → offres.id, cascade delete), `user_id` (bigint FK → users.id, denormalized for scoping), `nom_candidat` (string, required), `cv_text` (text, required, min 50 chars), `status` (enum: pending/processing/completed/failed), `created_at`, `updated_at`.

#### Scenario: Candidature status default
- **WHEN** a candidature is created without specifying status
- **THEN** the status SHALL default to `pending`

#### Scenario: Cascade delete on offre removal
- **WHEN** an offre with candidatures is deleted
- **THEN** all associated candidatures SHALL be deleted automatically

### Requirement: Analyses table schema
The system SHALL create an `analyses` table with columns: `id` (bigint PK auto-increment), `candidature_id` (bigint FK → candidatures.id, unique, cascade delete), `competences_extraites` (json, cast array), `annees_experience` (integer), `niveau_etudes` (string), `langues` (json, cast array), `matching_score` (integer, 0-100), `points_forts` (json, cast array), `lacunes` (json, cast array), `competences_manquantes` (json, cast array), `recommandation` (enum: convoquer/attente/rejeter), `justification` (text), `analyzed_at` (timestamp, nullable), `created_at`, `updated_at`.

#### Scenario: One analysis per candidature
- **WHEN** an analysis is created for candidature_id = 10
- **THEN** a second analysis for the same candidature_id SHALL be rejected (unique constraint)

#### Scenario: JSON columns cast to array
- **WHEN** an analysis is retrieved via Eloquent
- **THEN** `competences_extraites`, `langues`, `points_forts`, `lacunes`, `competences_manquantes` SHALL be PHP arrays

### Requirement: Competences table schema
The system SHALL create a `competences` table with columns: `id` (bigint PK auto-increment), `nom` (string, unique), `created_at`, `updated_at`.

#### Scenario: Competence name uniqueness
- **WHEN** a competence is created with a nom that already exists
- **THEN** the database SHALL reject the insert with a unique constraint violation

### Requirement: Offre-Competence pivot table
The system SHALL create an `offre_competence` pivot table with columns: `offre_id` (bigint FK → offres.id), `competence_id` (bigint FK → competences.id). The composite primary key SHALL be (`offre_id`, `competence_id`).

#### Scenario: Many-to-many relationship
- **WHEN** offre 1 is linked to competences 2 and 3
- **THEN** two rows SHALL exist in `offre_competence` with the correct foreign keys

### Requirement: Analyse-Competence pivot table
The system SHALL create an `analyse_competence` pivot table with columns: `analyse_id` (bigint FK → analyses.id), `competence_id` (bigint FK → competences.id). The composite primary key SHALL be (`analyse_id`, `competence_id`).

#### Scenario: Extracted competences linked to analysis
- **WHEN** analysis 5 has extracted competences 1, 2, 4
- **THEN** three rows SHALL exist in `analyse_competence` linking analysis 5 to those competences

### Requirement: Conversations table schema
The system SHALL create a `conversations` table with columns: `id` (bigint PK auto-increment), `candidature_id` (bigint FK → candidatures.id, unique, cascade delete), `user_id` (bigint FK → users.id), `title` (string, nullable), `created_at`, `updated_at`.

#### Scenario: One conversation per candidature
- **WHEN** a conversation is created for candidature_id = 7
- **THEN** a second conversation for the same candidature_id SHALL be rejected (unique constraint)

### Requirement: Messages table schema
The system SHALL create a `messages` table with columns: `id` (bigint PK auto-increment), `conversation_id` (bigint FK → conversations.id, cascade delete), `role` (enum: user/assistant/tool), `content` (text, required), `created_at`, `updated_at`.

#### Scenario: Cascade delete on conversation removal
- **WHEN** a conversation with messages is deleted
- **THEN** all associated messages SHALL be deleted automatically

### Requirement: Eloquent models with relationships
The system SHALL create Eloquent models for User, Offre, Candidature, Analyse, Competence, Conversation, and Message with the relationships defined in this spec.

#### Scenario: User has many offres
- **WHEN** a user with offres calls `$user->offres`
- **THEN** a HasMany relationship SHALL return all offres belonging to that user

#### Scenario: Candidature has one analyse
- **WHEN** a candidature with an analyse calls `$candidature->analyse`
- **THEN** a HasOne relationship SHALL return the single analyse (or null)

#### Scenario: Offre many-to-many competences
- **WHEN** an offre calls `$offre->competences`
- **THEN** a BelongsToMany relationship SHALL return all competences via `offre_competence` pivot

### Requirement: PHP enums for status and role fields
The system SHALL create PHP 8.1 backed enums: `StatutCandidatureEnum` (pending/processing/completed/failed), `RecommandationEnum` (convoquer/attente/rejeter), `MessageRoleEnum` (user/assistant/tool). All enums SHALL use string backing values.

#### Scenario: Enum cast on Candidature status
- **WHEN** a candidature is retrieved with `status = 'pending'`
- **THEN** `$candidature->status` SHALL be a `StatutCandidatureEnum` instance

#### Scenario: Enum cast on Analyse recommandation
- **WHEN** an analyse is retrieved with `recommandation = 'convoquer'`
- **THEN** `$analyse->recommandation` SHALL be a `RecommandationEnum` instance

### Requirement: Eloquent casts for JSON columns
The system SHALL define explicit `$casts` on the Analyse model for all JSON columns (`competences_extraites`, `langues`, `points_forts`, `lacunes`, `competences_manquantes`) casting them to `array`, and `analyzed_at` casting to `datetime`.

#### Scenario: Array cast on JSON columns
- **WHEN** an analyse is retrieved
- **THEN** the five JSON columns SHALL be native PHP arrays, not JSON strings

## Hors périmètre

- Pas de rôles multiples — un seul type d'utilisateur.
- Pas de fichiers binaires — le CV est uniquement du texte.
- Pas de soft deletes dans cette version (peut être ajouté).
- La table `competences` est optionnelle si les compétences restent
  uniquement en JSON dans `analyses.competences_extraites` —
  à décider lors de l'implémentation.