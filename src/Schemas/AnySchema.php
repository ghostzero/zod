<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Errors\ZodIssue;

class AnySchema extends BaseSchema
{
    public function parse(mixed $data): mixed
    {
        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);

        return $data;
    }
}

