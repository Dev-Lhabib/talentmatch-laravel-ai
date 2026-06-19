# Offers CRUD

## Purpose

Full CRUD management for job offers — two parallel entities:

- **Offre** (legacy, French): `offres` table with `titre`, many-to-many competences via pivot, candidature integration with analysis scores.
- **Offer** (new, English): `offers` table with `title`, inline `required_skills` JSON, status tracking (`open`/`closed`/`draft`), independent of the candidate system.

## Requirements

### Requirement: Offer creation
The system SHALL allow authenticated users to create job offers with a title, description, optional competences, and optional minimum experience.

#### Scenario: Successful offer creation
- **WHEN** an authenticated user submits the offer form with valid data (titre, description, optional competences, optional experience_min)
- **THEN** the system creates the offer linked to the authenticated user and redirects to the offer show page

#### Scenario: Validation rejects invalid input
- **WHEN** an authenticated user submits the offer form with an empty title or description shorter than 20 characters
- **THEN** the system returns the form with validation errors and does not create the offer

#### Scenario: Competences synced to pivot
- **WHEN** an authenticated user submits competences with the offer
- **THEN** the system syncs the competences to the `offre_competence` pivot table

### Requirement: Offer listing
The system SHALL display a paginated list of the authenticated user's offers with application counts.

#### Scenario: Paginated offer index
- **WHEN** an authenticated user visits GET /offres
- **THEN** the system returns a paginated list (15 per page) of offers belonging to that user, with `applications_count` eager loaded

#### Scenario: Empty state
- **WHEN** an authenticated user with no offers visits GET /offres
- **THEN** the system displays an empty state message

### Requirement: Offer detail view
The system SHALL display offer details including applications with analysis scores.

#### Scenario: Offer show with applications
- **WHEN** an authenticated user visits GET /offres/{offre}
- **THEN** the system returns the offer with its competences, and applications eager loaded with their candidate and analysis data (matching_score, recommandation)

#### Scenario: Applications displayed with score, status, and rank
- **WHEN** an authenticated offer owner views the offer detail page
- **THEN** the system displays applications sorted by score with their candidate name, scores, status badges, and rank indicators

#### Scenario: Pending application badge
- **WHEN** an application has no analysis yet
- **THEN** the system displays "En attente" instead of a score

#### Scenario: Candidate assignment dropdown
- **WHEN** an authenticated user visits GET /offres/{offre}
- **THEN** the page includes a dropdown listing all candidates and an "Analyser" button to create a new application

#### Scenario: Analyse all unassigned candidates
- **WHEN** an authenticated user clicks "Analyser tous les candidats"
- **THEN** the system creates applications for all candidates not yet linked to this offer and dispatches AnalyseCandidateJob for each

### Requirement: Offer update
The system SHALL allow the owner to edit and update their offers.

#### Scenario: Successful offer update
- **WHEN** the offer owner submits the edit form with valid data
- **THEN** the system updates the offer and redirects to the show page

#### Scenario: Competences re-synced on update
- **WHEN** the offer owner updates competences
- **THEN** the system syncs the new competences list to the pivot table

### Requirement: Offer deletion
The system SHALL allow the owner to delete their offers with cascade removal of applications and analyses.

#### Scenario: Successful offer deletion
- **WHEN** the offer owner confirms deletion
- **THEN** the system deletes the offer, its applications, analyses, and associated data, then redirects to the index

### Requirement: User authorization
The system SHALL enforce that users can only access, modify, or delete their own offers using policy-based authorization via the `AuthorizesRequests` trait.

#### Scenario: Accessing another user's offer returns 403
- **WHEN** an authenticated user visits GET /offres/{offre} where offre.user_id !== auth()->id()
- **THEN** the system returns a 403 Forbidden response

#### Scenario: Updating another user's offer returns 403
- **WHEN** an authenticated user submits PUT /offres/{offre} where offre.user_id !== auth()->id()
- **THEN** the system returns a 403 Forbidden response

#### Scenario: Deleting another user's offer returns 403
- **WHEN** an authenticated user submits DELETE /offres/{offre} where offre.user_id !== auth()->id()
- **THEN** the system returns a 403 Forbidden response

#### Scenario: Base controller supports authorization
- **WHEN** any controller extends the base `Controller` class
- **THEN** the `authorize()` method SHALL be available via the `AuthorizesRequests` trait

#### Scenario: Policies are explicitly registered
- **WHEN** the application boots
- **THEN** `OffrePolicy`, `OfferPolicy`, and `CandidaturePolicy` SHALL be registered in `AppServiceProvider::boot()` via `Gate::policy()`

### Requirement: Zero N+1 queries
The system SHALL use eager loading to prevent N+1 queries on offer listings and detail pages.

#### Scenario: Index eager loading
- **WHEN** the offer index page is rendered
- **THEN** the query uses `withCount('applications')` and Debugbar confirms no N+1 queries

#### Scenario: Show eager loading
- **WHEN** the offer detail page is rendered
- **THEN** the query uses `with('applications.candidate.analyse')` and Debugbar confirms no N+1 queries

### Requirement: Offer (English) creation
The system SHALL allow authenticated users to create job offers (English entity) with a title, description, optional minimum experience, optional required skills (as a JSON array of strings), and a status (default: open).

#### Scenario: Successful offer (English) creation
- **WHEN** an authenticated user submits the offer form with valid data (title, description, optional experience_min, optional required_skills)
- **THEN** the system creates the offer linked to the authenticated user and redirects to the offer show page

#### Scenario: Validation rejects empty title
- **WHEN** an authenticated user submits the offer form with an empty title
- **THEN** the system returns the form with a validation error on the title field and does not create the offer

#### Scenario: Validation rejects short description
- **WHEN** an authenticated user submits the offer form with a description shorter than 20 characters
- **THEN** the system returns the form with a validation error on the description field and does not create the offer

#### Scenario: Default status is open
- **WHEN** an authenticated user creates an offer without specifying a status
- **THEN** the system sets the offer status to `open`

### Requirement: Offer (English) listing
The system SHALL display a paginated list of the authenticated user's English offers with title, status badge, and candidate count.

#### Scenario: Paginated offer index
- **WHEN** an authenticated user visits GET /offers
- **THEN** the system returns a paginated list of offers belonging to that user

#### Scenario: Empty state
- **WHEN** an authenticated user with no offers visits GET /offers
- **THEN** the system displays an empty state message

### Requirement: Offer (English) detail view
The system SHALL display full offer details including title, description, required skills as badges, status badge, and experience minimum.

#### Scenario: Offer show with all details
- **WHEN** an authenticated user visits GET /offers/{offer}
- **THEN** the system returns the offer with its title, description, required_skills, status, and experience_min

#### Scenario: Required skills displayed as tags
- **WHEN** viewing an offer with at least one required skill
- **THEN** the system displays each skill as a rounded badge

#### Scenario: Empty skills section
- **WHEN** viewing an offer with no required skills
- **THEN** the system displays "Aucune compétence requise" or similar message

### Requirement: Offer (English) update
The system SHALL allow the owner to edit and update their English offers.

#### Scenario: Successful offer update
- **WHEN** the offer owner submits the edit form with valid data
- **THEN** the system updates the offer and redirects to the show page

#### Scenario: Status can be changed on update
- **WHEN** the offer owner changes the status from `open` to `closed`
- **THEN** the system persists the new status value

### Requirement: Offer (English) deletion
The system SHALL allow the owner to delete their English offers.

#### Scenario: Successful offer deletion
- **WHEN** the offer owner confirms deletion
- **THEN** the system deletes the offer and redirects to the index page

#### Scenario: Other users cannot delete
- **WHEN** a different authenticated user attempts to delete an offer they do not own
- **THEN** the system returns a 403 Forbidden response

### Requirement: Offer (English) user authorization
The system SHALL enforce that users can only access, modify, or delete their own English offers.

#### Scenario: Accessing another user's offer returns 403
- **WHEN** an authenticated user visits GET /offers/{offer} where offer.user_id !== auth()->id()
- **THEN** the system returns a 403 Forbidden response

#### Scenario: Updating another user's offer returns 403
- **WHEN** an authenticated user submits PUT /offers/{offer} where offer.user_id !== auth()->id()
- **THEN** the system returns a 403 Forbidden response

### Requirement: Required skills as JSON
The system SHALL store required skills as a JSON array on the `offers` table and cast them as an array on the Eloquent model.

#### Scenario: Skills round-trip as array
- **WHEN** an offer is created with required_skills = ["Laravel", "Vue.js"]
- **THEN** reading the offer returns the skills as a PHP array, not a string
