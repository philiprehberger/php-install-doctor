<?php

declare(strict_types=1);

namespace PhilipRehberger\InstallDoctor;

final class CheckResult
{
    private function __construct(
        public readonly Status $status,
        public readonly string $name,
        public readonly string $message,
    ) {}

    public static function pass(string $name, string $message): self
    {
        return new self(Status::Pass, $name, $message);
    }

    public static function warning(string $name, string $message): self
    {
        return new self(Status::Warning, $name, $message);
    }

    public static function fail(string $name, string $message): self
    {
        return new self(Status::Fail, $name, $message);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'name' => $this->name,
            'message' => $this->message,
        ];
    }
}
