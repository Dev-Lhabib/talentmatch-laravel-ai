# Application Detail View

## Purpose

Display a full-screen analysis page for an Application at `GET /applications/{application}` showing candidate profile, matching data, analysis results, and an AI assistant chat panel.

## Requirements

### Requirement: Application detail view
The system SHALL display a full-screen analysis page for an Application at `GET /applications/{application}` showing candidate profile, matching data, and analysis results.

#### Scenario: View application detail with full analysis
- **WHEN** an authenticated user navigates to `/applications/{application}`
- **THEN** the page displays the candidate's name, first paragraph of CV text as bio, and an avatar placeholder
- **AND** the matching score from `analyses.matching_score` rendered as a visual ring/progress indicator
- **AND** competences badges from `analyses.competences_extraites` (JSON array)
- **AND** languages badges from `analyses.langues` (JSON array)
- **AND** points forts list from `analyses.points_forts` (JSON array)
- **AND** lacunes list from `analyses.lacunes` (JSON array)
- **AND** recommendation banner from `analyses.recommandation` with color-coded styling

#### Scenario: Failed analysis state
- **WHEN** `application.status` is `failed`
- **THEN** the page displays "⚠️ Analyse échouée" with a "Réessayer" button
- **AND** clicking the button dispatches `AnalyseCandidatJob` again for this application

#### Scenario: Analysing state
- **WHEN** `application.status` is `analysing`
- **THEN** the page displays a loading/spinner indicator

#### Scenario: Pending state
- **WHEN** `application.status` is `pending`
- **THEN** the page displays "Pas encore analysé" message
