<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

class LazySchemaTest extends TestCase
{
    public function test_recursive_lazy_schema(): void
    {
        $node = Z::lazy(function () use (&$node) {
            return Z::object([
                'value' => Z::number(),
                'next' => $node->optional()->nullable(),
            ]);
        });

        $data = [
            'value' => 1,
            'next' => [
                'value' => 2,
            ],
        ];

        $parsed = $node->parse($data);
        $this->assertSame(2, $parsed['next']['value']);

        $this->expectException(ZodError::class);
        $node->parse(['value' => 'nope']);
    }
}

