# Agent Conversationnel

## Purpose

Provide a conversational chat interface for RH agents to interact with the AI assistant about specific candidatures. The agent uses tools to retrieve factual data and maintains conversation context across sessions.

## Requirements

### Requirement: Chat interface for candidatures
The system SHALL provide a chat interface for conversing with the AI agent about a specific candidature.

#### Scenario: Chat page displays for completed candidature
- **WHEN** an authenticated offer owner visits the chat URL for a candidature with status `completed`
- **THEN** the system displays the chat view with message history, input form, and candidate info

#### Scenario: Chat page supports comparison context
- **WHEN** the chat URL includes a `compare` query parameter with a valid candidature ID
- **THEN** the system pre-fills the message input with "Compare ce candidat avec le candidat #{id}" (visible to user for editing before submission)

#### Scenario: Chat access for non-completed candidature returns 422
- **WHEN** an authenticated offer owner visits the chat URL for a candidature with status other than `completed`
- **THEN** the system returns a 422 error indicating analysis must be completed first

#### Scenario: Chat access by non-owner returns 403
- **WHEN** an authenticated user visits the chat URL for a candidature belonging to another user's offer
- **THEN** the system returns a 403 Forbidden response

### Requirement: Message submission
The system SHALL accept user messages and return agent responses with tool-backed answers.

#### Scenario: User sends message and receives response
- **WHEN** a user submits a message via the chat form
- **THEN** the system persists the user message, calls the agent (which may call tools), persists the agent response, and redirects back to the chat view

#### Scenario: Invalid message returns validation error
- **WHEN** a user submits an empty message or message longer than 2000 characters
- **THEN** the system returns the form with validation errors

### Requirement: Agent uses tools for factual answers
The system SHALL ensure the agent calls appropriate tools before answering factual questions.

#### Scenario: Score question triggers tool call
- **WHEN** a user asks "Pourquoi ce score ?" or similar question about the analysis
- **THEN** the agent calls `getCandidateAnalysis` tool before responding (visible in logs)

#### Scenario: Job criteria question triggers tool call
- **WHEN** a user asks about job requirements or competences
- **THEN** the agent calls `getJobRequirements` tool before responding

### Requirement: Conversation history persistence
The system SHALL persist all messages and maintain context across page reloads.

#### Scenario: Messages persist across sessions
- **WHEN** a user sends multiple messages in a conversation
- **THEN** all messages are stored in the database and displayed when the chat page is reloaded

#### Scenario: Agent uses conversation history
- **WHEN** a user asks a follow-up question
- **THEN** the agent receives previous messages as context and can reference earlier exchanges

### Requirement: Quick suggestions
The system SHALL provide optional quick suggestion buttons for common questions.

#### Scenario: Quick suggestions displayed
- **WHEN** the chat page loads
- **THEN** the system displays optional suggestion buttons like "Pourquoi ce score ?", "Questions d'entretien ?", "Points faibles ?"
