# Candidate Offer Assignment

## Purpose

Allow recruiters to assign candidates to job offers from both candidate profile and offer detail pages, with duplicate detection and bulk analysis support.

## Requirements

### Requirement: Assign candidate to offer from candidate profile
The system SHALL allow recruiters to assign a candidate to any offer from the candidate profile page using a dropdown selector.

#### Scenario: Successful assignment from candidate profile
- **WHEN** a recruiter is viewing a candidate profile at GET /candidates/{candidate}
- **AND** selects an offer from the dropdown and clicks "Analyser"
- **THEN** the system creates an Application linking the candidate to that offer, dispatches AnalyseCandidateJob for that application, and redirects to the offer show page

#### Scenario: Duplicate assignment prevented
- **WHEN** a recruiter attempts to assign a candidate to an offer they are already linked to
- **THEN** the system displays a warning message and does not create a duplicate Application

#### Scenario: Dropdown shows all offers
- **WHEN** the candidate profile page loads
- **THEN** the dropdown lists all offers from the offres table, sorted by creation date descending

### Requirement: Assign candidate to offer from offer detail page
The system SHALL allow recruiters to assign any candidate to the current offer from the offer detail page using a dropdown selector.

#### Scenario: Successful assignment from offer detail page
- **WHEN** a recruiter is viewing an offer at GET /offres/{offre}
- **AND** selects a candidate from the dropdown and clicks "Analyser"
- **THEN** the system creates an Application linking that candidate to the offer, dispatches AnalyseCandidateJob for that application, and redirects back to the offer show page

#### Scenario: Duplicate assignment prevented
- **WHEN** a recruiter attempts to assign a candidate already linked to this offer
- **THEN** the system displays a warning message and does not create a duplicate Application

#### Scenario: Dropdown shows all candidates
- **WHEN** the offer detail page loads
- **THEN** the dropdown lists all candidates from the candidates table, sorted by name

### Requirement: Analyse all unassigned candidates
The system SHALL provide a button on the offer detail page to dispatch analysis for all candidates not yet linked to the offer.

#### Scenario: Bulk analyse all candidates
- **WHEN** a recruiter clicks "Analyser tous les candidats"
- **THEN** the system creates Applications for each candidate not yet linked, dispatches AnalyseCandidateJob for each, and redirects back to the offer page
