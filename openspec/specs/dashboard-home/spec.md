# Dashboard Home

## Purpose

The dashboard homepage (`/dashboard`) provides recruiters with a high-level overview of the recruitment platform: key metrics, recent offers, recent analyses, and quick action buttons.

## Requirements

### Requirement: Dashboard stats cards

The dashboard SHALL display 11 stat cards in two responsive rows (6 + 5 per row on large screens). Each card SHALL show a colored left border accent, an icon with matching background, a large number, a French label, and a descriptive subtitle. The 11 cards are:

| Card | Data source | Color |
|---|---|---|
| Total Candidats | `Candidate::count()` | teal (`#2dd4bf`) |
| Total Offres | `Offre::where('user_id', auth()->id())->count()` | blue (`#3b82f6`) |
| Analyses complétées | Applications with `status = completed` that have an analysis | green (`#34d399`) |
| Analyses en attente | Applications with `status = pending` or `processing` | yellow (`#fbbf24`) |
| Analyses échouées | Applications with `status = failed` | red (`#dc4a3c`) |
| Candidats sans analyse | Candidates with no applications at all | gray (`#94a3b8`) |
| Offres actives | Offres with `status = 'open'` | purple (`#a78bfa`) |
| Taux de réussite | Percentage of completed analyses with `matching_score >= 70` | emerald (`#10b981`) |
| À convoquer | Analyses where `recommandation = 'convoquer'` | green (`#34d399`) |
| En attente | Analyses where `recommandation = 'attente'` | yellow (`#fbbf24`) |
| Non retenu | Analyses where `recommandation = 'rejeter'` | red (`#dc4a3c`) |

#### Scenario: Stats cards render with correct counts

- **WHEN** a user visits `/dashboard`
- **THEN** 11 stat cards SHALL display in two rows (5 + 6 on large screens)
- **AND** each card SHALL have the correct count or percentage value
- **AND** each card SHALL have a colored left border matching its assigned color
- **AND** each card SHALL display a descriptive subtitle under the main label

### Requirement: Recent offres list

The dashboard SHALL display the last 5 offres in a list, each showing title, creation date, candidatures count, and a status badge. A "Voir toutes les offres" link SHALL link to `/offres`.

#### Scenario: Recent offres show when offres exist

- **WHEN** the user has at least one offre
- **THEN** the last 5 offres SHALL appear with title, date, candidatures count, and status

### Requirement: Recent analyses list

The dashboard SHALL display the last 5 completed applications, each showing candidate name, offer title, matching score badge (green >70%, yellow 40–70%, red <40%), and completion date. A "Voir toutes les analyses" link SHALL link to the candidates dashboard.

#### Scenario: Recent analyses show when completed analyses exist

- **WHEN** the user has completed analyses
- **THEN** the last 5 SHALL display with candidate name, offer title, score badge, and date

### Requirement: Quick action buttons

The dashboard SHALL display "Actions rapides" buttons: "Ajouter un candidat" → `/candidates/create`, "Créer une offre" → `/offres/create`, "Voir les feedbacks" → `/feedback`.

#### Scenario: Quick action buttons render

- **WHEN** a user visits `/dashboard`
- **THEN** three quick action buttons SHALL render below the stats and lists
