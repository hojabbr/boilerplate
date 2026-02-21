## [1.4.0](https://github.com/hojabbr/boilerplate/compare/v1.3.0...v1.4.0) (2026-02-21)

### Features

- enhance blog post series functionality and relationships ([55823f9](https://github.com/hojabbr/boilerplate/commit/55823f9d5915bce65abd64b09aa4fda04a85f3b5))

## [1.3.0](https://github.com/hojabbr/boilerplate/compare/v1.2.0...v1.3.0) (2026-02-21)

### Features

- integrate Laravel AI SDK and enhance blog features ([4d79835](https://github.com/hojabbr/boilerplate/commit/4d798356ca9d6f90b0986945e6e6628ae0c2ab4f))

## [1.2.0](https://github.com/hojabbr/boilerplate/compare/v1.1.0...v1.2.0) (2026-02-21)

### Features

- add login and registration feature flags and update related logic ([3309963](https://github.com/hojabbr/boilerplate/commit/3309963445d5c78e160a06833670408fd96d54d3))

## [1.1.0](https://github.com/hojabbr/boilerplate/compare/v1.0.0...v1.1.0) (2026-02-21)

### Features

- **docs:** update project documentation for clarity and consistency ([4adea62](https://github.com/hojabbr/boilerplate/commit/4adea625fd630797c5bda06ddc9da92f7ae9c677))

## 1.0.0 (2026-02-20)

### Features

- enhance CI workflow and improve type safety in UI components ([e0b0c52](https://github.com/hojabbr/boilerplate/commit/e0b0c5205ab007f68c65d7ef80222b9480977480))
- enhance models and localization support ([ecf9192](https://github.com/hojabbr/boilerplate/commit/ecf919238f01e853071d1e2aa0dca8e2d3528818))
- enhance PaginatorLinks and Dashboard components for improved key handling and structure ([c75e317](https://github.com/hojabbr/boilerplate/commit/c75e3173ae2b15a5b83dfd00e6f06e008fe0824f))
- enhance SEO and localization support across web controllers and layouts ([f5440cb](https://github.com/hojabbr/boilerplate/commit/f5440cb2231c460bb5ecc5679a8bc41b2e07136e))
- improve accessibility and motion handling in UI ([dcefaa2](https://github.com/hojabbr/boilerplate/commit/dcefaa2da68fb27328d7c07e503208021327a1fe))
- integrate localization and role/permission management ([1828094](https://github.com/hojabbr/boilerplate/commit/1828094d4ff2cd953a06e4b24bcb9999306c4b7c))
- **permissions:** add permission management and UI components ([cca7800](https://github.com/hojabbr/boilerplate/commit/cca7800a5b8d52e106f256a2c07b3aa7013e4326))

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- **Architecture:** Backend reorganized into `app/Core/` (contracts, exceptions, shared PagePropsService) and `app/Domains/` (Auth, Blog, Contact, Pages, Dashboard, Profile, Landing) with Actions, DTOs, Queries, Services. Controllers are thin and delegate to domain classes. Fortify actions moved to `App\Domains\Auth\Actions\`. Frontend reorganized under `resources/js/` with `features/` (auth, blog, contact, dashboard, landing, pages, profile), `context/`, `themes/`, `services/`. See ARCHITECTURE.mdc and README for full structure.

### Added

- Initial release as open-source Laravel React boilerplate (Laravel 12, Inertia 2, React 19, Filament, localization, feature flags, Scout, Reverb).
