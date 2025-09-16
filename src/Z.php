<?php
declare(strict_types=1);

namespace GhostZero\Zod;

use GhostZero\Zod\Contracts\Schema;
use GhostZero\Zod\Schemas\AnySchema;
use GhostZero\Zod\Schemas\ArraySchema;
use GhostZero\Zod\Schemas\BaseSchema;
use GhostZero\Zod\Schemas\BooleanSchema;
use GhostZero\Zod\Schemas\EnumSchema;
use GhostZero\Zod\Schemas\IntersectionSchema;
use GhostZero\Zod\Schemas\LazySchema;
use GhostZero\Zod\Schemas\LiteralSchema;
use GhostZero\Zod\Schemas\NeverSchema;
use GhostZero\Zod\Schemas\NullSchema;
use GhostZero\Zod\Schemas\NumberSchema;
use GhostZero\Zod\Schemas\ObjectSchema;
use GhostZero\Zod\Schemas\PreprocessSchema;
use GhostZero\Zod\Schemas\RecordSchema;
use GhostZero\Zod\Schemas\StringSchema;
use GhostZero\Zod\Schemas\TupleSchema;
use GhostZero\Zod\Schemas\UnionSchema;
use GhostZero\Zod\Schemas\UnknownSchema;

class Z
{
    public static function string(): StringSchema
    {
        return new StringSchema();
    }

    public static function number(): NumberSchema
    {
        return new NumberSchema();
    }

    public static function boolean(): BooleanSchema
    {
        return new BooleanSchema();
    }

    public static function any(): AnySchema
    {
        return new AnySchema();
    }

    public static function unknown(): UnknownSchema
    {
        return new UnknownSchema();
    }

    public static function never(): NeverSchema
    {
        return new NeverSchema();
    }

    public static function null(): NullSchema
    {
        return new NullSchema();
    }

    public static function literal(mixed $value): LiteralSchema
    {
        return new LiteralSchema($value);
    }

    /**
     * @param list<string> $values
     */
    public static function enum(array $values): EnumSchema
    {
        return new EnumSchema($values);
    }

    /**
     * @param Schema[] $schemas
     */
    public static function union(array $schemas): UnionSchema
    {
        return new UnionSchema($schemas);
    }

    /**
     * @param list<Schema> $schemas
     */
    public static function tuple(array $schemas): TupleSchema
    {
        return new TupleSchema($schemas);
    }

    public static function intersection(Schema $left, Schema $right): IntersectionSchema
    {
        return new IntersectionSchema($left, $right);
    }

    /**
     * @param Schema $element
     */
    public static function array(Schema $element): ArraySchema
    {
        return new ArraySchema($element);
    }

    /**
     * @param array<string, Schema> $shape
     */
    public static function object(array $shape): ObjectSchema
    {
        return new ObjectSchema($shape);
    }

    public static function record(Schema $value, ?Schema $key = null): RecordSchema
    {
        return new RecordSchema($value, $key);
    }

    /**
     * @param callable():Schema $factory
     */
    public static function lazy(callable $factory): LazySchema
    {
        return new LazySchema($factory);
    }

    /**
     * @param callable(mixed):mixed $preprocess
     */
    public static function preprocess(callable $preprocess, Schema $schema): Schema
    {
        if ($schema instanceof BaseSchema) {
            return $schema->preprocess($preprocess);
        }

        return new PreprocessSchema($preprocess, $schema);
    }

    public static function coerce(): Coerce
    {
        return new Coerce();
    }
}

