# Spec 03 — Soumission et Suivi des Candidatures

## User Stories

- US5 — Soumettre un CV
- US8 — Recommandation typée (badge visible par offre et par candidat)

## Objectif

Permettre à un agent RH de coller le texte d'un CV et le nom du candidat pour lancer
une analyse IA en arrière-plan, tout en affichant immédiatement le statut de traitement.

## Dans le périmètre

- Formulaire de soumission d'un CV (texte brut) associé à une offre.
- Validation stricte (Form Request) avant toute création ou appel IA.
- Création d'une `Candidature` avec `status = pending`, puis dispatch du
  `AnalyseCandidatJob` (défini dans spec 04).
- Vue de suivi : statut mis à jour (pending → processing → completed/failed).
- Badge `recommandation` coloré visible sur la liste et le détail (spec 04 fournit la
  donnée, cette spec fournit l'affichage).

## Hors périmètre / Ce que l'implémentation ne doit PAS faire

- La soumission ne doit **jamais** appeler l'IA de façon synchrone — uniquement via le
  job en queue (voir spec 04).
- Le contrôleur ne doit **pas** calculer de score, estimer de compétences ni prendre de
  décision de recommandation — ces responsabilités appartiennent à l'agent IA (spec 04).
- Pas de téléchargement de fichier PDF/DOCX dans cette version — texte brut uniquement.
- Un CV de moins de 50 caractères ne doit **jamais** être persisté ni envoyé à l'IA —
  il est rejeté à la validation Form Request.
- Un agent RH ne peut pas soumettre un CV contre l'offre d'un autre utilisateur.

## Exigences fonctionnelles

### Soumission

```
POST /offres/{offre}/candidatures
```

Form Request : `SoumettreCandidatureRequest`

```php
[
    'nom_candidat' => 'required|string|max:255',
    'cv_text'      => 'required|string|min:50|max:50000',
]
```

Actions du contrôleur :

1. Autoriser via `OffrePolicy` (l'offre appartient à `auth()->id()`).
2. Créer `Candidature` :
   - `offre_id`, `user_id` (auth), `nom_candidat`, `cv_text`, `status = pending`.
3. Dispatcher `AnalyseCandidatJob::dispatch($candidature)` sur la queue `default`.
4. Rediriger vers la page de l'offre avec message flash « Analyse en cours… ».

### Liste des candidatures d'une offre

Affichée dans le détail d'une offre (spec 02, `GET /offres/{offre}`), chargée via
`with('candidatures.analyse')`.

Colonnes : nom candidat | statut | score (ou « — ») | badge recommandation | lien vers détail.

### Détail d'une candidature

```
GET /offres/{offre}/candidatures/{candidature}
```

Redirection vers la vue d'analyse (spec 04) ou affichage inline du statut « en attente ».

### Suppression

```
DELETE /offres/{offre}/candidatures/{candidature}
```

Supprime la candidature, son analyse (cascade) et sa conversation mémoire associée.

## `StatutCandidatureEnum`

```php
enum StatutCandidatureEnum: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Failed     = 'failed';
}
```

Cast dans le modèle `Candidature` :

```php
protected $casts = [
    'status' => StatutCandidatureEnum::class,
];
```

## Modèle Eloquent `Candidature`

```php
protected $fillable = [
    'offre_id', 'user_id', 'nom_candidat', 'cv_text', 'status',
];

protected $casts = [
    'status' => StatutCandidatureEnum::class,
];

public function offre(): BelongsTo { ... }
public function user(): BelongsTo { ... }
public function analyse(): HasOne { ... }
public function conversation(): HasOne { ... }
```

## Badges recommandation

| Valeur enum      | Couleur badge  | Libellé affiché     |
|------------------|----------------|----------------------|
| `convoquer`      | Vert           | ✅ À convoquer        |
| `attente`        | Orange         | ⏳ En attente         |
| `rejeter`        | Rouge          | ❌ À rejeter           |
| `null` (pending) | Gris           | 🔄 Analyse en cours  |
| `failed`         | Rouge foncé    | ⚠️ Analyse échouée   |

## Impact sur le modèle de données

- Table `candidatures` — voir spec 00.
- Enum `StatutCandidatureEnum`.

## Critères d'acceptation

- [ ] Un CV vide ou < 50 caractères est refusé (422) — aucun enregistrement créé.
- [ ] Un CV valide crée une `Candidature(status=pending)` et dispatch le job sans
  attendre sa complétion.
- [ ] La réponse HTTP est immédiate (< 1 s) — la page ne se fige pas.
- [ ] Le statut `pending → processing → completed/failed` est visible sur la liste.
- [ ] Soumettre un CV contre l'offre d'un autre utilisateur retourne 403.
- [ ] Supprimer une candidature supprime aussi son analyse et sa conversation.

## Dépendances

- Requiert : spec 01 (auth), spec 02 (offres — `offre_id`).
- Requis par : spec 04 (le job crée l'`Analyse`), spec 05-07 (tools/agent lisent
  `candidature_id`).

## Branche Git

`feature/candidatures` → `develop`

## Workflow OpenSpec

```bash
opsx propose candidatures
opsx apply candidatures
opsx sync candidatures
opsx archive candidatures
```
