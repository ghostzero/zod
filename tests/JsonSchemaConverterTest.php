<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Z;
use PHPUnit\Framework\TestCase;

class JsonSchemaConverterTest extends TestCase
{
    public function test_openai_schema_example(): void
    {
        $schema = Z::object([
            'name' => Z::string(),
            'summary' => Z::string()->max(100),
            'description' => Z::string(),
        ]);

        $json = Z::jsonSchema($schema);

        $this->assertSame([
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'summary' => ['type' => 'string', 'maxLength' => 100],
                'description' => ['type' => 'string'],
            ],
            'additionalProperties' => false,
            'required' => ['name', 'summary', 'description'],
        ], $json);
    }

    public function test_optional_nullable_and_defaults_are_reflected(): void
    {
        $schema = Z::object([
            'id' => Z::number()->int()->positive(),
            'title' => Z::string()->optional()->nullable(),
            'status' => Z::enum(['draft', 'published'])->default('draft'),
            'tags' => Z::array(Z::string()->nonempty())->nonempty()->optional(),
        ]);

        $json = Z::jsonSchema($schema);

        $this->assertSame([
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'exclusiveMinimum' => 0.0,
                ],
                'title' => [
                    'type' => ['string', 'null'],
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['draft', 'published'],
                    'default' => 'draft',
                ],
                'tags' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'minLength' => 1,
                    ],
                    'minItems' => 1,
                ],
            ],
            'additionalProperties' => false,
            'required' => ['id'],
        ], $json);
    }
}
