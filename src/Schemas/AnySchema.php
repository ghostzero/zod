<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodIssue;

class AnySchema extends BaseSchema
{
    public function parse(mixed $data): mixed
    {
        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);

        return $data;
    }
}

