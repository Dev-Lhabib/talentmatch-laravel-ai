# Candidates CRUD

## Purpose

Allow recruiters to manage candidate profiles (name, email, phone, CV text) as first-class entities, independent of any job offer. This enables a candidate-centric view of the recruitment pipeline.

## Requirements

### Requirement: Candidate is a first-class entity

The system SHALL have a `candidates` table with the following columns:
- `id` — auto-increment primary key
- `name` — string (required, max 255)
- `email` — string (nullable, max 255)
- `phone` — string (nullable, max 50)
- `cv_text` — longText (required, min 50 characters)
- `created_at` / `updated_at` — timestamps

The system SHALL have an `App\Models\Candidate` Eloquent model with:
- `$fillable`: `name`, `email`, `phone`, `cv_text`
- A `hasMany('applications')` relationship stub (even if `Application` model/table does not exist yet)

#### Scenario: Candidate migration runs

- **WHEN** `php artisan migrate` is executed
- **THEN** a `candidates` table exists with all specified columns
- **AND** the existing `candidatures` table is NOT modified or dropped

#### Scenario: Candidate model exists

- **WHEN** a developer resolves `App\Models\Candidate` from the container
- **THEN** it extends `Illuminate\Database\Eloquent\Model`
- **AND** its `$fillable` array contains `name`, `email`, `phone`, `cv_text`

### Requirement: List candidates (index)

The system SHALL display a paginated list of all candidates at `GET /candidates`.
The list SHALL be searchable by candidate name.
Only authenticated users SHALL access this page.

#### Scenario: Visitors see candidates list

- **WHEN** an authenticated user navigates to `/candidates`
- **THEN** they see a page titled "Candidates"
- **AND** each candidate's name is displayed
- **AND** the list is paginated (15 per page)

#### Scenario: Search filters by name

- **WHEN** the user types a search term in the search bar and submits
- **THEN** only candidates whose name contains the search term are shown

#### Scenario: Unauthenticated access is rejected

- **WHEN** a guest navigates to `/candidates`
- **THEN** they are redirected to the login page

### Requirement: Create candidate

The system SHALL allow authenticated users to create a new candidate via `GET /candidates/create` (form) and `POST /candidates` (store).

Validation rules:
- `name`: required, string, max 255
- `email`: nullable, string, email, max 255
- `phone`: nullable, string, max 50
- `cv_text`: required, string, min 50

#### Scenario: Display create form

- **WHEN** an authenticated user navigates to `/candidates/create`
- **THEN** they see a form with fields for `name`, `email`, `phone`, `cv_text`

#### Scenario: Successful creation

- **WHEN** the user submits valid data
- **THEN** a new `Candidate` record is created in the database
- **AND** the user is redirected to the candidate's show page
- **AND** a success flash message is displayed

#### Scenario: Validation errors

- **WHEN** the user submits a `cv_text` shorter than 50 characters
- **THEN** the form is re-displayed with validation errors
- **AND** no candidate record is created

### Requirement: Show candidate profile

The system SHALL display a candidate's full profile at `GET /candidates/{candidate}`.

#### Scenario: View candidate profile

- **WHEN** an authenticated user navigates to `/candidates/1`
- **THEN** they see the candidate's name, email, phone, and CV text

### Requirement: Edit candidate

The system SHALL allow editing a candidate via `GET /candidates/{candidate}/edit` and `PUT /candidates/{candidate}`.

Validation rules SHALL be identical to creation.

#### Scenario: Display edit form

- **WHEN** an authenticated user navigates to `/candidates/1/edit`
- **THEN** the form is pre-filled with the candidate's current data

#### Scenario: Successful update

- **WHEN** the user modifies the name and submits
- **THEN** the candidate's name is updated in the database
- **AND** the user is redirected to the candidate's show page

### Requirement: Delete candidate

The system SHALL allow authenticated users to delete a candidate via `DELETE /candidates/{candidate}`.

#### Scenario: Successful deletion

- **WHEN** an authenticated user clicks "Delete" and confirms
- **THEN** the candidate record is removed from the database
- **AND** the user is redirected to the candidates list
- **AND** a success flash message is displayed

### Requirement: Navigation

The sidebar SHALL include a "Candidates" link pointing to `/candidates`.

#### Scenario: Sidebar shows Candidates link

- **WHEN** an authenticated user views any page with the sidebar
- **THEN** they see a "Candidates" navigation item
- **AND** clicking it navigates to `/candidates`
