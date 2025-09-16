<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class StringSchemaTest extends TestCase
{
    public function test_parse_string_success(): void
    {
        $s = Z::string();
        $this->assertSame('abc', $s->parse('abc'));
    }

    public function test_parse_string_invalid_type(): void
    {
        $this->expectException(ZodError::class);
        Z::string()->parse(123);
    }

    public function test_min_max_and_nonempty(): void
    {
        $s = Z::string()->min(2)->max(5);
        $this->assertSame('ab', $s->parse('ab'));
        $this->assertSame('abcde', $s->parse('abcde'));

        $this->expectException(ZodError::class);
        $s->parse('a');
    }

    public function test_nonempty(): void
    {
        $s = Z::string()->nonempty();
        $this->expectException(ZodError::class);
        $s->parse('');
    }
}

