<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Checks;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\Contracts\Check;

final class ExtensionCheck implements Check
{
    /**
     * @param  array<string>  $extensions
     */
    public function __construct(
        private readonly array $extensions,
    ) {}

    public function run(): CheckResult
    {
        $missing = [];

        foreach ($this->extensions as $ext) {
            if (! extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        if (empty($missing)) {
            return CheckResult::pass('Extensions', 'All required extensions are loaded ('.implode(', ', $this->extensions).')');
        }

        return CheckResult::fail('Extensions', 'Missing extensions: '.implode(', ', $missing));
    }
}
