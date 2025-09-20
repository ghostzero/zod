<?php
declare(strict_types=1);

namespace Nyra\Zod\Tests;

use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Z;
use PHPUnit\Framework\TestCase;

enum MyEnum: string
{
    case Case1 = 'case1';
    case Case2 = 'case2';
    case Case3 = 'case3';
    case Case4 = 'case4';
}

class EnumSchemaTest extends TestCase
{
    public function test_enum_accepts_known_values(): void
    {
        $schema = Z::enum(['red', 'green', 'blue']);
        $this->assertSame('red', $schema->parse('red'));
    }

    public function test_enum_rejects_unknown_value(): void
    {
        $schema = Z::enum(['red', 'green', 'blue']);

        $this->expectException(ZodError::class);
        $schema->parse('yellow');
    }

    public function test_enum_accepts_php_enum_instances(): void
    {
        $schema = Z::enum([
            MyEnum::Case1,
            MyEnum::Case2,
            MyEnum::Case3,
            MyEnum::Case4,
        ]);

        $this->assertSame(MyEnum::Case1->value, $schema->parse(MyEnum::Case1));
        $this->assertSame('case1', $schema->parse('case1'));
    }

    public function test_enum_rejects_unknown_php_enum_instance(): void
    {
        $schema = Z::enum([
            MyEnum::Case1,
            MyEnum::Case2,
        ]);

        $this->expectException(ZodError::class);
        $schema->parse(MyEnum::Case3);
    }
}