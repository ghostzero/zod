<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class TupleSchemaTest extends TestCase
{
    public function test_tuple_parses_all_items(): void
    {
        $schema = Z::tuple([
            Z::string(),
            Z::number()->int(),
        ]);

        $this->assertSame(['ok', 5], $schema->parse(['ok', 5]));
    }

    public function test_tuple_length_must_match(): void
    {
        $schema = Z::tuple([
            Z::string(),
        ]);

        $this->expectException(ZodError::class);
        $schema->parse(['extra', 'value']);
    }

    public function test_tuple_rest_allows_additional_items(): void
    {
        $schema = Z::tuple([
            Z::number()->int(),
        ])->rest(Z::string());

        $this->assertSame([1, 'a', 'b'], $schema->parse([1, 'a', 'b']));
    }
}

