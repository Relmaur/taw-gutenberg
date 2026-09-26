# TAW Gutenberg

The TAW **block (full site editing) theme**. Everything visual is plain Gutenberg: `theme.json`,
`templates/` and `parts/`, editable in the Site Editor. The **data layer** comes from
[`taw/core`](https://github.com/Relmaur/taw-core). You define post types, taxonomies, fields and
options pages as JSON in `taw-schema/`, and taw/core registers them, stores the values as post meta
and options, and exposes them over REST.

**PHP 8.2+ · WordPress 6.6+ · GPL-2.0-or-later**

## Install

```bash
cd wp-content/themes
TAW_STARTER=gutenberg composer create-project taw/create my-theme \
  --repository='{"type":"vcs","url":"https://github.com/Relmaur/taw-create"}'
```

That's the TAW installer ([taw-create](https://github.com/Relmaur/taw-create)). Without `TAW_STARTER`
it asks which TAW theme you want. To install this package directly:
`composer create-project taw/gutenberg my-theme --repository='{"type":"vcs","url":"https://github.com/Relmaur/taw-gutenberg"}'`.

The install runs `npm install` and `npm run build` for you (Node 22.12+).

Activate **TAW Gutenberg** in Appearance → Themes. If `composer install` or the build hasn't run, the
theme still renders, and shows administrators a notice saying what to run.

## Defining data

One JSON file per post type, taxonomy, fieldset or options page in `taw-schema/` (subfolders are
fine). See `taw-schema/` for a working example: a `book` post type, a `genre` taxonomy, and a
`book_details` fieldset.

```bash
php bin/taw schema:validate   # checks every file; no WordPress needed
```

- Values are post meta `_taw_<field id>`. Read them with `\TAW\Core\Metabox\Metabox::get($postId, 'book_author')`,
  or through REST (`/wp-json/wp/v2/book/<id>`, under `meta`).
- Namespace field ids by entity (`book_author`, not `author`): taw/core's field registry is keyed by
  bare id.
- The format reference is the taw/core README, § "Schema".

## Fields in a sidebar (data panel)

By default, fieldsets show as metaboxes under the editor canvas. taw/core (v1.51.0+) can show them in
a **TAW Data** sidebar instead: one place for a post's data, with every field type. This theme leaves
it off. Switch it on at whichever level you need; the first one set wins:

```jsonc
// One fieldset: add to its file, e.g. taw-schema/book-details.json
"ui": "panel"

// Every fieldset that doesn't choose: a new file, taw-schema/settings.json
{ "version": 1, "kind": "settings", "key": "site", "fieldsetUi": "panel" }
```

```php
// One install: wp-config.php (replaces the settings file's value, not a fieldset's own "ui")
define('TAW_DATA_UI', 'panel');
```

A fieldset shows in the sidebar **or** as a metabox, never both. Values are stored the same way
either way, so switching back and forth is safe. Full guide: taw-docs "Data panel", and the taw/core
README § "Data panel".

## Showing fields with core blocks (Block Bindings)

taw/core (v1.60.0+) registers a `taw/field` Block Bindings source, so core blocks can show TAW fields
without a custom block per field. **`templates/single-book.html` is the reference:** the cover, subtitle,
author and year come from the `book_details` fieldset.

```html
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"taw/field","args":{"field":"book_subtitle"}}}}} -->
<p></p>
<!-- /wp:paragraph -->
```

- **In the editor:** select a paragraph, heading, list item, button, image or post date and use the
  **TAW field** toolbar button (or ⋮ → **Connect to TAW field…**). One pick binds every attribute the
  field fills, e.g. an image field gives core/image its ID, URL and alt. Bound blocks preview the real
  value and are read-only; edit the value in the metabox.
- **`args`:** `field`, plus `from` (`post`, `option`, `term`, `user`), `sub` for a group's sub-field and
  `size` for images. In a Query Loop each item shows its own post's fields.
- **Empty fields** keep the block's saved content (empty in the template above), so a book without a
  subtitle just renders an empty paragraph.
- Full guide: taw-docs "Block Bindings", and the taw/core README § "Block Bindings".

## Locking the editor down for a client

taw/core's editing policies decide how much of the editor a client can use, in four layers: page
content, site structure (templates, parts, Global Styles, Navigation), design tokens and editor
features. The theme ships `taw-schema/editing.json` with `"preset": "open"`, so a fresh install is
unrestricted. Pick a level per client install in `wp-config.php`:

```php
define('TAW_EDITING_PRESET', 'structured');     // open | guided | structured | locked
define('TAW_EDITING_BYPASS_USERS', ['marco']);  // your logins: they stay unlocked, even next to client admins
```

| Level | What a client can do |
|---|---|
| `open` | Everything (the default) |
| `guided` | Compose pages from a curated set of core blocks plus this theme's own (`taw-gutenberg/*`, via `themeBlocks` in `taw-schema/editing.json`). Brand colors and font sizes only. No code editor, Custom HTML or Global Styles |
| `structured` | Pages are `contentOnly`: edit text and media inside the layout, not the layout itself. Templates and template parts are locked, and design tokens are presets only |
| `locked` | Pages are fully locked (`templateLock: all`). The Site Editor and Navigation are locked too |

- **Tools → TAW Editing** shows what's in effect and whether you're exempt.
- **Finer control:** override single settings or per-post-type rules in `taw-schema/editing.json`
  (or a child theme's copy). The format is in the taw/core README, § "Editing policies".
- **Field-only blocks (`allowBound`, taw/core v1.62.0+):** a content rule can let clients add some
  blocks only connected to a TAW field. They appear in the inserter as **Field text**, **Field image**…
  and can't be saved unbound. Add it to a post type's rule in `taw-schema/editing.json`, e.g.:

  ```json
  "layers": { "content": { "book": { "allow": ["core/heading", "core/buttons"], "allowBound": ["core/paragraph", "core/button", "core/image"] } } }
  ```

  It only matters where blocks can be inserted (`lock` left `false`); the shipped file doesn't set it.
- **If you lock yourself out:** `define('TAW_EDITING_OFF', true);` turns it all off.

## Blocks and assets (Vite)

Custom blocks live in `src/blocks/<name>/` and are built with **Vite + TypeScript**. The theme ships
one example, **Callout** (`taw-gutenberg/callout`): a note with an info, success or warning tone.

```text
src/blocks/callout/
  block.json      name, attributes, and "editorScript": "file:./index.tsx", "style": "file:./style.scss"
  index.tsx       registers block.json's metadata with edit + save
  edit.tsx, save.tsx, tones.ts, style.scss, *.test.ts(x)
```

To add a block, copy that folder. `vite.config.js` finds every block's `index.tsx`, `view.ts`,
`style.scss` and `editor.scss`, and `Setup\Blocks` registers every `block.json` through taw/core's
`Assets\Vite::block()`. Name blocks `taw-gutenberg/<name>`: that's the pattern editing policies
allow.

- **`npm run dev`** starts the Vite dev server. WordPress finds it through `dist/hot` and loads
  blocks from it. Editing a template, part or PHP file reloads the page.
- **`npm run build`** writes hashed files and a manifest to `dist/` (not committed; deploys build).
- Without a build or a dev server, blocks still register, and administrators see which assets
  are missing.
- `@wordpress/*` and `react` imports use the copies WordPress already loads, so bundles stay small.
  JSX uses the classic runtime: `import React from 'react'` in each JSX file.
- Site-wide scripts/styles: list them in `Setup\Assets::FRONTEND`/`EDITOR` and in `GLOBAL_ENTRIES`
  in `vite.config.js` (none ship, so pages load nothing extra).

## Development

```bash
composer install        # first: vite.config.js imports taw/core's Vite plugins from vendor/
npm install
composer run test       # PHPUnit + Brain Monkey (no WordPress)
composer run phpstan    # level max
php bin/taw schema:validate
npm run check           # ESLint, Prettier, tsc, Vitest, production build
```

Architecture: `functions.php` → `TAW\Gutenberg\Theme` (service registry) → `Setup\TawData`, which
calls `\TAW\Core\Boot::data()` (taw/core's data layer), `Setup\Editing`, which calls
`\TAW\Core\Boot::editing()` (editing policies), and `Setup\Blocks`/`Setup\Assets` (Vite, through
taw/core's `Assets\Vite`). See `docs/adr/`.
