# Spec 02 — Gestion des Offres d'Emploi (CRUD)

## User Stories

- US2 — Créer une offre d'emploi
- US3 — Liste de mes offres
- US4 — Voir le détail d'une offre

## Objectif

Permettre à un agent RH de créer, consulter, modifier et supprimer ses offres d'emploi,
avec visibilité sur le nombre de candidats analysés et leur score.

## Dans le périmètre

- CRUD complet sur `offres` (create / read / update / delete).
- `competences_requises` est un champ JSON (tableau de chaînes) — peut être vide.
- `niveau_experience_min` est un entier (années), peut être 0.
- Liste paginée des offres avec compteur de candidatures via `withCount`.
- Détail d'une offre : critères + liste des candidatures avec score (ou badge
  « en attente »).
- `OffrePolicy` : un utilisateur ne peut lire/modifier/supprimer que SES offres.

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- Pas de publication publique d'offres — elles sont internes et privées.
- Pas de duplication/clonage d'offre dans cette spec (peut être ajouté ultérieurement).
- Pas de tri ou filtre avancé dans cette spec (le tri par score est dans spec 09).
- Le contrôleur ne doit **jamais** calculer ni estimer un score — il affiche ce que la
  table `analyses` contient.
- Aucune requête N+1 : la liste doit utiliser `withCount('candidatures')` et le détail
  `with('candidatures.analyse')`.

## Exigences fonctionnelles

### US2 — Créer une offre

```
POST /offres
```

Form Request : `OffreRequest`

```php
[
  'titre'                  => 'required|string|max:255',
  'description'            => 'required|string|min:20',
  'competences_requises'   => 'nullable|array',
  'competences_requises.*' => 'string|max:100',
  'niveau_experience_min'  => 'nullable|integer|min:0|max:50',
]
```

### US3 — Liste des offres

```
GET /offres
```

- Paginée (15 par page), scopée `user_id`, eager load `candidatures` count.
- Colonnes affichées : titre, date de création, nombre de candidats.

### US4 — Détail d'une offre

```
GET /offres/{offre}
```

- Critères : titre, description, compétences requises, niveau d'expérience min.
- Liste des candidatures : nom candidat, `matching_score` (ou « En attente »), badge
  `recommandation`.

### Modification / Suppression

```
GET  /offres/{offre}/edit
PUT  /offres/{offre}
DELETE /offres/{offre}
```

- Même Form Request `OffreRequest` pour la modification.
- Suppression : confirmation côté vue (modale ou page de confirmation).

## Modèle Eloquent `Offre`

```php
protected $fillable = [
    'user_id', 'titre', 'description',
    'competences_requises', 'niveau_experience_min',
];

protected $casts = [
    'competences_requises' => 'array',
];

// Relations
public function candidatures(): HasMany { ... }
public function user(): BelongsTo { ... }
```

## Routes

```php
Route::middleware('auth')->group(function () {
    Route::resource('offres', OffreController::class);
});
```

## Impact sur le modèle de données

- Table `offres` — voir spec 00.

## Critères d'acceptation

- [ ] Créer une offre avec ou sans `competences_requises` fonctionne.
- [ ] Le formulaire rejette un titre vide ou une description < 20 caractères.
- [ ] La liste affiche le bon compteur de candidatures (incluant celles en attente).
- [ ] Debugbar confirme 0 requête N+1 sur la liste et sur le détail.
- [ ] Accéder à `/offres/{id}` appartenant à un autre utilisateur retourne 403.
- [ ] Supprimer une offre retire ses candidatures et analyses associées de façon propre.

## Dépendances

- Requiert : spec 01 (authentification).
- Requis par : spec 03 (candidatures), spec 04 (analyse IA), spec 07 (agent).

## Branche Git

`feature/offres-crud` → `develop`

## Workflow OpenSpec

```bash
opsx propose offres-crud
opsx apply offres-crud
opsx sync offres-crud
opsx archive offres-crud
```
