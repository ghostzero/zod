<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class NullSchema extends BaseSchema
{
    public function parse(mixed $data): null
    {
        if ($data !== null) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected null', [])]);
        }

        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);

        return null;
    }
}

