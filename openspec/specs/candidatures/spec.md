# Candidatures

## Purpose

CV submission, tracking, and management for job applications — submit candidate CVs against offers, view analysis results, and manage candidature lifecycle.

## Requirements

### Requirement: CV submission
The system SHALL allow authenticated users to submit a CV (text) against an offer they own.

#### Scenario: Successful CV submission
- **WHEN** an authenticated user submits a candidature form with valid data (nom_candidat, cv_text min 50 chars) against their own offer
- **THEN** the system creates a candidature with `status = pending`, dispatches the analysis job if available, and redirects to the offre show page with a flash message

#### Scenario: Validation rejects short CV
- **WHEN** an authenticated user submits a candidature with cv_text shorter than 50 characters
- **THEN** the system returns the form with validation errors and does not create the candidature

#### Scenario: Validation rejects empty candidate name
- **WHEN** an authenticated user submits a candidature with an empty nom_candidat
- **THEN** the system returns the form with validation errors

### Requirement: Candidature detail view
The system SHALL display candidature details including status and analysis data when available.

#### Scenario: Detail with analysis
- **WHEN** an authenticated user views a candidature that has an associated analyse
- **THEN** the system displays the candidate name, status, matching score, recommendation badge, points forts, lacunes, and justification

#### Scenario: Detail without analysis
- **WHEN** an authenticated user views a candidature with status pending and no analyse
- **THEN** the system displays the candidate name, status badge "Analyse en cours", and a message indicating analysis is in progress

### Requirement: Candidature deletion
The system SHALL allow the offer owner to delete a candidature with cascade removal of its analyse and conversation.

#### Scenario: Successful deletion
- **WHEN** the offer owner confirms deletion of a candidature
- **THEN** the system deletes the candidature, its analyse, and its conversation, then redirects to the offre show page

### Requirement: Candidature authorization
The system SHALL enforce that users can only submit, view, or delete candidatures on offers they own.

#### Scenario: Submitting candidature on another user's offer returns 403
- **WHEN** an authenticated user submits a candidature against an offer where offre.user_id !== auth()->id()
- **THEN** the system returns a 403 Forbidden response

#### Scenario: Viewing candidature on another user's offer returns 403
- **WHEN** an authenticated user visits a candidature detail where the offre belongs to another user
- **THEN** the system returns a 403 Forbidden response

#### Scenario: Deleting candidature on another user's offer returns 403
- **WHEN** an authenticated user attempts to delete a candidature where the offre belongs to another user
- **THEN** the system returns a 403 Forbidden response

### Requirement: Status badges
The system SHALL display colored status badges for candidatures based on their status and recommendation.

#### Scenario: Pending status badge
- **WHEN** a candidature has status pending and no analyse
- **THEN** the system displays a gray "Analyse en cours" badge

#### Scenario: Completed with convoquer recommendation
- **WHEN** a candidature has status completed and recommandation convoquer
- **THEN** the system displays a green "À convoquer" badge with the matching score

#### Scenario: Completed with attente recommendation
- **WHEN** a candidature has status completed and recommandation attente
- **THEN** the system displays an orange "En attente" badge with the matching score

#### Scenario: Completed with rejeter recommendation
- **WHEN** a candidature has status completed and recommandation rejeter
- **THEN** the system displays a red "À rejeter" badge with the matching score

#### Scenario: Failed status badge
- **WHEN** a candidature has status failed
- **THEN** the system displays a dark red "Analyse échouée" badge
