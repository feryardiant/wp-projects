# Changelog

All notable changes to this project will be documented in this file. See [commit-and-tag-version](https://github.com/absolute-version/commit-and-tag-version) for commit guidelines.

## [0.0.3](https://github.com/feryardiant/wp-projects/compare/v0.0.2...v0.0.3) (2026-05-20)


### Features

* **ci:** improved build and translation generation scripts, enhanced CI/CD workflows and test reporting infra ([#29](https://github.com/feryardiant/wp-projects/issues/29)) ([0cc8818](https://github.com/feryardiant/wp-projects/commit/0cc88186fa04852c61a8e135cb27e14ae25a487b))
* **tabellio-cf7:** init support for WordPress 7.0 ([#32](https://github.com/feryardiant/wp-projects/issues/32)) ([c0dc175](https://github.com/feryardiant/wp-projects/commit/c0dc175276c6fe563d764fb3cf25d3672679b48c))


### Bug Fixes

* unable to reset wp installation when running via `WP_RESET=1 scripts/init-wp.sh` ([e6811fe](https://github.com/feryardiant/wp-projects/commit/e6811fe43408ba507a66fe89f89fe379556cb20e))

## [0.0.2](https://github.com/feryardiant/wp-projects/compare/v0.0.1...v0.0.2) (2026-05-19)


### Features

* **ci:** automate package distribution ([#28](https://github.com/feryardiant/wp-projects/issues/28)) ([6e1a3c8](https://github.com/feryardiant/wp-projects/commit/6e1a3c80ea98401c407a6f33df707594c246fa14))


### Bug Fixes

* **tabellio-cf7:** the `views` files remain using old namespace ([087b5e6](https://github.com/feryardiant/wp-projects/commit/087b5e614b881d824dc6aff6e0033c80fc05839a))

## 0.0.1 (2026-05-17)


### Features

* **cf7-entry-manager:** workaround to solve [#5](https://github.com/feryardiant/wp-projects/issues/5) ([#14](https://github.com/feryardiant/wp-projects/issues/14)) ([7f49060](https://github.com/feryardiant/wp-projects/commit/7f49060087e75ff114748ae21b720215ed4a24a9))
* **ci:** automate release workflows ([#26](https://github.com/feryardiant/wp-projects/issues/26)) ([3abf40a](https://github.com/feryardiant/wp-projects/commit/3abf40a8f8ce40919edfab8f4af46cccc8db48d3)), closes [projek-xyz/actions#24](https://github.com/projek-xyz/actions/issues/24) [projek-xyz/actions#25](https://github.com/projek-xyz/actions/issues/25) [feryardiant/wp-env#21](https://github.com/feryardiant/wp-env/issues/21)
* Init `conventional-commit` and `mu-plugins` ([#3](https://github.com/feryardiant/wp-projects/issues/3)) ([0f8d70e](https://github.com/feryardiant/wp-projects/commit/0f8d70e7bb07d80520410892883e955bf68146d8)), closes [feryardiant/wp-env#2](https://github.com/feryardiant/wp-env/issues/2)


### Bug Fixes

* don't install dev-dependencies during `make-dist.sh` ([0b93c9e](https://github.com/feryardiant/wp-projects/commit/0b93c9e12c811b4c1030a725d96a797b4c168eb7))
* improve multisite setup (feryardiant/wp-env[#4](https://github.com/feryardiant/wp-projects/issues/4)) ([95aa9a8](https://github.com/feryardiant/wp-projects/commit/95aa9a884a326ea89240ae211812bca461cecfcc))
* make sure all user notices are properly excaped ([#24](https://github.com/feryardiant/wp-projects/issues/24)) ([5279854](https://github.com/feryardiant/wp-projects/commit/5279854b5258ff046bd8460706110d264734a744))
* make sure local `wp-cli` could work properly ([#25](https://github.com/feryardiant/wp-projects/issues/25)) ([4d2af39](https://github.com/feryardiant/wp-projects/commit/4d2af39a8f4e6c91be4832bd978401ac4a95b036)), closes [feryardiant/wp-env#19](https://github.com/feryardiant/wp-env/issues/19)
