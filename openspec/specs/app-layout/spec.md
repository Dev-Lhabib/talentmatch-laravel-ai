# App Layout

## Fixed sidebar navigation rail

The sidebar SHALL be a fixed-position navigation rail on the left edge of the viewport.

- Sidebar SHALL use `position: fixed`, `left: 0`
- Sidebar SHALL span the full viewport height (`h-screen`, `top-16`)
- Sidebar SHALL be 64px wide (`w-16`)
- Sidebar SHALL NOT scroll — no `overflow-y` on the sidebar element
- Sidebar SHALL render below the navbar in the stacking context (`z-40`)
- All sidebar content (logo, navigation links, logout) SHALL remain visible at all times
- Sidebar MUST NOT overlap main content — main content SHALL have `margin-left: 64px` (`ml-16`)

### Scenario: Page with long content
- **WHEN** the main content area has content taller than the viewport
- **THEN** the sidebar remains fixed and fully visible
- **AND** only the main content area shows a vertical scrollbar

### Scenario: Page with short content
- **WHEN** the main content area has content shorter than the viewport
- **THEN** the sidebar still spans the full viewport height
- **AND** the sidebar and navbar remain fixed

### Scenario: Active state on navigation items
- **WHEN** a navigation link is active
- **THEN** its highlight state (`bg-accent text-white` or `bg-card text-white`) SHALL render correctly within the fixed sidebar

## Fixed navbar header

The navbar SHALL be a fixed-position header at the top of the viewport.

- Navbar SHALL use `position: fixed`, `top: 0`, full width
- Navbar SHALL be 64px tall (`h-16`)
- Navbar SHALL render above the sidebar in the stacking context (`z-50`)
- Main content SHALL have `padding-top: 64px` (`pt-16`) to start below the navbar

### Scenario: Navbar always visible
- **WHEN** the user scrolls the main content
- **THEN** the navbar remains fixed at the top of the viewport
