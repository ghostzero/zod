<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

class PreprocessSchemaTest extends TestCase
{
    public function test_preprocess_runs_before_parse(): void
    {
        $schema = Z::preprocess(
            static fn (mixed $value) => is_string($value) ? trim($value) : $value,
            Z::string()->min(3)
        );

        $this->assertSame('abc', $schema->parse('  abc '));
    }

    public function test_schema_preprocess_helper_allows_chaining(): void
    {
        $schema = Z::number()
            ->preprocess(static fn (mixed $value) => is_string($value) ? (int) $value : $value)
            ->int()
            ->min(5);

        $this->assertSame(6, $schema->parse('6'));

        $this->expectException(ZodError::class);
        $schema->parse('2');
    }
}

