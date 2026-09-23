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
git clone https://github.com/Relmaur/taw-gutenberg.git
cd taw-gutenberg && composer install --no-dev
```

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

## Development

```bash
composer install
composer run test       # PHPUnit + Brain Monkey (no WordPress)
composer run phpstan    # level max
php bin/taw schema:validate
```

Architecture: `functions.php` → `TAW\Gutenberg\Theme` (service registry) → `Setup\TawData`, which
calls `\TAW\Core\Boot::data()`, taw/core's data-only entry point. See `docs/adr/`.
