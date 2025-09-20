<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

class RefineTest extends TestCase
{
    public function test_refine_allows_custom_validation(): void
    {
        $schema = Z::string()->refine(fn ($value) => $value === 'ok', 'Must equal ok');
        $this->assertSame('ok', $schema->parse('ok'));

        $this->expectException(ZodError::class);
        $schema->parse('nope');
    }
}

