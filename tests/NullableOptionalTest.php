<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

class NullableOptionalTest extends TestCase
{
    public function test_nullable_optional_property(): void
    {
        $schema = Z::object([
            'maybe' => Z::string()->nullable()->optional(),
        ]);

        $this->assertSame(['maybe' => null], $schema->parse(['maybe' => null]));
        $this->assertSame([], $schema->parse([]));

        $this->expectException(ZodError::class);
        $schema->parse(['maybe' => 123]);
    }
}

