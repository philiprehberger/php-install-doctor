<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Checks;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\Contracts\Check;

final class MemoryLimitCheck implements Check
{
    public function __construct(
        private readonly int $minimumMb = 128,
    ) {}

    public function run(): CheckResult
    {
        $limit = ini_get('memory_limit');

        if ($limit === '') {
            return CheckResult::warning('Memory Limit', 'Unable to determine memory limit');
        }

        $bytes = self::parseMemoryLimit($limit);

        if ($bytes === -1) {
            return CheckResult::pass('Memory Limit', 'Unlimited memory');
        }

        $mb = (int) ($bytes / 1024 / 1024);

        if ($mb >= $this->minimumMb) {
            return CheckResult::pass('Memory Limit', "{$mb}MB meets minimum {$this->minimumMb}MB");
        }

        return CheckResult::fail('Memory Limit', "{$mb}MB is below minimum {$this->minimumMb}MB");
    }

    public static function parseMemoryLimit(string $limit): int
    {
        $limit = trim($limit);

        if ($limit === '-1') {
            return -1;
        }

        $value = (int) $limit;
        $unit = strtolower(substr($limit, -1));

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
