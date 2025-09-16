<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class UnionSchemaTest extends TestCase
{
    public function test_union_success(): void
    {
        $schema = Z::union([
            Z::string(),
            Z::number()->int(),
        ]);

        $this->assertSame('abc', $schema->parse('abc'));
        $this->assertSame(5, $schema->parse(5));
    }

    public function test_union_failure_collects_issues(): void
    {
        $schema = Z::union([
            Z::string(),
            Z::number()->positive(),
        ]);

        try {
            $schema->parse(false);
            $this->fail('Expected ZodError to be thrown');
        } catch (ZodError $error) {
            $issues = $error->getIssues();
            $this->assertSame('invalid_union', $issues[0]->code);
            $this->assertArrayHasKey('errors', $issues[0]->params);
            $this->assertGreaterThanOrEqual(1, count($issues[0]->params['errors']));
        }
    }
}

