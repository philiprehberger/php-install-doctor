<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor;

enum Status: string
{
    case Pass = 'pass';
    case Warning = 'warning';
    case Fail = 'fail';
}
