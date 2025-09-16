<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class LiteralSchema extends BaseSchema
{
    public function __construct(private readonly mixed $value)
    {
    }

    public function parse(mixed $data): mixed
    {
        if ($data !== $this->value) {
            $expected = var_export($this->value, true);
            throw new ZodError([
                new ZodIssue('invalid_literal', "Expected literal {$expected}", []),
            ]);
        }

        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);

        return $data;
    }
}

