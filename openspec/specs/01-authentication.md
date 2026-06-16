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

- [ ] Un utilisateur peut s'inscrire avec email/mot de passe valides et est
  automatiquement connecté.
- [ ] Un utilisateur peut se connecter avec des identifiants valides et est redirigé
  vers le dashboard des offres.
- [ ] Un utilisateur déconnecté ne peut accéder à aucune offre/candidature/conversation.
- [ ] Un utilisateur connecté ne voit que SES offres et candidatures (vérifié par test
  manuel avec deux comptes).

## Dépendances

- Requis par : toutes les autres specs (02 à 09).
- Dépend de : rien (point d'entrée du projet).

## Branche Git

`feature/authentication` → `develop`

## Workflow OpenSpec

```
opsx propose "Authentification : inscription, connexion, déconnexion, scoping user_id"
opsx plan authentication
opsx tasks authentication
opsx implement authentication
```
