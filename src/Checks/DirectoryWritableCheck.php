<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Checks;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\Contracts\Check;

final class DirectoryWritableCheck implements Check
{
    /**
     * @param  array<string>  $directories
     */
    public function __construct(
        private readonly array $directories,
    ) {}

    public function run(): CheckResult
    {
        $notWritable = [];
        $notFound = [];

        foreach ($this->directories as $dir) {
            if (! is_dir($dir)) {
                $notFound[] = $dir;
            } elseif (! is_writable($dir)) {
                $notWritable[] = $dir;
            }
        }

        if (empty($notWritable) && empty($notFound)) {
            return CheckResult::pass('Directory Writable', 'All directories are writable');
        }

        $issues = [];
        if (! empty($notFound)) {
            $issues[] = 'Not found: '.implode(', ', $notFound);
        }
        if (! empty($notWritable)) {
            $issues[] = 'Not writable: '.implode(', ', $notWritable);
        }

        return CheckResult::fail('Directory Writable', implode('. ', $issues));
    }
}
