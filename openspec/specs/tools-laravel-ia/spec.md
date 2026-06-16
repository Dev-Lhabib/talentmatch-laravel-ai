# Tools Laravel AI

## Purpose

Read-only tools that allow the conversational agent to query real data from the database, preventing hallucination and ensuring every factual response is backed by actual data.

## Requirements

### Requirement: getCandidateAnalysis tool
The system SHALL provide a tool that retrieves complete analysis data for a specific candidature, enforcing user ownership.

#### Scenario: Valid candidature returns analysis
- **WHEN** the agent calls `getCandidateAnalysis` with a valid candidature ID owned by the authenticated user
- **THEN** the tool returns an array with candidat name, offre title, matching_score, recommandation, justification, points_forts, lacunes, competences_manquantes, competences_extraites, annees_experience, niveau_etudes, langues

#### Scenario: Unauthorized candidature returns error
- **WHEN** the agent calls `getCandidateAnalysis` with a candidature ID belonging to another user
- **THEN** the tool returns a string error message "Candidature introuvable ou accès non autorisé."

#### Scenario: Candidature without analysis returns status message
- **WHEN** the agent calls `getCandidateAnalysis` with a valid candidature ID that has no analysis yet
- **THEN** the tool returns a string message indicating analysis is not available with current status

### Requirement: getJobRequirements tool
The system SHALL provide a tool that retrieves job offer criteria, enforcing user ownership.

#### Scenario: Valid offre returns requirements
- **WHEN** the agent calls `getJobRequirements` with a valid offre ID owned by the authenticated user
- **THEN** the tool returns an array with titre, description, competences_requises, niveau_experience_min

#### Scenario: Unauthorized offre returns error
- **WHEN** the agent calls `getJobRequirements` with an offre ID belonging to another user
- **THEN** the tool returns a string error message "Offre introuvable ou accès non autorisé."

### Requirement: compareCandidates tool
The system SHALL provide a tool that compares two candidatures on the same offer, enforcing user ownership and same-offer constraint.

#### Scenario: Two valid candidatures on same offer return comparison
- **WHEN** the agent calls `compareCandidates` with two valid candidature IDs owned by the authenticated user on the same offer
- **THEN** the tool returns an array with offre title and both candidatures' analysis data side by side

#### Scenario: Different offers returns error
- **WHEN** the agent calls `compareCandidates` with two candidatures on different offers
- **THEN** the tool returns a string error message "Les deux candidatures doivent appartenir à la même offre d'emploi."

#### Scenario: Missing candidature returns error
- **WHEN** the agent calls `compareCandidates` with one or both candidature IDs invalid or unauthorized
- **THEN** the tool returns a string error message "Une ou plusieurs candidatures sont introuvables ou non autorisées."

#### Scenario: One analysis missing returns error
- **WHEN** the agent calls `compareCandidates` with two valid candidatures but one has no analysis
- **THEN** the tool returns a string error message "L'une des deux analyses n'est pas encore disponible."

### Requirement: Tool authorization
The system SHALL enforce that every tool query includes user_id scoping to prevent cross-user data access.

#### Scenario: All queries are user-scoped
- **WHEN** any tool executes a database query
- **THEN** the query includes `where('user_id', auth()->id())` condition

### Requirement: Tools are read-only
The system SHALL ensure that tools never perform database write operations.

#### Scenario: No write queries in tools
- **WHEN** any tool is called
- **THEN** the tool only executes SELECT queries (no INSERT, UPDATE, DELETE)
