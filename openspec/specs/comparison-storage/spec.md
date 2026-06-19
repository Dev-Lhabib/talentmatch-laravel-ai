# Comparison Storage

## Purpose

Store structured AI comparison results between two candidates for the same job offer.

## Requirements

### Requirement: Comparisons database table

The system SHALL have a `comparisons` table to store structured AI comparison results.

The table SHALL contain:
- `id` — bigint unsigned, auto-increment primary key
- `offre_id` — bigint unsigned, foreign key to `offres.id`
- `application1_id` — bigint unsigned, foreign key to `applications.id`
- `application2_id` — bigint unsigned, foreign key to `applications.id`
- `candidate1_verdict` — text, AI-generated 2-3 sentence verdict for candidate 1
- `candidate2_verdict` — text, AI-generated 2-3 sentence verdict for candidate 2
- `winner_id` — integer, the `application_id` of the stronger candidate
- `winner_reason` — text, 1-sentence explanation of why the winner was chosen
- `created_at` — timestamp
- `updated_at` — timestamp

#### Scenario: Comparison record is created

- **WHEN** a comparison is created
- **THEN** all fields SHALL be persisted correctly
- **AND** both foreign keys SHALL reference existing applications
- **AND** `winner_id` SHALL equal either `application1_id` or `application2_id`

### Requirement: Comparison Eloquent model

The system SHALL have an `App\Models\Comparison` model with:
- `$fillable` containing all columns except `id` and timestamps
- `$casts` for integer fields
- Relationships: `offre()`, `application1()`, `application2()`, `winner()`

#### Scenario: Model relationships resolve correctly

- **WHEN** loading a Comparison with its relations
- **THEN** `offre`, `application1`, `application2` SHALL return the correct Eloquent models
- **AND** `winner` SHALL return the winning Application model
