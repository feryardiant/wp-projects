# AGENTS.md

This file focuses on high-signal guidance an agent would miss without help.

## Environment

- Copy `.env.example` to `.env` before `docker compose up -d`; the project breaks without it.
- WordPress is reachable at `SITE_URL` or `http://localhost:${FORWARD_WEB_PORT}` (default 8080).
- Default WP credentials: `admin` / `password`.
- MySQL port is forwarded to `FORWARD_DB_PORT` (default 3306).

## Docker & WP-CLI

- Root `compose.yml` extends `docker/compose.base.yml`.
- To add a local plugin/theme, mount it in the `x-packages` anchor of `compose.yml`.
- Always run WordPress admin commands through the dedicated container:
  `docker compose run --rm cli wp <command>`
- DB data lives in `docker/volumes/mysql`; site files in `docker/volumes/wordpress`.
- File ownership inside containers is user `33:www-data`.

## Workspace Setup

- Run `bun install` (or `bun i`) for JS tooling — bun.lock is the source of truth, not package-lock.json.
- Run `composer update` for PHP dependencies (CI does this; no composer.lock committed at root).
- Each package in `packages/` is a WordPress plugin or theme. New packages should also be added to the `x-packages` anchor in `compose.yml`.

## Development Commands

- `composer lint` — PHPCS lint across packages and tests.
- `composer format` — run `phpcbf` auto-fix.
- `bun run format` / `bun run lint` — Biome for JS/TS/CSS/JSON.
- `composer test:unit` — PHPUnit unit tests (no WordPress required).
- `composer test:integration` — PHPUnit integration tests (requires running WP + MySQL; use `docker compose up -d` or CI's matrix).

## Tests

- Test suites: `tests/units/` and `tests/integrations/`.
- Integration tests bootstrap a WordPress test environment using `tests/integrations/wp-tests-config.php`.
- `tests/bootstrap.php` auto-discovers plugins/themes in `packages/` by checking for `functions.php` + `style.css` (theme) or `<slug>.php` (plugin).
- To run a single test file: `composer test:unit -- --filter <TestName>` or `composer test:integration -- --filter <TestName>`.

## Commit & Release Conventions

- Commit messages must follow [Conventional Commits](https://www.conventionalcommits.org/): `feat:`, `fix:`, `chore:`, `ci:`, `docs:`, `refactor:`, `perf:`, `test:`, `build:`, `revert:`.
- Commitlint runs on every commit (via simple-git-hooks + lint-staged).
- Lint-staged auto-formats staged JS/TS/CSS/JSON with Biome and PHP with phpcbf.
- Release: `bunx commit-and-tag-version -s` (bump + tag in one step).
- `bump:` is a custom commit type for version bumps.

## WordPress Init Script

- `scripts/init-wp.sh` handles fresh installs, plugin/theme activation, multisite conversion, media import, and WooCommerce setup.
- It sources `.env` automatically; override with env vars like `WP_RESET=1`, `SKIP_MEDIA=1`, `MULTISITE_ENABLED=1`.
- WordPress version compatibility is hardcoded in plugin/theme version maps inside the script. Do not change `.env.example` WP version to an unsupported value without updating the maps.

## CI (GitHub Actions)

- `.github/workflows/main.yml`: lint + unit tests (PHP 8.2-8.5) + integration tests (WP 6.0-7.0, PHP 8.2).
- Integration job pulls `scripts/` from `projek-xyz/wp-env` if not already present (happens outside that owner repo).
- Do not commit `.env`, `.env.testing`, or lockfiles that aren't checked into the repo root.

## AI-Generated Artifacts

- Store plans, specs, and design docs in `.agents/plans/` only.
- Do not use any other directory for persistent agent artifacts.

## Package: tabellio-cf7

- Entrypoint: `packages/tabellio-cf7/tabellio-cf7.php`
- Uses PSR-4 autoloading via `includes/autoload.php`.
- No JS build step at the package level.
