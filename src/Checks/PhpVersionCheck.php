<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Checks;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\Contracts\Check;

final class PhpVersionCheck implements Check
{
    public function __construct(
        private readonly string $minimumVersion = '8.2.0',
    ) {}

    public function run(): CheckResult
    {
        $current = PHP_VERSION;

        if (version_compare($current, $this->minimumVersion, '>=')) {
            return CheckResult::pass('PHP Version', "PHP {$current} meets minimum {$this->minimumVersion}");
        }

        return CheckResult::fail('PHP Version', "PHP {$current} does not meet minimum {$this->minimumVersion}");
    }
}
