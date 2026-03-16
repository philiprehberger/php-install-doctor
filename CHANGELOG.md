# Changelog

All notable changes to this project will be documented in this file.

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
