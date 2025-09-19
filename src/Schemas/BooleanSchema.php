<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Errors\ZodIssue;

class BooleanSchema extends BaseSchema
{
    public function parse(mixed $data): bool
    {
        if (!is_bool($data)) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected boolean', [])]);
        }
        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);
        return $data;
    }
}

