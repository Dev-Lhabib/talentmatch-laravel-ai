# Dashboard Layout

## Sidebar navigation icons

The sidebar SHALL display navigation icons in the following top-to-bottom order: home (house icon), dashboard (grid icon), people (users icon — active state for candidates page), feedback (smiley icon), offres (briefcase icon). The bottom section SHALL contain: help (question-mark icon), logout (exit icon).

- The people icon SHALL use `bg-accent text-white` active state when on the candidates dashboard route
- The dashboard icon SHALL use `bg-card text-white` active state when on the dashboard route
- The help icon SHALL link to the profile page (`route('profile.show')`)

### Scenario: Sidebar renders with correct icons
- **WHEN** a user navigates to any authenticated page
- **THEN** the sidebar SHALL display all 7 icons in the correct order
- **AND** the active page's icon SHALL have the appropriate highlight

## Single unified layout

The application SHALL use a single authenticated layout (`layouts/app.blade.php`). The duplicate `components/layout/dashboard.blade.php` SHALL be removed, and `dashboard/candidates.blade.php` SHALL extend the unified layout.

### Scenario: All authenticated pages use same layout
- **WHEN** a user visits any authenticated page (dashboard, candidates, offres, profile, feedback)
- **THEN** the same base layout SHALL render (fixed navbar, fixed sidebar, scrollable main content)
- **AND** the sidebar and navbar SHALL remain fixed during scrolling

## Recommendation banner

The candidate analysis recommendation banner SHALL use `bg-[#143d3a]` (via the `success-bg` Tailwind token) as its background color.

### Scenario: Recommendation banner renders
- **WHEN** a candidate analysis is displayed
- **THEN** the recommendation banner SHALL have the background color `#143d3a`
