<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Closure;
use Nyra\Zod\Contracts\Schema as SchemaContract;
use InvalidArgumentException;

class LazySchema extends BaseSchema
{
    /** @var Closure():SchemaContract */
    private Closure $factory;

    /**
     * @param callable():SchemaContract $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory instanceof Closure ? $factory : Closure::fromCallable($factory);
    }

    public function parse(mixed $data): mixed
    {
        $schema = ($this->factory)();
        if (!$schema instanceof SchemaContract) {
            throw new InvalidArgumentException('Lazy factory must return a schema instance');
        }

        $parsed = $schema->parse($data);

        $issues = $this->runChecks($parsed);
        $this->assertNoIssues($issues);

        return $parsed;
    }
}

