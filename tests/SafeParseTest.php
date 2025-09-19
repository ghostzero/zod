<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

class SafeParseTest extends TestCase
{
    public function test_safe_parse_success_and_failure(): void
    {
        $schema = Z::string();
        $success = $schema->safeParse('value');
        $this->assertTrue($success->success);
        $this->assertSame('value', $success->data);

        $failure = $schema->safeParse(123);
        $this->assertFalse($failure->success);
        $this->assertNotNull($failure->error);
    }
}

