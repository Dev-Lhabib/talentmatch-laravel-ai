# Spec 06 — Mémoire de Conversation

## User Story

US10 — Mémoire de conversation.

## Objectif

Persister les échanges entre l'agent RH et l'assistant IA, rattachés à une candidature
précise, afin que l'agent puisse poser des questions de suivi sans répéter le contexte à
chaque message.

## Dans le périmètre

- Utilisation des tables de mémoire fournies par le SDK `laravel/ai` (publication des
  migrations du SDK).
- Extension de la table `ai_conversations` pour y ajouter `candidature_id` (colonne
  custom).
- Une `Conversation` est créée à la première question sur une candidature `completed` ;
  les échanges suivants réutilisent la même conversation.
- Chaque message (user + assistant) est persisité dans `ai_messages`.
- Le contexte (messages précédents) est injecté dans chaque appel suivant à l'agent.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- **Jamais** partager une conversation entre deux candidatures ou deux utilisateurs
  différents — le scoping est double (`candidature_id` **et** `user_id`).
- **Jamais** injecter dans la conversation le CV brut en entier à chaque tour — le
  contexte est l'historique des messages et les données renvoyées par les tools, pas une
  copie du CV.
- **Jamais** tronquer ou supprimer des messages d'historique de façon silencieuse — si
  la fenêtre de contexte est trop large, stratégie explicite (résumé ou limite de
  messages récents).
- La mémoire n'est pas un espace de stockage général — elle ne contient que des échanges
  liés à une analyse de candidature.

## Tables de Mémoire (SDK `laravel/ai`)

Publier les migrations du SDK :

```bash
docker compose exec app php artisan vendor:publish --tag=ai-migrations
docker compose exec app php artisan migrate
```

Structure attendue (à adapter selon la version exacte du SDK) :

### `ai_conversations`

| Colonne           | Type         | Notes                                        |
|-------------------|--------------|----------------------------------------------|
| `id`              | bigint PK    |                                              |
| `candidature_id`  | bigint FK    | **Colonne custom ajoutée** → `candidatures.id`, nullable, unique (1 conversation par candidature) |
| `user_id`         | bigint FK    | Pour scoping — peut être géré par le SDK ou ajouté manuellement |
| `model`           | string       | Modèle IA utilisé (ex. `claude-sonnet-4-6`)  |
| `system_prompt`   | text         | System prompt de l'agent (optionnel selon SDK)|
| timestamps        |              |                                              |

### `ai_messages`

| Colonne           | Type         | Notes                                         |
|-------------------|--------------|-----------------------------------------------|
| `id`              | bigint PK    |                                               |
| `conversation_id` | bigint FK    | → `ai_conversations.id`                       |
| `role`            | string       | `user` \| `assistant` \| `tool`               |
| `content`         | text/json    | Contenu du message ou résultat du tool        |
| `tool_calls`      | json         | Nullable — appels de tools effectués          |
| timestamps        |              |                                               |

## Migration custom — ajout `candidature_id`

Si le SDK ne prévoit pas ce champ, créer une migration séparée :

```php
// database/migrations/xxxx_add_candidature_id_to_ai_conversations.php
Schema::table('ai_conversations', function (Blueprint $table) {
    $table->foreignId('candidature_id')
          ->nullable()
          ->unique()   // 1 conversation par candidature
          ->constrained('candidatures')
          ->cascadeOnDelete();
});
```

## Logique de résolution de conversation

Dans `ChatController` (spec 07) :

```php
private function resolveConversation(Candidature $candidature): AiConversation
{
    return AiConversation::firstOrCreate(
        ['candidature_id' => $candidature->id],
        [
            'user_id' => auth()->id(),
            'model'   => config('ai.default_model'),
        ]
    );
}
```

La conversation est ensuite passée à l'agent pour que celui-ci charge l'historique des
messages et maintienne le contexte.

## Modèle `Candidature` — relation conversation

```php
public function conversation(): HasOne
{
    return $this->hasOne(AiConversation::class, 'candidature_id');
}
```

## Stratégie de contexte

À chaque appel à l'agent, l'historique des messages de la conversation est chargé et
inclus dans le prompt (via le SDK). Limite recommandée : 20 derniers messages ou ~8 000
tokens — à ajuster selon les contraintes du modèle utilisé.

## Pourquoi la mémoire de conversation plutôt qu'un chat stateless ?

> Sans mémoire, chaque question repart de zéro : l'utilisateur devrait répéter « Je parle
> du candidat Dupont pour le poste de Lead Dev, son score était 73 » à chaque tour. Avec
> la mémoire, l'agent retient le contexte : « son niveau d'études ? » est compréhensible
> sans répétition. La persistance en base (et non en session) permet de reprendre une
> conversation après déconnexion ou rechargement de page.

## Critères d'acceptation

- [ ] La première question sur une candidature `completed` crée une `AiConversation`
  rattachée à `candidature_id`.
- [ ] Les questions suivantes sur le même candidat réutilisent la même conversation (pas
  de nouvelle ligne dans `ai_conversations`).
- [ ] Chaque message (user + assistant) est bien persisté dans `ai_messages`.
- [ ] Question de suivi sans contexte répété (ex. « et ses langues ? ») → réponse
  correcte grâce à l'historique.
- [ ] Accéder à la conversation d'un autre utilisateur via `candidature_id` → 403.
- [ ] Supprimer une candidature → suppression en cascade de la conversation et des
  messages.

## Dépendances

- Requiert : spec 03 (candidatures), SDK `laravel/ai` publié (T0.3).
- Requis par : spec 07 (agent conversationnel — utilise la conversation pour maintenir
  le contexte).

## Branche Git

`feature/conversation-memory` → `develop`

## Workflow OpenSpec

```bash
opsx propose conversation-memory
opsx apply conversation-memory
opsx sync conversation-memory
opsx archive conversation-memory
```
