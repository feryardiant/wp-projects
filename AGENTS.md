# Agent Guidelines & Project Context

This file serves as a persistent context for AI agents working in this project. All agents MUST read and adhere to these guidelines to ensure consistency and prevent environment breakage.

## 🏗 Project Identity

A monorepo for custom WordPress asset development (plugins/themes) and distribution, backed by an automated Dockerized development environment. Includes specialized tools like **Tabellio for Contact Form 7**.

## 🛠 Operational Mandates

1.  **Environment Variables**: The project is strictly dependent on a `.env` file. NEVER hardcode values in [`compose.yml`](compose.yml). Verify required variables (see `README.md`) before proposing changes.
2.  **Service Lifecycle**: Always use `docker compose` for starting/stopping services.
3.  **WP-CLI Management**: Use the dedicated `cli` service for all WordPress commands.
    *   Command pattern: `docker compose run --rm cli wp <command>`
4.  **Volumes & Persistence**: Database data is in `docker/volumes/mysql`, and site files are in `docker/volumes/wordpress`. Modification of site files MUST be done with awareness of file permissions (the environment uses user `33` / `www-data`).
5.  **Metadata Management**: ALL AI-generated metadata (plans, specs, and design documents) MUST be stored exclusively in the `conductor/` directory. Do not use any other directory for persistent or temporary agent artifacts.

## 📁 Development Guidelines

1.  **Modular Infrastructure**: The environment uses a modular pattern where the root [`compose.yml`](compose.yml) extends [`docker/compose.base.yml`](docker/compose.base.yml). Core service changes should be made in the base file, while project-specific mounts and extensions belong in the root file.
2.  **Package Management**: Prefer mounting custom themes/plugins via the `x-packages` YAML anchor in the root [`compose.yml`](compose.yml).
3.  **Initialization**: Changes to site titles, admin users, or pre-installed plugins should be implemented in [`scripts/init-wp.sh`](scripts/init-wp.sh).

## 📝 Persistent Memory (Context)

- **Date**: 2026-05-10
- **Status**: Documentation updated to reflect modular Docker architecture and standardized tracking in `conductor/`.
- **Next Steps**: Maintain strict adherence to modular configuration patterns and use `conductor/` for all planning.
