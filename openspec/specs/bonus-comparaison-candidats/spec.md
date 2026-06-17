# Bonus: Comparaison de Candidats

## Purpose

Enable RH agents to compare two candidates side-by-side using the AI assistant's comparison tool, providing structured analysis of scores, strengths, weaknesses, and recommendations.

## Requirements

### Requirement: Candidature multi-selection for comparison
The system SHALL allow RH agents to select exactly two candidatures from the same offer for comparison.

#### Scenario: Checkboxes displayed for completed candidatures
- **WHEN** an authenticated offer owner views the offer detail page
- **THEN** each candidature with status `completed` has a checkbox for selection

#### Scenario: Non-completed candidatures cannot be selected
- **WHEN** a candidature has status other than `completed`
- **THEN** the checkbox is disabled with a tooltip indicating analysis must be completed

#### Scenario: Only two candidatures can be selected
- **WHEN** an RH agent selects a third candidature
- **THEN** the system prevents selection or deselects the oldest selection

#### Scenario: Compare button appears when exactly two are selected
- **WHEN** exactly two candidatures are selected
- **THEN** a "Comparer ces deux candidats" button becomes visible

### Requirement: Comparison redirect to chat
The system SHALL redirect to the chat interface with a pre-filled comparison prompt.

#### Scenario: Compare button redirects to chat
- **WHEN** an RH agent clicks the compare button with two candidatures selected
- **THEN** the system redirects to the chat of the first selected candidature with a `compare` query parameter containing the second candidature ID

#### Scenario: Chat pre-fills comparison message
- **WHEN** the chat page loads with a `compare` query parameter
- **THEN** the message input is pre-filled with "Compare ce candidat avec le candidat #{id2}"

#### Scenario: User confirms comparison message
- **WHEN** the user submits the pre-filled comparison message
- **THEN** the agent calls `compareCandidates` tool with both candidature IDs and returns a comparative analysis
