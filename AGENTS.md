# AGENTS.md — TalentMatch

Ce fichier définit comment les agents de code (Laravel Boost / MCP, Copilot, Claude Code,
etc.) doivent travailler sur ce dépôt. Il est lu par les outils IA avant toute génération
de code et complète les specs dans `openspec/specs/`.

## 1. Contexte projet

TalentMatch est une application Laravel de présélection RH. Deux couches IA :

1. **Structured output** (`laravel/ai`) : l'analyse d'un CV contre une offre retourne un
   JSON typé strict (voir `openspec/specs/04-analyse-ia.md`).
2. **Agent conversationnel** avec **tools** Laravel et **mémoire de conversation**
   persistée (voir `openspec/specs/05-tools-laravel-ai.md`,
   `06-conversation-memory.md`, `07-agent-conversationnel.md`).

## 2. Stack & environnement

- Laravel 13, PHP 8.3, MySQL 8, Docker.
- **Toutes les commandes Laravel/Composer s'exécutent dans le conteneur `app`** :
  ```bash
  docker compose exec app php artisan ...
  docker compose exec app composer ...
  docker compose exec app php artisan queue:work
  ```
- Ne jamais exécuter `php artisan` ou `composer` directement sur la machine hôte.
- Laravel Boost est installé comme dev dependency et tourne en MCP server local — il
  fournit le schéma DB, les routes et les versions de packages à l'agent. Toujours
  l'interroger avant de générer du code touchant au schéma ou aux routes.

## 3. Workflow obligatoire — specs avant code

- Aucune ligne de code métier n'est écrite sans spec validée dans `openspec/specs/`.
- Cycle : `opsx propose` → `opsx plan` → `opsx tasks` → `opsx implement` (voir
  `docs/workflow-commands.md`).
- `openspec/taskboard.md` est la source de vérité pour les priorités et dépendances
  entre tâches.

## 4. Conventions de code

- PSR-12, types stricts (`declare(strict_types=1)` recommandé sur les classes
  service/job/tool).
- Toute création/modification de ressource passe par un **Form Request** dédié — jamais
  de `$request->all()` non validé.
- Tout champ JSON/array ou enum stocké en base **doit** avoir un cast Eloquent explicite
  (`$casts`).
- Pas de logique métier dans les contrôleurs au-delà de l'orchestration (validation →
  service/job → réponse).
- Eager loading systématique (`with()`, `withCount()`) — zéro requête N+1, vérifié avec
  Laravel Debugbar.

## 5. Règles spécifiques IA — ce que l'agent NE DOIT JAMAIS faire

- ❌ Ne **jamais** calculer le `matching_score` ou la `recommandation` via des règles
  `if/else` ou des regex. Le score est **toujours** produit par `laravel/ai` via
  structured output, contraint par le schéma défini dans
  `openspec/specs/04-analyse-ia.md`.
- ❌ Ne **jamais** exécuter l'appel IA d'analyse de façon synchrone dans une requête
  HTTP. L'analyse passe **toujours** par `AnalyseCandidatJob` sur une queue.
- ❌ L'agent conversationnel ne doit **jamais** répondre à une question factuelle
  (score, lacunes, critères d'offre, comparaison) sans appeler le tool correspondant
  (`getCandidateAnalysis`, `getJobRequirements`, `compareCandidates`). Une réponse
  factuelle sans appel de tool = hallucination = bug.
- ❌ Aucun tool ne doit retourner de données appartenant à un autre utilisateur que celui
  authentifié. Chaque tool vérifie `user_id` avant de retourner une ressource.
- ❌ Ne jamais désactiver ou contourner la validation du schéma JSON de structured
  output, même en cas d'erreur. En cas de réponse hors schéma : retry (max 3) puis
  statut `failed` + log — jamais d'insertion de données invalides en base.
- ❌ Ne jamais committer `.env`, clés API, ou tokens.
- ❌ Ne jamais modifier une migration déjà jouée — créer une nouvelle migration.

## 6. Mémoire de conversation

- Les échanges agent ↔ utilisateur sont persistés via les tables de mémoire fournies par
  le SDK `laravel/ai` (voir `openspec/specs/06-conversation-memory.md`).
- Une conversation est rattachée à une candidature/analyse précise — ne jamais partager
  le contexte de mémoire entre deux candidats ou deux utilisateurs différents.

## 7. Commits

- Commits quotidiens obligatoires, Conventional Commits + mention `[AI-assisted]` quand
  le code a été généré ou significativement assisté par un agent IA (voir
  `docs/commit-strategy.md`).
- Toujours préciser dans le message ou la description ce qui a été corrigé/modifié
  manuellement après génération, et pourquoi.

## 8. Branches

- `main` ← `develop` ← `feature/*`. Voir `docs/branching-strategy.md` pour le détail par
  branche et l'ordre de merge recommandé.

## 9. Cas limites à toujours gérer

- CV vide ou trop court → rejeté à la validation (Form Request), jamais envoyé à l'IA.
- Offre sans compétences requises → l'analyse reste possible ; le scoring se base alors
  sur la description du poste et l'expérience du candidat (le prompt l'indique
  explicitement).
- Réponse IA hors schéma → retry automatique puis échec propre (`status = failed`),
  jamais de crash ni de données corrompues en base.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/ai (AI) - v0
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
