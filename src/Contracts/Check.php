<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Contracts;

use PhilipRehberger\InstallDoctor\CheckResult;

interface Check
{
    public function run(): CheckResult;
}
