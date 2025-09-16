<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class NeverSchema extends BaseSchema
{
    public function parse(mixed $data): never
    {
        throw new ZodError([new ZodIssue('invalid_type', 'Never type cannot be parsed', [])]);
    }
}

