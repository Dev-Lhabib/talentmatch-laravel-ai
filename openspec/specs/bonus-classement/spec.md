# Bonus: Classement Automatique

## Purpose

Automatically sort candidates by their AI analysis score to enable quick prioritization for hiring decisions.

## Requirements

### Requirement: Candidatures sorted by matching score
The system SHALL sort candidatures by `matching_score` descending by default on the offer detail page.

#### Scenario: Candidatures sorted by score descending
- **WHEN** an authenticated offer owner views the offer detail page
- **THEN** candidatures are ordered by `matching_score` from highest to lowest

#### Scenario: Candidatures without scores appear last
- **WHEN** a candidature has no analysis (status `pending` or `failed`)
- **THEN** it appears after all candidatures with scores, ordered by `created_at` ascending

#### Scenario: Deterministic ordering for tied scores
- **WHEN** multiple candidatures have the same `matching_score`
- **THEN** they are ordered by `created_at` ascending (earliest first)

### Requirement: Rank indicator displayed
The system SHALL display a rank number for each candidature in the list.

#### Scenario: Rank number shown for each candidature
- **WHEN** the offer detail page loads with candidatures
- **THEN** each candidature card displays its rank position (1, 2, 3...)

#### Scenario: Top 3 candidates highlighted
- **WHEN** a candidature is ranked 1st, 2nd, or 3rd
- **THEN** the rank indicator displays with distinct styling (gold, silver, bronze colors)

#### Scenario: No rank for unscored candidatures
- **WHEN** a candidature has no analysis score
- **THEN** no rank number is displayed on its card
