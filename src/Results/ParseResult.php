<?php
declare(strict_types=1);

namespace GhostZero\Zod\Results;

use GhostZero\Zod\Errors\ZodError;

class ParseResult
{
    private function __construct(
        public readonly bool $success,
        public readonly mixed $data = null,
        public readonly ?ZodError $error = null
    ) {
    }

    public static function success(mixed $data): self
    {
        return new self(true, $data, null);
    }

    public static function failure(ZodError $error): self
    {
        return new self(false, null, $error);
    }
}

