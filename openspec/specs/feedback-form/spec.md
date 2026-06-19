# Feedback Form

## Purpose

Allow recruiters to submit structured feedback (bug reports, suggestions, analysis reviews) with relevant context and priority, persisted in the database for team review.

## Requirements

### Requirement: Structured feedback submission form
The system SHALL provide a multi-field feedback form at `GET /feedback` with type selection, contextual fields, priority toggle, and validation.

#### Scenario: Display feedback form with all field types
- **WHEN** an authenticated user navigates to `/feedback`
- **THEN** the page displays: type selection (4 radio card options), sujet text input, message textarea, priority toggle (Low/Medium/High), and submit button
- **AND** the offre and candidate dropdowns are hidden by default

#### Scenario: Show contextual dropdowns for analysis feedback
- **WHEN** a user selects "Retour sur une analyse IA" as the feedback type
- **THEN** the offre dropdown and candidate dropdown become visible

#### Scenario: Hide contextual dropdowns for non-analysis types
- **WHEN** a user selects any type other than "Retour sur une analyse IA"
- **THEN** the offre and candidate dropdowns are hidden

#### Scenario: Offre dropdown is populated
- **WHEN** the contextual dropdowns are visible
- **THEN** the offre dropdown lists all offres from the database sorted by creation date descending

#### Scenario: Candidate dropdown is populated
- **WHEN** the contextual dropdowns are visible
- **THEN** the candidate dropdown lists all candidates from the database sorted by name

#### Scenario: Validation fails with missing required fields
- **WHEN** a user submits the form without a type, sujet, or message
- **THEN** the system returns validation errors and does not persist the feedback

#### Scenario: Validation fails with short message
- **WHEN** a user submits a message shorter than 20 characters
- **THEN** the system returns a validation error for the message field

#### Scenario: Successful feedback submission
- **WHEN** a user submits the form with valid data
- **THEN** the system creates a feedbacks record with the submitted data, user_id (from authenticated user), status "nouveau", and redirects back with a success flash message

### Requirement: Feedback data persistence
The system SHALL persist feedback submissions in a `feedbacks` database table with proper enums, nullable foreign keys, and timestamps.

#### Scenario: Feedback record is created
- **WHEN** a feedback is successfully submitted
- **THEN** the feedbacks table contains: id, user_id (nullable FK to users), type (bug/suggestion/analyse/autre), offre_id (nullable FK to offres), candidate_id (nullable FK to candidates), sujet, message, priorite (low/medium/high default medium), status (nouveau/lu/traité default nouveau), and timestamps

#### Scenario: Feedback persists after user deletion
- **WHEN** a user who submitted feedback is deleted
- **THEN** the feedback record's user_id is set to null (feedback is preserved)

#### Scenario: Feedback persists after offre or candidate deletion
- **WHEN** an offre or candidate referenced in feedback is deleted
- **THEN** the feedback record's offre_id or candidate_id is set to null (feedback is preserved)

#### Scenario: Default priority is medium
- **WHEN** a feedback is submitted without specifying priority
- **THEN** the priorite field defaults to "medium"

#### Scenario: Default status is nouveau
- **WHEN** a feedback record is created
- **THEN** the status field defaults to "nouveau"
