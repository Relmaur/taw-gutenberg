# CLAUDE.md — TAW Gutenberg

Full reference: **`AGENTS.md`** in this folder. Also read the umbrella's `AGENTS.md` and
`docs/STATE.md` (one level up).

- FSE block theme; everything visual is `theme.json` + `templates/` + `parts/`.
- Data layer = taw/core via `\TAW\Core\Boot::data()` (never `Theme::boot()`); data is defined in
  `taw-schema/*.json`, validated with `php bin/taw schema:validate`.
- Services implement `Bootable`: `register()` only adds hooks.
- Namespace field ids by entity (`book_author`).
- `composer run test` + `composer run phpstan` (level max) before committing.
