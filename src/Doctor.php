<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor;

use PhilipRehberger\InstallDoctor\Contracts\Check;

final class Doctor
{
    /**
     * Run all default checks.
     */
    public static function diagnose(): DiagnosticReport
    {
        return self::check(
            new Checks\PhpVersionCheck,
            new Checks\ExtensionCheck(['json', 'mbstring', 'openssl', 'pdo', 'tokenizer', 'xml', 'ctype', 'fileinfo']),
            new Checks\MemoryLimitCheck,
        );
    }

    /**
     * Run specific checks.
     */
    public static function check(Check ...$checks): DiagnosticReport
    {
        $results = [];
        foreach ($checks as $check) {
            $results[] = $check->run();
        }

        return new DiagnosticReport($results);
    }
}
