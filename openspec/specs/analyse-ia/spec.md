# Analyse IA

## Purpose

AI-powered CV analysis that evaluates candidate fit against job offers using structured output, processes results asynchronously via queue, and displays analysis in the candidature detail view.

## Requirements

### Requirement: Async CV analysis via queue
The system SHALL process CV analysis asynchronously using a Laravel queue job, never blocking the HTTP request.

#### Scenario: Job dispatched on candidature submission
- **WHEN** a valid candidature is submitted
- **THEN** the system dispatches `AnalyseCandidatJob` to the default queue and immediately returns to the user with a flash message

#### Scenario: Job sets processing status
- **WHEN** `AnalyseCandidatJob` begins execution
- **THEN** the system updates the candidature status to `processing`

#### Scenario: Job completes successfully
- **WHEN** `AnalyseCandidatJob` receives a valid AI response conforming to the schema
- **THEN** the system creates an `Analyse` record with all fields and updates the candidature status to `completed`

#### Scenario: Job fails after max retries
- **WHEN** `AnalyseCandidatJob` fails 3 times (schema mismatch or AI error)
- **THEN** the system updates the candidature status to `failed`, logs the error, and does not create an invalid `Analyse` record

### Requirement: AI structured output
The system SHALL call `laravel/ai` with a constrained JSON schema that enforces typed responses.

#### Scenario: Valid AI response
- **WHEN** the AI service receives a CV and job description
- **THEN** the system returns an `AnalyseResultDTO` with fields: competences_extraites, annees_experience, niveau_etudes, langues, matching_score, points_forts, lacunes, competences_manquantes, recommandation, justification

#### Scenario: AI response outside schema triggers retry
- **WHEN** the AI returns a response that does not match the expected schema
- **THEN** the system throws an exception and the job retries (up to 3 attempts)

#### Scenario: Offer without competences
- **WHEN** the job offer has no competences_requises
- **THEN** the system constructs a prompt that instructs the AI to base evaluation on job description and candidate experience only

### Requirement: Analysis data model
The system SHALL persist analysis results with proper type casting for arrays and enums.

#### Scenario: Array fields are cast
- **WHEN** an `Analyse` record is created with competences_extraites, langues, points_forts, lacunes, competences_manquantes
- **THEN** the system stores them as JSON and casts them to PHP arrays on retrieval

#### Scenario: Recommendation enum is cast
- **WHEN** an `Analyse` record is created with recommandation value
- **THEN** the system casts it to `RecommandationEnum` enum on retrieval

### Requirement: Analysis result display
The system SHALL display AI analysis results in the candidature detail view.

#### Scenario: Full analysis display
- **WHEN** an authenticated user views a candidature with a completed analysis
- **THEN** the system displays: matching score (0-100 badge), recommendation badge (color-coded), points forts list, lacunes list, competences manquantes tags, extracted profile (experience, studies, languages, competences), and justification paragraph

#### Scenario: Processing state display
- **WHEN** an authenticated user views a candidature with status `processing`
- **THEN** the system displays a "Analyse en cours..." indicator

#### Scenario: Failed state display
- **WHEN** an authenticated user views a candidature with status `failed`
- **THEN** the system displays an error message with option to retry (future)
