# Analyse IA

## Purpose

AI-powered CV analysis that evaluates candidate fit against job offers using structured output, processes results asynchronously via queue, and displays analysis in the application detail view.

## Requirements

### Requirement: Async CV analysis via queue
The system SHALL process CV analysis asynchronously using a Laravel queue job, referencing an Application instead of a Candidate and never blocking the HTTP request.

#### Scenario: Job dispatched on application creation
- **WHEN** a valid Application is created (via assignment from candidate or offer page)
- **THEN** the system dispatches `AnalyseCandidatJob` with the Application to the default queue and immediately returns with a flash message
- **AND** the Application status is set to `pending`

#### Scenario: Job sets analysing status
- **WHEN** `AnalyseCandidatJob` begins execution
- **THEN** the system updates the Application status to `analysing`

#### Scenario: Job completes successfully
- **WHEN** `AnalyseCandidatJob` receives a valid AI response conforming to the schema
- **THEN** the system creates an `Analyse` record linked via `application_id`, and updates the Application status to `completed`

#### Scenario: Job fails after max retries
- **WHEN** `AnalyseCandidatJob` fails 3 times (schema mismatch or AI error)
- **THEN** the system updates the Application status to `failed`, logs the error, and does not create an invalid `Analyse` record

#### Scenario: Retry failed analysis
- **WHEN** `application.status` is `failed`
- **AND** a user clicks "Réessayer" or POSTs to `/applications/{application}/retry`
- **THEN** the system updates the Application status to `pending` and dispatches a new `AnalyseCandidatJob`

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
The system SHALL persist analysis results linked to an Application via `application_id`, with proper type casting for arrays and enums.

#### Scenario: Analyse belongs to Application
- **WHEN** an `Analyse` record is created
- **THEN** its `application_id` FK references the `applications` table
- **AND** the `Analyse` model has a `belongsTo(Application::class)` relationship

#### Scenario: Array fields are cast
- **WHEN** an `Analyse` record is created with competences_extraites, langues, points_forts, lacunes, competences_manquantes
- **THEN** the system stores them as JSON and casts them to PHP arrays on retrieval

#### Scenario: Recommendation enum is cast
- **WHEN** an `Analyse` record is created with recommandation value
- **THEN** the system casts it to `RecommandationEnum` enum on retrieval

### Requirement: Analysis result display
The system SHALL display AI analysis results in the application detail view.

#### Scenario: Full analysis display
- **WHEN** an authenticated user views an application with a completed analysis
- **THEN** the system displays: matching score (0-100 badge), recommendation badge (color-coded), points forts list, lacunes list, competences manquantes tags, extracted profile (experience, studies, languages, competences), and justification paragraph

#### Scenario: Processing state display
- **WHEN** an authenticated user views an application with status `analysing`
- **THEN** the system displays a "Analyse en cours..." indicator

#### Scenario: Failed state display
- **WHEN** an authenticated user views an application with status `failed`
- **THEN** the system displays an error message with "Réessayer" button that dispatches AnalyseCandidatJob
