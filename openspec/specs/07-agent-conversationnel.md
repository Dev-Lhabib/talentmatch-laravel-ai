# Spec 07 — Assistant Conversationnel (Agent)

## User Stories

- US9 — Poser une question sur un candidat
- US10 — Mémoire de conversation
- US11 — L'assistant appelle des tools

## Objectif

Permettre à un agent RH de converser en langage naturel avec un assistant IA contextualisé
sur une candidature précise. L'assistant répond uniquement sur la base des données réelles
retournées par ses tools (spec 05) et se souvient du contexte des échanges précédents
(spec 06).

## Dans le périmètre

- Interface de chat (saisie + historique) par candidature `completed`.
- `ChatController` : résolution/création de conversation, appel agent, persistance.
- System prompt de l'agent avec contexte initial (nom candidat, titre offre, ID
  candidature) et consignes anti-hallucination.
- L'agent dispose de `getCandidateAnalysis`, `getJobRequirements`, `compareCandidates`.
- Questions supportées (non exhaustif) :
  - « Pourquoi ce score ? »
  - « Quelles questions poser en entretien ? »
  - « Quelles sont ses compétences manquantes ? »
  - « Compare-le avec le candidat X » (si `compareCandidates` disponible)

## Hors périmètre / Ce que l'agent ne doit PAS faire

- **Jamais** inventer un score, des lacunes, des compétences ou une recommandation —
  toute donnée factuelle doit venir d'un tool.
- **Jamais** répondre à « quel est son score ? » sans avoir appelé `getCandidateAnalysis`
  au préalable (même si l'info figure dans un message précédent — si l'agent n'est pas
  sûr, il appelle le tool).
- **Jamais** accéder à la conversation ou aux données d'un autre utilisateur.
- L'agent ne peut **pas** modifier des données (créer, mettre à jour, supprimer) — les
  tools sont en lecture seule (voir spec 05).
- Le chat n'est accessible que pour une candidature dont `status = completed` — une
  candidature `pending`/`processing`/`failed` n'a pas de chat.

## System Prompt de l'Agent

```
Tu es TalentMatch Assistant, un assistant RH expert.
Tu aides les agents RH à comprendre et exploiter les analyses de candidats générées
automatiquement.

Contexte de cette session :
- Candidature : #{{ $candidature->id }} — {{ $candidature->nom_candidat }}
- Offre : {{ $candidature->offre->titre }}

Règles absolues :
1. Tu ne réponds JAMAIS à une question factuelle (score, lacunes, compétences,
   recommandation, critères du poste) sans avoir appelé le tool approprié.
2. Si tu ne sais pas avec certitude, appelle un tool — ne devine pas.
3. Tu ne peux pas modifier de données — tu es en lecture seule.
4. Tu ne parles que de cette candidature et de cette offre, sauf si on te demande
   explicitement de comparer avec une autre candidature (auquel cas tu appelles
   compareCandidates).
5. Tes réponses sont concises, professionnelles, et orientées vers l'aide à la décision
   RH.
```

## `ChatController`

```php
class ChatController extends Controller
{
    public function show(Offre $offre, Candidature $candidature): View
    {
        $this->authorizeAccess($offre, $candidature);
        $conversation = $this->resolveConversation($candidature);
        $messages     = $conversation->messages()->orderBy('created_at')->get();

        return view('chat.show', compact('offre', 'candidature', 'conversation', 'messages'));
    }

    public function store(
        ChatMessageRequest $request,
        Offre $offre,
        Candidature $candidature
    ): RedirectResponse {
        $this->authorizeAccess($offre, $candidature);
        $conversation = $this->resolveConversation($candidature);

        // Persister message user
        $conversation->messages()->create([
            'role'    => 'user',
            'content' => $request->validated('message'),
        ]);

        // Appel agent laravel/ai avec tools + historique
        $response = TalentMatchAgent::for($conversation)
            ->withTools([
                new GetCandidateAnalysisTool(),
                new GetJobRequirementsTool(),
                new CompareCandidatesTool(),
            ])
            ->ask($request->validated('message'));

        // Persister réponse assistant
        $conversation->messages()->create([
            'role'    => 'assistant',
            'content' => $response->text(),
        ]);

        return redirect()->route('chat.show', [$offre, $candidature])
            ->with('success', 'Réponse reçue.');
    }

    private function authorizeAccess(Offre $offre, Candidature $candidature): void
    {
        abort_if($offre->user_id !== auth()->id(), 403);
        abort_if($candidature->offre_id !== $offre->id, 403);
        abort_if($candidature->status !== StatutCandidatureEnum::Completed, 422,
            "L'analyse de cette candidature n'est pas encore terminée.");
    }

    private function resolveConversation(Candidature $candidature): AiConversation
    {
        return AiConversation::firstOrCreate(
            ['candidature_id' => $candidature->id],
            ['user_id' => auth()->id()]
        );
    }
}
```

## `ChatMessageRequest`

```php
public function rules(): array
{
    return [
        'message' => 'required|string|min:2|max:2000',
    ];
}
```

## Routes

```php
Route::middleware('auth')->group(function () {
    Route::get(
        '/offres/{offre}/candidatures/{candidature}/chat',
        [ChatController::class, 'show']
    )->name('chat.show');

    Route::post(
        '/offres/{offre}/candidatures/{candidature}/chat',
        [ChatController::class, 'store']
    )->name('chat.store');
});
```

## Vue Chat (`resources/views/chat/show.blade.php`)

Sections :
1. **En-tête** : nom candidat, offre, score + badge recommandation (rappel visuel).
2. **Historique des messages** : bulles user (droite) / assistant (gauche) avec timestamp.
3. **Saisie** : `<form>` POST + `<textarea>` + bouton Envoyer.
4. **Suggestions rapides** (optionnel) : boutons pré-remplis « Pourquoi ce score ? »,
   « Questions d'entretien ? », « Points faibles ? ».

## Exemple de scénario de conversation

```
RH : Pourquoi ce score de 67/100 ?
Agent : [appelle getCandidateAnalysis(42)]
        → Le score de 67 s'explique par des points forts solides en PHP et Laravel
          (4 ans d'expérience), mais des lacunes importantes sur Docker et les tests
          unitaires, qui sont des compétences requises pour ce poste.

RH : Quelles questions lui poser en entretien ?
Agent : [utilise le contexte déjà chargé — peut ne pas rappeler le tool]
        → 1. Comment gérez-vous les environnements de conteneurisation ?
          2. Quelle est votre expérience avec les tests unitaires / PHPUnit ?
          3. Parlez-moi d'un projet où vous avez dû monter en compétence rapidement…

RH : Et son niveau d'études ?
Agent : [utilise la mémoire — le champ était dans la réponse précédente du tool]
        → Master en Informatique (selon le CV analysé).
```

## Critères d'acceptation

- [ ] Le lien « Chat » est affiché sur la vue détail d'une candidature `completed`
  uniquement.
- [ ] Une candidature `pending`/`processing`/`failed` → accès au chat retourne 422.
- [ ] « Pourquoi ce score ? » → `getCandidateAnalysis` est appelé (visible en log)
  **avant** la réponse.
- [ ] Deux questions de suite sur le même candidat → le deuxième tour utilise l'historique
  persisté en base (pas de rechargement de contexte depuis zéro).
- [ ] Accéder au chat d'un candidat d'un autre utilisateur via URL directe → 403.
- [ ] La réponse de l'agent ne contient jamais de score/lacune inventé (vérifié en
  comparant avec `analyses` en base).

## Dépendances

- Requiert : spec 05 (tools), spec 06 (mémoire), spec 04 (candidature `completed`).
- Requis par : spec 08 (comparaison bonus — outil `compareCandidates` appelable depuis
  le chat).

## Branche Git

`feature/agent-conversationnel` → `develop`

## Workflow OpenSpec

```bash
opsx propose "Agent conversationnel : ChatController, system prompt anti-hallucination, tools + mémoire, vue chat"
opsx plan agent-conversationnel
opsx tasks agent-conversationnel
opsx implement agent-conversationnel
```
