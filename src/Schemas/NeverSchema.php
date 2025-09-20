<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Errors\ZodIssue;

class NeverSchema extends BaseSchema
{
    public function parse(mixed $data): never
    {
        throw new ZodError([new ZodIssue('invalid_type', 'Never type cannot be parsed', [])]);
    }
}

