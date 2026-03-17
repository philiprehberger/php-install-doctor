# PHP Install Doctor

[![Tests](https://github.com/philiprehberger/php-install-doctor/actions/workflows/tests.yml/badge.svg)](https://github.com/philiprehberger/php-install-doctor/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/philiprehberger/php-install-doctor.svg)](https://packagist.org/packages/philiprehberger/php-install-doctor)
[![License](https://img.shields.io/github/license/philiprehberger/php-install-doctor)](LICENSE)

CLI diagnostic tool that checks PHP extensions, permissions, and configuration.

## Requirements

- PHP ^8.2

## Installation

```bash
composer require philiprehberger/php-install-doctor
```

## CLI Usage

Run the built-in diagnostic command:

```bash
vendor/bin/doctor
```

Example output:

```
PHP Install Doctor
========================================
[PASS] PHP Version: PHP 8.3.4 meets minimum 8.2.0
[PASS] Extensions: All required extensions are loaded (json, mbstring, openssl, pdo, tokenizer, xml, ctype, fileinfo)
[PASS] Memory Limit: 256MB meets minimum 128MB

Results: 3 passed, 0 warnings, 0 failed (total: 3)
```

The command exits with code `0` if all checks pass, or `1` if any check fails.

## Programmatic Usage

### Run all default checks

```php
use PhilipRehberger\InstallDoctor\Doctor;

$report = Doctor::diagnose();

if ($report->isHealthy()) {
    echo "All checks passed!\n";
} else {
    echo $report->toConsoleOutput();
}
```

### Run specific checks

```php
use PhilipRehberger\InstallDoctor\Doctor;
use PhilipRehberger\InstallDoctor\Checks\PhpVersionCheck;
use PhilipRehberger\InstallDoctor\Checks\ExtensionCheck;
use PhilipRehberger\InstallDoctor\Checks\MemoryLimitCheck;
use PhilipRehberger\InstallDoctor\Checks\DirectoryWritableCheck;

$report = Doctor::check(
    new PhpVersionCheck('8.2.0'),
    new ExtensionCheck(['pdo', 'redis', 'imagick']),
    new MemoryLimitCheck(256),
    new DirectoryWritableCheck(['/tmp', '/var/log']),
);

echo $report->toConsoleOutput();
```

### Get results as array

```php
$report = Doctor::diagnose();
$data = $report->toArray();

// [
//     ['status' => 'pass', 'name' => 'PHP Version', 'message' => '...'],
//     ['status' => 'pass', 'name' => 'Extensions', 'message' => '...'],
//     ...
// ]
```

## Built-in Checks

| Check | Description | Default |
|---|---|---|
| `PhpVersionCheck` | Verifies PHP meets a minimum version | `8.2.0` |
| `ExtensionCheck` | Checks that required extensions are loaded | json, mbstring, openssl, pdo, tokenizer, xml, ctype, fileinfo |
| `MemoryLimitCheck` | Validates memory limit meets a minimum | `128` MB |
| `DirectoryWritableCheck` | Confirms directories exist and are writable | (none) |

## Custom Checks

Implement the `Check` interface to create your own checks:

```php
use PhilipRehberger\InstallDoctor\Contracts\Check;
use PhilipRehberger\InstallDoctor\CheckResult;

final class DatabaseConnectionCheck implements Check
{
    public function run(): CheckResult
    {
        try {
            new \PDO('mysql:host=localhost;dbname=app', 'root', '');
            return CheckResult::pass('Database', 'Connection successful');
        } catch (\PDOException $e) {
            return CheckResult::fail('Database', $e->getMessage());
        }
    }
}

$report = Doctor::check(
    new DatabaseConnectionCheck(),
);
```

## API

### `Doctor`

| Method | Description |
|---|---|
| `Doctor::diagnose(): DiagnosticReport` | Run all default checks |
| `Doctor::check(Check ...$checks): DiagnosticReport` | Run specific checks |

### `DiagnosticReport`

| Property / Method | Description |
|---|---|
| `$report->passed` | Array of passed `CheckResult` instances |
| `$report->warnings` | Array of warning `CheckResult` instances |
| `$report->failed` | Array of failed `CheckResult` instances |
| `$report->isHealthy(): bool` | `true` if no failures |
| `$report->toArray(): array` | All results as arrays |
| `$report->toConsoleOutput(): string` | Formatted console output |

### `CheckResult`

| Method | Description |
|---|---|
| `CheckResult::pass(string $name, string $message): self` | Create a passing result |
| `CheckResult::warning(string $name, string $message): self` | Create a warning result |
| `CheckResult::fail(string $name, string $message): self` | Create a failing result |
| `$result->toArray(): array` | Result as associative array |

### `Status`

Enum with cases: `Pass`, `Warning`, `Fail`.

## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## License

MIT
