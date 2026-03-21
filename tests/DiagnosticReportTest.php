<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Tests;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\DiagnosticReport;
use PHPUnit\Framework\TestCase;

final class DiagnosticReportTest extends TestCase
{
    public function test_empty_results_produce_empty_report(): void
    {
        $report = new DiagnosticReport([]);

        $this->assertSame([], $report->passed);
        $this->assertSame([], $report->warnings);
        $this->assertSame([], $report->failed);
    }

    public function test_empty_report_is_healthy(): void
    {
        $report = new DiagnosticReport([]);

        $this->assertTrue($report->isHealthy());
    }

    public function test_report_with_only_passes_is_healthy(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('A', 'OK'),
            CheckResult::pass('B', 'OK'),
        ]);

        $this->assertTrue($report->isHealthy());
        $this->assertCount(2, $report->passed);
        $this->assertCount(0, $report->warnings);
        $this->assertCount(0, $report->failed);
    }

    public function test_report_with_only_warnings_is_healthy(): void
    {
        $report = new DiagnosticReport([
            CheckResult::warning('A', 'Caution'),
            CheckResult::warning('B', 'Caution'),
        ]);

        $this->assertTrue($report->isHealthy());
        $this->assertCount(0, $report->passed);
        $this->assertCount(2, $report->warnings);
        $this->assertCount(0, $report->failed);
    }

    public function test_report_with_failures_is_not_healthy(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('A', 'OK'),
            CheckResult::fail('B', 'Bad'),
        ]);

        $this->assertFalse($report->isHealthy());
    }

    public function test_report_with_only_failures_is_not_healthy(): void
    {
        $report = new DiagnosticReport([
            CheckResult::fail('A', 'Bad'),
            CheckResult::fail('B', 'Worse'),
        ]);

        $this->assertFalse($report->isHealthy());
        $this->assertCount(0, $report->passed);
        $this->assertCount(0, $report->warnings);
        $this->assertCount(2, $report->failed);
    }

    public function test_results_are_sorted_into_correct_categories(): void
    {
        $report = new DiagnosticReport([
            CheckResult::fail('Fail1', 'msg'),
            CheckResult::pass('Pass1', 'msg'),
            CheckResult::warning('Warn1', 'msg'),
            CheckResult::pass('Pass2', 'msg'),
            CheckResult::fail('Fail2', 'msg'),
            CheckResult::warning('Warn2', 'msg'),
        ]);

        $this->assertCount(2, $report->passed);
        $this->assertCount(2, $report->warnings);
        $this->assertCount(2, $report->failed);

        $this->assertSame('Pass1', $report->passed[0]->name);
        $this->assertSame('Pass2', $report->passed[1]->name);
        $this->assertSame('Warn1', $report->warnings[0]->name);
        $this->assertSame('Warn2', $report->warnings[1]->name);
        $this->assertSame('Fail1', $report->failed[0]->name);
        $this->assertSame('Fail2', $report->failed[1]->name);
    }

    public function test_to_array_returns_all_results_in_order(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('P', 'ok'),
            CheckResult::warning('W', 'warn'),
            CheckResult::fail('F', 'bad'),
        ]);

        $array = $report->toArray();

        $this->assertCount(3, $array);
        $this->assertSame('pass', $array[0]['status']);
        $this->assertSame('P', $array[0]['name']);
        $this->assertSame('warning', $array[1]['status']);
        $this->assertSame('W', $array[1]['name']);
        $this->assertSame('fail', $array[2]['status']);
        $this->assertSame('F', $array[2]['name']);
    }

    public function test_to_array_order_is_passed_then_warnings_then_failed(): void
    {
        $report = new DiagnosticReport([
            CheckResult::fail('F', 'bad'),
            CheckResult::pass('P', 'ok'),
            CheckResult::warning('W', 'warn'),
        ]);

        $array = $report->toArray();

        $this->assertSame('pass', $array[0]['status']);
        $this->assertSame('warning', $array[1]['status']);
        $this->assertSame('fail', $array[2]['status']);
    }

    public function test_to_array_empty_report(): void
    {
        $report = new DiagnosticReport([]);

        $this->assertSame([], $report->toArray());
    }

    public function test_console_output_contains_header(): void
    {
        $report = new DiagnosticReport([]);
        $output = $report->toConsoleOutput();

        $this->assertStringContainsString('PHP Install Doctor', $output);
        $this->assertStringContainsString(str_repeat('=', 40), $output);
    }

    public function test_console_output_with_all_statuses(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('Pass Check', 'All good'),
            CheckResult::warning('Warn Check', 'Be careful'),
            CheckResult::fail('Fail Check', 'Broken'),
        ]);

        $output = $report->toConsoleOutput();

        $this->assertStringContainsString('[PASS] Pass Check: All good', $output);
        $this->assertStringContainsString('[WARN] Warn Check: Be careful', $output);
        $this->assertStringContainsString('[FAIL] Fail Check: Broken', $output);
    }

    public function test_console_output_summary_line(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('A', 'ok'),
            CheckResult::pass('B', 'ok'),
            CheckResult::warning('C', 'warn'),
            CheckResult::fail('D', 'bad'),
            CheckResult::fail('E', 'bad'),
            CheckResult::fail('F', 'bad'),
        ]);

        $output = $report->toConsoleOutput();

        $this->assertStringContainsString('2 passed, 1 warnings, 3 failed (total: 6)', $output);
    }

    public function test_console_output_empty_report_summary(): void
    {
        $report = new DiagnosticReport([]);
        $output = $report->toConsoleOutput();

        $this->assertStringContainsString('0 passed, 0 warnings, 0 failed (total: 0)', $output);
    }

    public function test_console_output_passes_only(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('Only', 'Just fine'),
        ]);

        $output = $report->toConsoleOutput();

        $this->assertStringContainsString('[PASS] Only: Just fine', $output);
        $this->assertStringNotContainsString('[WARN]', $output);
        $this->assertStringNotContainsString('[FAIL]', $output);
        $this->assertStringContainsString('1 passed, 0 warnings, 0 failed (total: 1)', $output);
    }

    public function test_properties_are_readonly(): void
    {
        $report = new DiagnosticReport([CheckResult::pass('A', 'ok')]);

        $reflection = new \ReflectionClass($report);

        $this->assertTrue($reflection->getProperty('passed')->isReadOnly());
        $this->assertTrue($reflection->getProperty('warnings')->isReadOnly());
        $this->assertTrue($reflection->getProperty('failed')->isReadOnly());
    }

    public function test_to_array_entries_contain_expected_keys(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('Test', 'msg'),
        ]);

        $entry = $report->toArray()[0];

        $this->assertArrayHasKey('status', $entry);
        $this->assertArrayHasKey('name', $entry);
        $this->assertArrayHasKey('message', $entry);
        $this->assertCount(3, $entry);
    }

    public function test_large_number_of_results(): void
    {
        $results = [];
        for ($i = 0; $i < 50; $i++) {
            $results[] = CheckResult::pass("Pass{$i}", 'ok');
            $results[] = CheckResult::warning("Warn{$i}", 'warn');
            $results[] = CheckResult::fail("Fail{$i}", 'bad');
        }

        $report = new DiagnosticReport($results);

        $this->assertCount(50, $report->passed);
        $this->assertCount(50, $report->warnings);
        $this->assertCount(50, $report->failed);
        $this->assertFalse($report->isHealthy());
        $this->assertCount(150, $report->toArray());
    }

    public function test_mixed_status_report_with_warnings_and_failures(): void
    {
        $report = new DiagnosticReport([
            CheckResult::warning('W', 'warn'),
            CheckResult::fail('F', 'fail'),
        ]);

        $this->assertFalse($report->isHealthy());
        $this->assertCount(0, $report->passed);
        $this->assertCount(1, $report->warnings);
        $this->assertCount(1, $report->failed);
    }
}
