<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class RecordSchemaTest extends TestCase
{
    public function test_record_with_value_schema(): void
    {
        $schema = Z::record(Z::number()->int());
        $parsed = $schema->parse(['a' => 1, 'b' => 2]);
        $this->assertSame(['a' => 1, 'b' => 2], $parsed);
    }

    public function test_record_enforces_key_schema(): void
    {
        $schema = Z::record(
            Z::string(),
            Z::enum(['allowed'])
        );

        $this->expectException(ZodError::class);
        $schema->parse(['other' => 'value']);
    }
}

