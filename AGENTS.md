# AGENTS.md — TAW Gutenberg

The TAW block (FSE) theme: a **data-only consumer of taw/core**. Part of the TAW umbrella
(`TAW/taw-gutenberg`, a submodule; Local by Flywheel sites symlink into it; see the umbrella's
AGENTS.md and ADR-0002). Read the umbrella's `docs/STATE.md` for where work left off.

## Stack (verify versions in composer.lock before using an API)

- WordPress 6.6+ (developed against 7.1), theme.json v3, full site editing (no PHP templates).
- PHP 8.2+. `taw/core` (Composer, pinned by tag in `composer.lock`), booted with
  `\TAW\Core\Boot::data()` only. Never call `Theme::boot()` here: that's taw-theme's classic toolkit.
- No JS build yet (YAGNI). Add one only with a real custom-block need, and record it in an ADR.

## Layout

| Path | Purpose |
|---|---|
| `theme.json`, `templates/`, `parts/` | All presentation, plain Gutenberg markup |
| `functions.php` | Loads Composer (fails soft with an admin notice), registers the services |
| `app/Theme.php` | Service registry: add a service by listing it in `services()` |
| `app/Contracts/Bootable.php` | Service contract: `register()` only adds hooks |
| `app/Setup/TawData.php` | Boots taw/core's data layer at `after_setup_theme:0` |
| `app/Setup/Editing.php` | Boots taw/core's editing policies (`Boot::editing()`) at `after_setup_theme:0` (ADR-0002) |
| `taw-schema/*.json` | Post types, taxonomies, fieldsets, options pages (taw/core ADR-0004) |
| `taw-schema/editing.json` | The editor lockdown policy. Ships `"preset": "open"`; installs pick a level in wp-config.php |
| `bin/taw` | `schema:validate` (no WordPress) |
| `docs/adr/` | This theme's decisions |

## Commands

```bash
composer run test          # unit tests, no WordPress
composer run phpstan       # level max, must stay green
php bin/taw schema:validate
```

## Rules

- Field ids are namespaced by entity (`book_author`): taw/core's field registry is keyed by bare id,
  and this theme shares the test site's database with taw-theme.
- New data goes in `taw-schema/` as JSON. `schema:validate` and `SchemaFilesTest` must stay green.
- **Editor lockdown is taw/core's, not this theme's** (ADR-0002). Don't add lockdown code here. Change
  `taw-schema/editing.json` or a post type's `"editing"` rule. The shipped preset must stay `open`
  (`EditingTest` checks this); clients get their level from `TAW_EDITING_PRESET` in wp-config.php.
- After every taw/core tag: `composer update taw/core`, then commit `composer.lock` (the umbrella's
  taw-bump/taw-ship flows).
- Umbrella rules apply: confirm every state-changing git operation (scope and actor), bump the
  umbrella pointer after committing here, and push this repo before the umbrella.
