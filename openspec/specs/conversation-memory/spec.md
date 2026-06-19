# Conversation Memory

## Purpose

Persist conversations between the RH agent and AI assistant, linked to specific applications, so the agent can ask follow-up questions without repeating context at every message.

## Requirements

### Requirement: Conversation belongs to Application
The system SHALL store each conversation linked to an Application via `application_id` (unique FK), so that every application has exactly one conversation for the AI assistant chat.

#### Scenario: Conversation created per application
- **WHEN** an Application is created
- **THEN** the system creates a Conversation record with `application_id` set to the new application's id
- **AND** the Conversation model has a `belongsTo(Application::class)` relationship

#### Scenario: Messages stored with role
- **WHEN** a user or AI sends a message in the chat
- **THEN** the message is stored in the `messages` table with `conversation_id`, `role` (user/assistant), and `content`
- **AND** the Message model has a `belongsTo(Conversation::class)` relationship

#### Scenario: Follow-up question reuses conversation
- **WHEN** an authenticated user asks a follow-up question about an application that already has a conversation
- **THEN** the system reuses the existing conversation (no new Conversation record created)

### Requirement: Conversation context for LLM
The system SHALL build LLM prompts from the conversation's associated application context.

#### Scenario: Prompt includes full context
- **WHEN** the AI responds to a user message
- **THEN** the prompt includes: offer title, candidate name, full CV text, analysis data (score, competences, strengths, gaps), and the last 10 messages

### Requirement: Chat endpoint with context-aware LLM
The system SHALL provide `POST /applications/{application}/chat` that accepts a user message, builds a context-aware LLM prompt, returns the AI response as JSON.

#### Scenario: User sends a message
- **WHEN** a user POSTs `{ "message": "..." }` to `/applications/{application}/chat`
- **THEN** the system appends the user message to the messages table
- **AND** builds an LLM prompt containing: offer title, candidate name, CV text, analysis score/competences/strengths/gaps, and last 10 messages
- **AND** calls the AI model and saves the assistant response
- **AND** returns the assistant response as JSON

### Requirement: Conversation authorization
The system SHALL enforce that users can only access conversations for applications they own (via `application.offre.user_id`).

#### Scenario: Accessing another user's conversation returns 403
- **WHEN** an authenticated user tries to access a conversation for an application belonging to another user
- **THEN** the system returns a 403 Forbidden response

### Requirement: Cascade deletion
The system SHALL delete conversations and messages when the associated application is deleted.

#### Scenario: Deleting application deletes conversation
- **WHEN** an application with an associated conversation is deleted
- **THEN** the system deletes the conversation and all its messages in cascade
