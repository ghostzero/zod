<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class ObjectSchemaTest extends TestCase
{
    public function test_object_parsing_with_optional_and_strip(): void
    {
        $schema = Z::object([
            'name' => Z::string()->nonempty(),
            'age' => Z::number()->int()->optional(),
        ]);

        $parsed = $schema->parse(['name' => 'Ada', 'age' => 32, 'unknown' => 'value']);
        $this->assertSame(['name' => 'Ada', 'age' => 32], $parsed);

        $this->assertSame(['name' => 'Grace'], $schema->parse(['name' => 'Grace']));
    }

    public function test_object_passthrough_retains_unknown_keys(): void
    {
        $schema = Z::object([
            'name' => Z::string(),
        ])->passthrough();

        $parsed = $schema->parse(['name' => 'Linus', 'extra' => 1]);
        $this->assertSame(['name' => 'Linus', 'extra' => 1], $parsed);
    }

    public function test_object_strict_rejects_unknown_keys(): void
    {
        $schema = Z::object([
            'name' => Z::string(),
        ])->strict();

        $this->expectException(ZodError::class);
        $schema->parse(['name' => 'Guido', 'extra' => 'nope']);
    }

    public function test_object_missing_required_key(): void
    {
        $schema = Z::object([
            'name' => Z::string(),
            'email' => Z::string(),
        ]);

        $this->expectException(ZodError::class);
        $schema->parse(['name' => 'Margaret']);
    }

    public function test_object_default_values_are_applied(): void
    {
        $schema = Z::object([
            'name' => Z::string(),
            'role' => Z::string()->default('user'),
            'createdAt' => Z::string()->default('pending')->transform('strtoupper'),
        ]);

        $parsed = $schema->parse(['name' => 'Ada']);

        $this->assertSame([
            'name' => 'Ada',
            'role' => 'user',
            'createdAt' => 'PENDING',
        ], $parsed);
    }
}

