# Spec 01 — Authentification

## User Story

US1 — Inscription / Connexion / Déconnexion.

## Objectif

Permettre à un agent RH de créer un compte, se connecter et se déconnecter, afin que ses
offres et candidatures lui soient rattachées (`user_id`).

## Dans le périmètre

- Inscription (nom, email, mot de passe confirmé) avec hash bcrypt.
- Connexion / déconnexion.
- Middleware `auth` sur toutes les routes Offres / Candidatures / Agent.
- Toutes les requêtes Eloquent sur `Offre`/`Candidature` sont scopées à `auth()->id()`.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- Pas de connexion sociale (Google, etc.).
- Pas de rôles/permissions multiples — un seul type d'utilisateur « agent RH ».
- Pas de 2FA, pas de « mot de passe oublié » par SMS.
- Aucune route protégée, ni aucun tool de l'agent IA (spec 05), ne doit être accessible
  sans authentification.

## Exigences fonctionnelles

1. Formulaire d'inscription avec `RegisterRequest` (email unique, password min 8,
   confirmation).
2. Formulaire de connexion avec `LoginRequest`.
3. Déconnexion via POST (protection CSRF).
4. Redirection des invités vers `/login` pour toute route protégée.

## Impact sur le modèle de données

- Table `users` (par défaut Laravel) — voir spec 00.

## Critères d'acceptation

- [x] Un utilisateur peut s'inscrire avec email/mot de passe valides et est
  automatiquement connecté.
- [x] Un utilisateur peut se connecter avec des identifiants valides et est redirigé
  vers le dashboard des offres.
- [x] Un utilisateur déconnecté ne peut accéder à aucune offre/candidature/conversation.
- [x] Un utilisateur connecté ne voit que SES offres et candidatures (vérifié par test
  manuel avec deux comptes).

## Exigences structurées

### Requirement: Auth implementation completeness
The system SHALL implement all requirements defined in this spec as a first-class
feature, including registration, login, logout, middleware protection, and
user-scoped queries.

#### Scenario: Registration flow implemented
- **WHEN** a user submits the registration form with valid data (name, email, confirmed password)
- **THEN** the system creates a new user with bcrypt-hashed password and logs them in

#### Scenario: Login flow implemented
- **WHEN** a user submits the login form with valid credentials
- **THEN** the system authenticates them via the session guard and redirects to the dashboard

#### Scenario: Logout flow implemented
- **WHEN** an authenticated user submits a POST to `/logout`
- **THEN** the system destroys their session and redirects to `/login`

#### Scenario: Middleware protection applied
- **WHEN** an unauthenticated user attempts to access any protected route
- **THEN** the system redirects them to `/login`

#### Scenario: User scoping enforced
- **WHEN** an authenticated user queries `Offre` or `Candidature` records
- **THEN** the system returns only records where `user_id` matches `auth()->id()`

## Dépendances

- Requis par : toutes les autres specs (02 à 09).
- Dépend de : rien (point d'entrée du projet).

## Branche Git

`feature/authentication` → `develop`

## Workflow OpenSpec

```bash
opsx propose authentication
opsx apply authentication
opsx sync authentication
opsx archive authentication
```
