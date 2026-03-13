<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Tests;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\Checks\DirectoryWritableCheck;
use PhilipRehberger\InstallDoctor\Checks\ExtensionCheck;
use PhilipRehberger\InstallDoctor\Checks\MemoryLimitCheck;
use PhilipRehberger\InstallDoctor\Checks\PhpVersionCheck;
use PhilipRehberger\InstallDoctor\DiagnosticReport;
use PhilipRehberger\InstallDoctor\Doctor;
use PhilipRehberger\InstallDoctor\Status;
use PHPUnit\Framework\TestCase;

final class DoctorTest extends TestCase
{
    public function test_diagnose_returns_report(): void
    {
        $report = Doctor::diagnose();

        $this->assertInstanceOf(DiagnosticReport::class, $report);
    }

    public function test_report_is_healthy_when_no_failures(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('Test', 'OK'),
            CheckResult::warning('Test2', 'Caution'),
        ]);

        $this->assertTrue($report->isHealthy());
    }

    public function test_php_version_check_passes_with_current_version(): void
    {
        $check = new PhpVersionCheck('8.0.0');
        $result = $check->run();

        $this->assertSame(Status::Pass, $result->status);
        $this->assertSame('PHP Version', $result->name);
    }

    public function test_php_version_check_fails_with_future_version(): void
    {
        $check = new PhpVersionCheck('99.0.0');
        $result = $check->run();

        $this->assertSame(Status::Fail, $result->status);
        $this->assertStringContainsString('does not meet minimum', $result->message);
    }

    public function test_extension_check_passes_for_loaded_extensions(): void
    {
        $check = new ExtensionCheck(['json']);
        $result = $check->run();

        $this->assertSame(Status::Pass, $result->status);
        $this->assertSame('Extensions', $result->name);
    }

    public function test_extension_check_fails_for_missing_extension(): void
    {
        $check = new ExtensionCheck(['nonexistent_extension_xyz']);
        $result = $check->run();

        $this->assertSame(Status::Fail, $result->status);
        $this->assertStringContainsString('nonexistent_extension_xyz', $result->message);
    }

    public function test_memory_limit_parse_megabytes(): void
    {
        $bytes = MemoryLimitCheck::parseMemoryLimit('256M');

        $this->assertSame(256 * 1024 * 1024, $bytes);
    }

    public function test_memory_limit_parse_gigabytes(): void
    {
        $bytes = MemoryLimitCheck::parseMemoryLimit('2G');

        $this->assertSame(2 * 1024 * 1024 * 1024, $bytes);
    }

    public function test_memory_limit_parse_unlimited(): void
    {
        $bytes = MemoryLimitCheck::parseMemoryLimit('-1');

        $this->assertSame(-1, $bytes);
    }

    public function test_report_console_output_format(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('Check A', 'All good'),
            CheckResult::fail('Check B', 'Something wrong'),
        ]);

        $output = $report->toConsoleOutput();

        $this->assertStringContainsString('PHP Install Doctor', $output);
        $this->assertStringContainsString('[PASS] Check A: All good', $output);
        $this->assertStringContainsString('[FAIL] Check B: Something wrong', $output);
        $this->assertStringContainsString('1 passed, 0 warnings, 1 failed (total: 2)', $output);
    }

    public function test_report_to_array(): void
    {
        $report = new DiagnosticReport([
            CheckResult::pass('Test', 'OK'),
            CheckResult::fail('Test2', 'Bad'),
        ]);

        $array = $report->toArray();

        $this->assertCount(2, $array);
        $this->assertSame('pass', $array[0]['status']);
        $this->assertSame('fail', $array[1]['status']);
    }

    public function test_directory_writable_check(): void
    {
        $tempDir = sys_get_temp_dir();
        $check = new DirectoryWritableCheck([$tempDir]);
        $result = $check->run();

        $this->assertSame(Status::Pass, $result->status);

        $check = new DirectoryWritableCheck(['/nonexistent/path/xyz']);
        $result = $check->run();

        $this->assertSame(Status::Fail, $result->status);
        $this->assertStringContainsString('Not found', $result->message);
    }
}
