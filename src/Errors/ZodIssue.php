<?php
declare(strict_types=1);

namespace GhostZero\Zod\Errors;

class ZodIssue
{
    /**
     * @param array<int|string> $path
     * @param array<string, mixed> $params
     */
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly array $path = [],
        public readonly array $params = [],
    ) {
    }
}

