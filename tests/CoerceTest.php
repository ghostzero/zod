<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class CoerceTest extends TestCase
{
    public function test_number_coercion(): void
    {
        $schema = Z::coerce()->number()->int()->min(3);
        $this->assertSame(5, $schema->parse('5'));
    }

    public function test_boolean_coercion_handles_strings(): void
    {
        $schema = Z::coerce()->boolean();
        $this->assertTrue($schema->parse('yes'));
        $this->assertFalse($schema->parse('0'));
    }

    public function test_string_coercion_casts_scalars(): void
    {
        $schema = Z::coerce()->string()->nonempty();
        $this->assertSame('42', $schema->parse(42));
    }
}

