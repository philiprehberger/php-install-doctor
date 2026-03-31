# Changelog

All notable changes to `php-install-doctor` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.5] - 2026-03-31

### Changed
- Standardize README to 3-badge format with emoji Support section
- Update CI checkout action to v5 for Node.js 24 compatibility
- Add GitHub issue templates, dependabot config, and PR template

## [1.0.4] - 2026-03-23

### Fixed
- Standardize CHANGELOG preamble to use package name

## [1.0.3] - 2026-03-20

### Added
- Expanded test suite with dedicated DiagnosticReport and CheckResult tests

## [1.0.2] - 2026-03-17

### Changed
- Standardized package metadata, README structure, and CI workflow per package guide

## [1.0.1] - 2026-03-16

### Changed
- Standardize composer.json: add type, homepage, scripts

## [1.0.0] - 2026-03-13

### Added

- `Doctor` class with `diagnose()` and `check()` static methods
- `Check` contract interface for custom checks
- `CheckResult` value object with pass, warning, and fail factory methods
- `Status` enum (pass, warning, fail)
- `DiagnosticReport` with `isHealthy()`, `toArray()`, and `toConsoleOutput()`
- Built-in checks: `PhpVersionCheck`, `ExtensionCheck`, `MemoryLimitCheck`, `DirectoryWritableCheck`
- CLI binary (`bin/doctor`) for quick diagnostics
