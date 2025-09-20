<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

class EnumSchemaTest extends TestCase
{
    public function test_enum_accepts_known_values(): void
    {
        $schema = Z::enum(['red', 'green', 'blue']);
        $this->assertSame('red', $schema->parse('red'));
    }

    public function test_enum_rejects_unknown_value(): void
    {
        $schema = Z::enum(['red', 'green', 'blue']);

        $this->expectException(ZodError::class);
        $schema->parse('yellow');
    }
}

