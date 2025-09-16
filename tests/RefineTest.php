<?php
declare(strict_types=1);

namespace GhostZero\Zod\Tests;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Z;
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

