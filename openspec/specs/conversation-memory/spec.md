# Conversation Memory

## Purpose

Persist conversations between the RH agent and AI assistant, linked to specific candidatures, so the agent can ask follow-up questions without repeating context at every message.

## Requirements

### Requirement: Conversation creation on first question
The system SHALL create a conversation linked to a candidature when the first question is asked about a completed candidature.

#### Scenario: First question creates conversation
- **WHEN** an authenticated user asks a question about a candidature with status `completed` and no existing conversation
- **THEN** the system creates an `AiConversation` record with `candidature_id` and `user_id` fields

#### Scenario: First question on non-completed candidature returns error
- **WHEN** an authenticated user asks a question about a candidature with status other than `completed`
- **THEN** the system returns an error message indicating analysis must be completed first

### Requirement: Conversation reuse for follow-up questions
The system SHALL reuse existing conversations for subsequent questions on the same candidature.

#### Scenario: Follow-up question reuses conversation
- **WHEN** an authenticated user asks a follow-up question about a candidature that already has a conversation
- **THEN** the system reuses the existing conversation (no new `AiConversation` record created)

### Requirement: Message persistence
The system SHALL persist all messages (user and assistant) in the `ai_messages` table.

#### Scenario: User message is persisted
- **WHEN** a user sends a message in a conversation
- **THEN** the system creates an `AiMessage` record with role `user` and the message content

#### Scenario: Assistant response is persisted
- **WHEN** the agent responds to a user message
- **THEN** the system creates an `AiMessage` record with role `assistant` and the response content

### Requirement: Conversation context injection
The system SHALL load previous messages and include them in the agent's context for each call.

#### Scenario: Agent receives conversation history
- **WHEN** the agent is called for a follow-up question
- **THEN** the system loads the last 20 messages from the conversation and includes them in the prompt

### Requirement: Conversation authorization
The system SHALL enforce that users can only access conversations for candidatures they own.

#### Scenario: Accessing another user's conversation returns 403
- **WHEN** an authenticated user tries to access a conversation for a candidature belonging to another user
- **THEN** the system returns a 403 Forbidden response

### Requirement: Cascade deletion
The system SHALL delete conversations and messages when the associated candidature is deleted.

#### Scenario: Deleting candidature deletes conversation
- **WHEN** a candidature with an associated conversation is deleted
- **THEN** the system deletes the conversation and all its messages in cascade
