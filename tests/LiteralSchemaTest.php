<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
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

