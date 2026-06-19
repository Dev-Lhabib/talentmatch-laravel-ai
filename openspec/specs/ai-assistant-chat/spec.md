# AI Assistant Chat

## Purpose

Provide an interactive AI assistant chat panel within the application detail view, allowing recruiters to ask questions about a candidate's analysis without leaving the page.

## Requirements

### Requirement: Alpine.js chat UI
The system SHALL wire the AI assistant chat panel to the live endpoint without page reloads.

#### Scenario: Load existing conversation
- **WHEN** the application detail page loads
- **THEN** the chat panel fetches and displays existing messages with correct styling (user vs assistant avatar + bubble)

#### Scenario: Send message from UI
- **WHEN** a user types a message and clicks send
- **THEN** the Alpine.js component POSTs the message, shows a loading state, and appends the AI response inline
- **AND** conversation history persists when reopening the panel

#### Scenario: Chat unavailable before analysis
- **WHEN** application status is not `completed`
- **THEN** the chat panel displays a message: "Le chat sera disponible après l'analyse."
- **AND** the input and send button are disabled
