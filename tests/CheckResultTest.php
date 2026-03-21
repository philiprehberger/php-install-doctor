<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor\Tests;

use PhilipRehberger\InstallDoctor\CheckResult;
use PhilipRehberger\InstallDoctor\Status;
use PHPUnit\Framework\TestCase;

final class CheckResultTest extends TestCase
{
    public function test_pass_factory_sets_pass_status(): void
    {
        $result = CheckResult::pass('Test', 'OK');

        $this->assertSame(Status::Pass, $result->status);
    }

    public function test_warning_factory_sets_warning_status(): void
    {
        $result = CheckResult::warning('Test', 'Caution');

        $this->assertSame(Status::Warning, $result->status);
    }

    public function test_fail_factory_sets_fail_status(): void
    {
        $result = CheckResult::fail('Test', 'Bad');

        $this->assertSame(Status::Fail, $result->status);
    }

    public function test_pass_factory_preserves_name_and_message(): void
    {
        $result = CheckResult::pass('My Check', 'Everything looks good');

        $this->assertSame('My Check', $result->name);
        $this->assertSame('Everything looks good', $result->message);
    }

    public function test_warning_factory_preserves_name_and_message(): void
    {
        $result = CheckResult::warning('Disk Space', 'Running low');

        $this->assertSame('Disk Space', $result->name);
        $this->assertSame('Running low', $result->message);
    }

    public function test_fail_factory_preserves_name_and_message(): void
    {
        $result = CheckResult::fail('Connection', 'Timed out');

        $this->assertSame('Connection', $result->name);
        $this->assertSame('Timed out', $result->message);
    }

    public function test_to_array_returns_correct_structure_for_pass(): void
    {
        $result = CheckResult::pass('Check A', 'All good');
        $array = $result->toArray();

        $this->assertSame([
            'status' => 'pass',
            'name' => 'Check A',
            'message' => 'All good',
        ], $array);
    }

    public function test_to_array_returns_correct_structure_for_warning(): void
    {
        $result = CheckResult::warning('Check B', 'Be careful');
        $array = $result->toArray();

        $this->assertSame([
            'status' => 'warning',
            'name' => 'Check B',
            'message' => 'Be careful',
        ], $array);
    }

    public function test_to_array_returns_correct_structure_for_fail(): void
    {
        $result = CheckResult::fail('Check C', 'Broken');
        $array = $result->toArray();

        $this->assertSame([
            'status' => 'fail',
            'name' => 'Check C',
            'message' => 'Broken',
        ], $array);
    }

    public function test_status_enum_values(): void
    {
        $this->assertSame('pass', Status::Pass->value);
        $this->assertSame('warning', Status::Warning->value);
        $this->assertSame('fail', Status::Fail->value);
    }

    public function test_status_enum_from_string(): void
    {
        $this->assertSame(Status::Pass, Status::from('pass'));
        $this->assertSame(Status::Warning, Status::from('warning'));
        $this->assertSame(Status::Fail, Status::from('fail'));
    }

    public function test_status_enum_try_from_invalid_returns_null(): void
    {
        $this->assertNull(Status::tryFrom('invalid'));
    }

    public function test_status_enum_cases_count(): void
    {
        $cases = Status::cases();

        $this->assertCount(3, $cases);
    }

    public function test_properties_are_readonly(): void
    {
        $result = CheckResult::pass('Test', 'OK');

        $reflection = new \ReflectionClass($result);

        $this->assertTrue($reflection->getProperty('status')->isReadOnly());
        $this->assertTrue($reflection->getProperty('name')->isReadOnly());
        $this->assertTrue($reflection->getProperty('message')->isReadOnly());
    }

    public function test_factory_with_empty_strings(): void
    {
        $result = CheckResult::pass('', '');

        $this->assertSame('', $result->name);
        $this->assertSame('', $result->message);
        $this->assertSame(Status::Pass, $result->status);
    }

    public function test_factory_with_special_characters_in_message(): void
    {
        $message = 'Path: /var/log & <special> "chars"';
        $result = CheckResult::fail('Encoding', $message);

        $this->assertSame($message, $result->message);
        $this->assertSame($message, $result->toArray()['message']);
    }

    public function test_to_array_keys(): void
    {
        $result = CheckResult::pass('Test', 'OK');
        $array = $result->toArray();

        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertCount(3, $array);
    }
}
