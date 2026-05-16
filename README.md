# 📦 My Personal WordPress Projects

**A specialized monorepo for developing, testing, and distributing custom WordPress plugins and themes.**

Leverage a Docker-based local environment for rapid development, testing, and evaluation. This environment is pre-tuned for high-speed development of custom themes, plugins, and complex setups like WooCommerce or Multisite.

## 🚀 Featured Packages
- **Tabellio for Contact Form 7** ([`tabellio-cf7`](packages/tabellio-cf7/)): Never lose a lead again. Captures every submission and stores it securely in your WordPress dashboard.
- **Custom Theme** ([`custom-theme`](packages/custom-theme/)): A high-performance Blocksy child theme tailored for custom block development and deep WooCommerce integration.

## 🚀 Quick Start

1. **Configure**: `cp .env.example .env`
2. **Start**: `docker compose up -d`
3. **Evaluate**: Visit [http://localhost:8080](http://localhost:8080)

> **Default Credentials:**  
> **User:** `admin` | **Password:** `password`

### Lifecycle Commands
- **Start**: `docker compose up -d`
- **Stop**: `docker compose down`
- **Reset**: `docker compose down -v` (Wipes all data)
- **Logs**: `docker compose logs -f cli` (Monitor installation)

### Environment Variables
See [.env.example](.env.example) for a full list of available settings including site titles, admin credentials, and WooCommerce/Multisite options.

## 🏗️ Project Architecture

This project uses a modular Docker architecture to separate core service definitions from project-specific package mounts:
- **[`docker/compose.base.yml`](docker/compose.base.yml)**: Contains the base service definitions (web, cli, db, mail) and environment configurations.
- **[`compose.yml`](compose.yml)**: The root entry point that extends the base configuration and manages local package mounting via YAML anchors.

### Package Mounting
To add a new plugin or theme to the environment, update the `x-packages` anchor in the root [`compose.yml`](compose.yml). This ensures that your local package is mounted into the correct location within the container:

```yaml
x-packages: &packages
  - ./packages/my-plugin:/var/www/html/wp-content/plugins/my-plugin
```

For a comprehensive deep dive into the underlying architecture, monorepo structure, and development workflows, please refer to the [template documentation](https://github.com/feryardiant/wp-env#readme).

## ⚖️ Licensing

This project uses a **hybrid licensing model**:
- **Environment & Tools**: [MIT License](LICENSE-MIT).
- **WordPress Packages**: [GPLv3 or later](LICENSE-GPL).

This ensures the platform is free to use while ensuring all distributable assets remain compliant with the WordPress ecosystem.
