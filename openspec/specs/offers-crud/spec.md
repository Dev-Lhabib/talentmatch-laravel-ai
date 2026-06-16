# Offers CRUD

## Purpose

Full CRUD management for job offers — create, list, view, edit, and delete offers with user-scoped authorization, competence management, and eager-loaded candidature data.

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
The system SHALL display a paginated list of the authenticated user's offers with candidature counts.

#### Scenario: Paginated offer index
- **WHEN** an authenticated user visits GET /offres
- **THEN** the system returns a paginated list (15 per page) of offers belonging to that user, with `candidatures_count` eager loaded

#### Scenario: Empty state
- **WHEN** an authenticated user with no offers visits GET /offres
- **THEN** the system displays an empty state message

### Requirement: Offer detail view
The system SHALL display offer details including candidatures with analysis scores.

#### Scenario: Offer show with candidatures
- **WHEN** an authenticated user visits GET /offres/{offre}
- **THEN** the system returns the offer with its competences, and candidatures eager loaded with their analyses (matching_score, recommandation)

#### Scenario: Pending candidature badge
- **WHEN** a candidature has no analysis yet
- **THEN** the system displays "En attente" instead of a score

### Requirement: Offer update
The system SHALL allow the owner to edit and update their offers.

#### Scenario: Successful offer update
- **WHEN** the offer owner submits the edit form with valid data
- **THEN** the system updates the offer and redirects to the show page

#### Scenario: Competences re-synced on update
- **WHEN** the offer owner updates competences
- **THEN** the system syncs the new competences list to the pivot table

### Requirement: Offer deletion
The system SHALL allow the owner to delete their offers with cascade removal of candidatures and analyses.

#### Scenario: Successful offer deletion
- **WHEN** the offer owner confirms deletion
- **THEN** the system deletes the offer, its candidatures, analyses, and associated data, then redirects to the index

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
- **THEN** `OffrePolicy` and `CandidaturePolicy` SHALL be registered in `AppServiceProvider::boot()` via `Gate::policy()`

### Requirement: Zero N+1 queries
The system SHALL use eager loading to prevent N+1 queries on offer listings and detail pages.

#### Scenario: Index eager loading
- **WHEN** the offer index page is rendered
- **THEN** the query uses `withCount('candidatures')` and Debugbar confirms no N+1 queries

#### Scenario: Show eager loading
- **WHEN** the offer detail page is rendered
- **THEN** the query uses `with('candidatures.analyse')` and Debugbar confirms no N+1 queries
