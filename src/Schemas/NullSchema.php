<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Errors\ZodIssue;

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

