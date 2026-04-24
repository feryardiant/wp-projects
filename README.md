# 📦 My Personal WordPress Projects

**A specialized monorepo for developing, testing, and distributing custom WordPress plugins and themes.**

Leverage a Docker-based local environment for rapid development, testing, and evaluation. This environment is pre-tuned for high-speed development of custom themes, plugins, and complex setups like WooCommerce or Multisite.

### 🚀 Featured Packages
- **Entry Manager for Contact Form 7** ([`cf7-entry-manager`](packages/cf7-entry-manager/)): Never lose a lead again. Captures every submission and stores it securely in your WordPress dashboard.
- **Custom Theme** ([`custom-theme`](packages/custom-theme/)): A high-performance Blocksy child theme tailored for custom block development and deep WooCommerce integration.

### ⚡ Key Features
- 🚀 **Zero-Config**: Fully installed WordPress site via `docker compose up`.
- 🛍️ **WooCommerce Ready**: Automatic store setup and configuration.
- 🌐 **Multisite Support**: One-click conversion to a subfolder network.
- 📧 **Mailpit Integrated**: Instant email capture and testing dashboard.
- 🏗️ **Monorepo Structure**: Manage multiple themes and plugins in one project.

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

This project is built upon the [WP Env template](https://github.com/feryardiant/wp-env). For a comprehensive deep dive into the underlying architecture, monorepo structure, and development workflows, please refer to the [template documentation](https://github.com/feryardiant/wp-env#readme).

## ⚖️ Licensing

This project uses a **hybrid licensing model**:
- **Environment & Tools**: [MIT License](LICENSE-MIT).
- **WordPress Packages**: [GPLv3 or later](LICENSE-GPL).

This ensures the platform is free to use while ensuring all distributable assets remain compliant with the WordPress ecosystem.
