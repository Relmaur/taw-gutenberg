# ADR-0003: Adopt Vite (through taw/core's shared adapter) for blocks and theme assets

## Status

Accepted (2026-09-25). Plan: umbrella `docs/plans/vite-and-data-panel.md`, Track V. Depends on taw-core
ADR-0006 (`Assets\Vite`). Supersedes the "no JS build" point of ADR-0001's trade-offs.

## Context

ADR-0001 kept this theme minimal on purpose, with no JS build and no custom blocks until a real
need appeared. That need is here:

- **Custom blocks.** The editing policies' curated allow pattern `taw-gutenberg/*` matches nothing
  until the theme has blocks.
- **Parity with the theme it replaces.** ml-theme--custom-gutenberg builds its blocks with Vite 8,
  TypeScript and Vitest, and the owner wants the same workflow here.
- **One loader.** taw/core now has a theme-agnostic Vite adapter (ADR-0006), so the theme doesn't
  need its own.

## Decision

1. **Vite 8 + TypeScript** build `src/` into `dist/` (not committed).
   - `vite.config.js` imports taw/core's shared plugins: `wordpressExternals()`, `hotFile()` and the
     opt-in `phpReload()`.
   - JSX uses the classic runtime (`React.createElement`), as WordPress provides React as a global.
2. **Two new services** (`Bootable`), both using `\TAW\Core\Assets\Vite::theme()`:
   - `Setup\Assets` for global front-end and editor assets;
   - `Setup\Blocks` registers each `src/blocks/<name>/block.json`.
3. **Blocks are named `taw-gutenberg/<name>`.** One small example block ships to prove the pipeline.
4. **Install:** `post-create-project-cmd` runs `npm install && npm run build`. Without a build the
   theme still renders, and administrators see taw/core's "run `npm run build`" notice.
5. **Quality:**
   - ESLint, Prettier, `tsc --noEmit` and Vitest run next to the existing PHP checks;
   - CI runs all of them plus a production build.

## Trade-offs

- **Node becomes a development requirement** (it wasn't before). Accepted: blocks need it, and
  sites without custom assets still work without a build.
- **No `@wordpress/scripts`:** its automatic dependency files are lost, so script dependencies are
  declared in PHP. This is the same trade-off ml-theme ADR-0001 accepted.

## Consequences

- AGENTS.md, CLAUDE.md, the README and the taw-docs page gain the build workflow (`npm run dev`,
  `npm run build`, `npm run check`).
- The theme now depends on `Assets\Vite`, so `composer.json` requires the taw/core version that
  ships it.
