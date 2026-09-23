# ADR-0002: TAW Gutenberg locks the editor through taw/core editing policies, preset per install

## Status

Accepted (2026-09-23). Plan: umbrella `docs/plans/editing-policies.md`. Depends on taw-core ADR-0005.

## Context

TAW Gutenberg is a full site editing theme (ADR-0001), and it's used for client sites that need
different amounts of editor freedom: from composing pages freely to editing only text and images
inside fixed layouts. The previous Gutenberg theme, ml-theme--custom-gutenberg, did this with its
own `ThemeMode` class, which had one on/off level. taw/core now provides layered editing policies
(taw-core ADR-0005): content, site structure, design tokens and editor features, with presets
`open`, `guided`, `structured` and `locked`.

## Decision

1. The theme turns on editing policies with a `Setup\Editing` service in `Theme::services()`,
   which calls `\TAW\Core\Boot::editing()`.
2. The theme ships `taw-schema/editing.json` with `"preset": "open"`: a developer installing the
   theme gets full FSE.
3. Each client install picks its level in `wp-config.php`:
   - `TAW_EDITING_PRESET` sets the preset.
   - `TAW_EDITING_BYPASS_USERS` names the developer accounts that stay unlocked.
   - A child theme's `taw-schema/editing.json` covers deeper per-client rules.
4. The theme keeps no lockdown code of its own. `ThemeMode` is **not** ported.

## Trade-offs

- **The theme depends on taw/core for editor behavior, not only for data.** That's accepted: owning
  the tooling in taw/core is the point.
- **Until the theme has its own blocks, the curated allow-list only covers core blocks.** That's
  acceptable for now, and `taw-gutenberg/*` joins the list when the first block ships.

## Consequences

- A fresh install behaves exactly like today (`open`).
- The README documents the presets, the constants and recovery (`TAW_EDITING_OFF`).
- `composer.json` needs taw/core at or above the version that ships `Boot::editing()`.
