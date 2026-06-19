# Comparison Flow

## Purpose

One-click comparison flow that auto-generates a structured side-by-side result page from two candidate analyses.

## Requirements

### Requirement: Comparison creation endpoint

The system SHALL have a `POST /comparisons/create` route that accepts two `application_id` values.

The endpoint SHALL:
1. Load both `Application` models with `candidate`, `offre`, and `analyse` relationships
2. Authorize that both applications belong to the authenticated user (via `offre.user_id`)
3. Verify both applications have `status = completed` and have analyses
4. Build an AI prompt with full candidate data
5. Call the LLM synchronously
6. Parse the JSON response
7. Store the result in the `comparisons` table
8. Redirect to `/comparisons/{id}`

#### Scenario: Valid comparison is created

- **WHEN** two valid application IDs are posted
- **THEN** a Comparison record is created
- **AND** the user is redirected to the comparison result page

#### Scenario: Invalid application IDs return error

- **WHEN** one or both application IDs are invalid or unauthorized
- **THEN** the system SHALL return a 403 or 404 error

#### Scenario: Applications from different offres return error

- **WHEN** the two application IDs belong to different offres
- **THEN** the system SHALL return a controlled error

### Requirement: AI prompt and response parsing

The system SHALL build the following prompt and send it to the LLM:

```
Compare these two candidates for the role: {offre title}
Required skills: {required_skills}

Candidate 1: {name}, Score: {score}
CV: {cv_text}
Competences: {competences}
Strengths: {points_forts}
Gaps: {lacunes}

Candidate 2: {name}, Score: {score}
CV: {cv_text}
Competences: {competences}
Strengths: {points_forts}
Gaps: {lacunes}

Return ONLY valid JSON:
{
  "candidate1_verdict": string,
  "candidate2_verdict": string,
  "winner_id": integer,
  "winner_reason": string
}
```

The system SHALL:
- Use the same LLM provider as the existing chat agent (`laravel/ai` SDK)
- Validate the JSON response contains all required fields
- Fall back to an error message if JSON parsing fails

#### Scenario: LLM returns valid JSON

- **WHEN** the LLM responds with valid JSON
- **THEN** the response SHALL be parsed and stored in the comparison record

#### Scenario: LLM returns invalid response

- **WHEN** the LLM responds with invalid or non-JSON content
- **THEN** the comparison SHALL still be created with fallback error verdicts

### Requirement: Comparison result page

The system SHALL render a comparison result page at `GET /comparisons/{id}` with:

**Header:**
- Offre title centered
- "Analyse comparative IA" subtitle

**Two-column layout** (winner column highlighted with teal border/glow, loser dimmed):
- Medal emoji (🥇 winner, 🥈 loser) + rank badge
- Circular avatar with first letter of candidate name
- Candidate name (bold, large)
- Matching score ring (reuse existing donut component if available, else text score)
- Recommendation badge (À convoquer / En attente / Rejeté)
- **Compétences section**: For each `required_skill` of the offre: ✅ if candidate has it, ❌ if missing. Extra skills shown as gray badges
- **Points Forts** bullet list
- **Lacunes** bullet list
- **AI Verdict box** (dark card, teal border): 💬 {candidate1_verdict or candidate2_verdict}

**Footer:**
- Winner banner (full width, green background): 🏆 Recommandation IA : {winner name} — {winner_reason}
- Two action buttons: [✅ Convoquer {winner name}] (updates application status), [Retour aux candidatures] (back to /offres/{id})

#### Scenario: Comparison page renders correctly

- **WHEN** a user visits `/comparisons/{id}`
- **THEN** the page SHALL display both candidates in two columns
- **AND** the winner column SHALL have a teal highlight
- **AND** required skills SHALL show ✅/❌ per candidate
- **AND** the winner banner SHALL appear at the bottom

### Requirement: Processing overlay

When the user clicks "Comparer ces 2 candidats" on the offre page, the system SHALL show a full-page processing overlay using Alpine.js:
- Animated pulsing logo or spinner
- Text: "🔄 Comparaison en cours..."
- Subtext: "L'IA analyse les deux profils for {offre title}"
- The form POST proceeds behind the overlay
- No user interaction needed after clicking

#### Scenario: Processing overlay shows on submit

- **WHEN** the user clicks "Comparer ces 2 candidats"
- **THEN** a full-page overlay SHALL appear immediately
- **AND** the form SHALL submit to POST /comparisons/create

### Requirement: Update offre show page button

The existing "Comparer ces 2 candidats" button on `/offres/{id}` SHALL:
- Keep the existing checkbox selection logic (Alpine.js)
- POST to `/comparisons/create` with the two `application_id` values
- Show loading spinner via Alpine.js overlay while processing
- No longer redirect to the chat page

#### Scenario: Compare button posts to new endpoint

- **WHEN** exactly 2 candidates are selected on the offre page
- **AND** the user clicks "Comparer ces 2 candidats"
- **THEN** a POST request SHALL be sent to `/comparisons/create`
- **AND** the processing overlay SHALL appear

### Requirement: Keep existing chat for individual questions

The existing chat interface (`/offres/{offre}/candidatures/{app}/chat` and the chat panel in `/applications/{id}`) SHALL remain unchanged for asking individual questions about a single candidate (Pourquoi ce score?, Questions d'entretien?, Points faibles?).

The chat SHALL NOT be part of the comparison flow.

#### Scenario: Chat still works for individual candidates

- **WHEN** a user visits the chat page for a single candidate
- **THEN** the chat SHALL function as before
- **AND** no comparison-related changes SHALL affect it
