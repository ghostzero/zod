<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class LiteralSchemaTest extends TestCase
{
    public function test_literal(): void
    {
        $schema = Z::literal('ok');
        $this->assertSame('ok', $schema->parse('ok'));

        $this->expectException(ZodError::class);
        $schema->parse('nope');
    }
}

