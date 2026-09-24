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

Activate **TAW Gutenberg** in Appearance → Themes. If `composer install` hasn't run, the theme still
renders, and shows administrators a notice.

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
| `guided` | Compose pages from a curated set of core blocks. Brand colors and font sizes only. No code editor, Custom HTML or Global Styles |
| `structured` | Pages are `contentOnly`: edit text and media inside the layout, not the layout itself. Templates and template parts are locked, and design tokens are presets only |
| `locked` | Pages are fully locked (`templateLock: all`). The Site Editor and Navigation are locked too |

- **Tools → TAW Editing** shows what's in effect and whether you're exempt.
- **Finer control:** override single settings or per-post-type rules in `taw-schema/editing.json`
  (or a child theme's copy). The format is in the taw/core README, § "Editing policies".
- **If you lock yourself out:** `define('TAW_EDITING_OFF', true);` turns it all off.

## Development

```bash
composer install
composer run test       # PHPUnit + Brain Monkey (no WordPress)
composer run phpstan    # level max
php bin/taw schema:validate
```

Architecture: `functions.php` → `TAW\Gutenberg\Theme` (service registry) → `Setup\TawData`, which
calls `\TAW\Core\Boot::data()` (taw/core's data layer), and `Setup\Editing`, which calls
`\TAW\Core\Boot::editing()` (editing policies). See `docs/adr/`.
