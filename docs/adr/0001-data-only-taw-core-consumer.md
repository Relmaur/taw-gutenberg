# ADR-0001: TAW Gutenberg is a block (FSE) theme and a data-only taw/core consumer

## Status

Accepted (2026-09-23).

## Context

TAW has two kinds of site. Classic sites use taw-theme, which runs taw/core's whole classic-theme
toolkit: PHP blocks, Vite, Alpine, the visual editor. Gutenberg sites need taw/core's **data layer**
(post types, taxonomies, fields, options) without any of that presentation, because Gutenberg
provides its own. taw/core v1.42–v1.44 made this possible: `Boot::data()` (taw-core ADR-0003) and a
schema registry defined in PHP or `taw-schema/*.json` (taw-core ADR-0004). taw/core serves TAW sites
only, so this theme and taw-theme are its two consumers.

## Decision

1. **Full site editing block theme:** `theme.json` (v3), `templates/*.html` and `parts/*.html`.
   There are no PHP templates. The Site Editor owns layout.
2. **Data-only taw/core consumer:** `composer require taw/core`, booted through the `TawData` service
   with `\TAW\Core\Boot::data()` at `after_setup_theme:0`. The theme never calls `Theme::boot()`.
3. **Data is defined as JSON** in `taw-schema/` (PHP definitions remain possible through the
   `taw_schema_register` action). `bin/taw schema:validate` checks the files without WordPress, in CI
   and locally.
4. **Services only add hooks in `register()`** (`Contracts\Bootable`), so the service list can be
   unit-tested without WordPress.
5. **Field ids are namespaced by entity** (`book_subtitle`, not `subtitle`). taw/core's field
   registry is keyed by bare id until its Phase 2, and this theme shares a database with taw-theme
   on the test site.

## Trade-offs

- **FSE instead of a hybrid PHP shell:** templates can't run arbitrary PHP. Accepted: layout is
  Gutenberg's job here, and taw data reaches templates through Block Bindings (taw/core Phase 4)
  rather than PHP.
- **Minimal on purpose (YAGNI):** no JS build, no custom blocks yet. They get added when a real need
  appears, not up front.
- **Data tied to the theme:** the `taw-schema/` definitions live in the theme, so switching themes
  unregisters the post types. The data stays in the database. Site-level definitions that must
  survive a theme switch go in `wp-content/taw-schema/` instead.

## Consequences

- After every taw/core tag, this theme's `composer.lock` must be updated, just like taw-theme's.
- Planned: Block Bindings (`taw/field`), a loop block, and per-post-type editing policies (taw-core
  roadmap items 4, 5 and E) will be adopted here first.
