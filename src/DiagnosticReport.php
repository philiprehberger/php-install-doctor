<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor;

final class DiagnosticReport
{
    /** @var array<CheckResult> */
    public readonly array $passed;

    /** @var array<CheckResult> */
    public readonly array $warnings;

    /** @var array<CheckResult> */
    public readonly array $failed;

    /**
     * @param  array<CheckResult>  $results
     */
    public function __construct(array $results)
    {
        $passed = [];
        $warnings = [];
        $failed = [];

        foreach ($results as $result) {
            match ($result->status) {
                Status::Pass => $passed[] = $result,
                Status::Warning => $warnings[] = $result,
                Status::Fail => $failed[] = $result,
            };
        }

        $this->passed = $passed;
        $this->warnings = $warnings;
        $this->failed = $failed;
    }

    public function isHealthy(): bool
    {
        return empty($this->failed);
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            fn (CheckResult $r) => $r->toArray(),
            [...$this->passed, ...$this->warnings, ...$this->failed],
        );
    }

    public function toConsoleOutput(): string
    {
        $lines = [];
        $lines[] = 'PHP Install Doctor';
        $lines[] = str_repeat('=', 40);

        foreach ($this->passed as $result) {
            $lines[] = "[PASS] {$result->name}: {$result->message}";
        }
        foreach ($this->warnings as $result) {
            $lines[] = "[WARN] {$result->name}: {$result->message}";
        }
        foreach ($this->failed as $result) {
            $lines[] = "[FAIL] {$result->name}: {$result->message}";
        }

        $lines[] = '';
        $total = count($this->passed) + count($this->warnings) + count($this->failed);
        $lines[] = sprintf(
            'Results: %d passed, %d warnings, %d failed (total: %d)',
            count($this->passed),
            count($this->warnings),
            count($this->failed),
            $total,
        );

        return implode("\n", $lines);
    }
}
