<?php
declare(strict_types=1);

namespace GhostZero\Zod\Contracts;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Results\ParseResult;

interface Schema
{
    /**
     * Validate and return the parsed value or throw ZodError on failure.
     *
     * @param mixed $data
     * @return mixed
     * @throws ZodError
     */
    public function parse(mixed $data): mixed;

    /**
     * Validate and return a ParseResult with success flag.
     *
     * @param mixed $data
     */
    public function safeParse(mixed $data): ParseResult;

    /**
     * Mark schema as optional (accepts null or missing in object parsing).
     */
    public function optional(): Schema;

    /**
     * Mark schema as nullable (accepts null).
     */
    public function nullable(): Schema;
}

