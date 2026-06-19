# Dashboard Home

## Purpose

The dashboard homepage (`/dashboard`) provides recruiters with a high-level overview of the recruitment platform: key metrics, recent offers, recent analyses, and quick action buttons.

## Requirements

### Requirement: Dashboard stats cards

The dashboard SHALL display 4 stat cards in a responsive grid: Total Candidats (from `candidates` table), Total Offres (from `offres` table), Analyses Complétées (applications with `status = completed`), Analyses en Attente (applications with `status = pending` or `processing`). Each card SHALL show an icon, a large number, a label in French, and a subtle colored border (teal for positive, red for alerts).

#### Scenario: Stats cards render with correct counts

- **WHEN** a user visits `/dashboard`
- **THEN** 4 stat cards SHALL display side by side on large screens

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
