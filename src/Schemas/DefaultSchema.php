<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Contracts\Schema as SchemaContract;

class DefaultSchema extends BaseSchema
{
    /**
     * @param SchemaContract $inner
     * @param mixed $defaultValue
     */
    public function __construct(
        private readonly SchemaContract $inner,
        private readonly mixed $defaultValue,
    ) {
    }

    public function getInner(): SchemaContract
    {
        return $this->inner;
    }

    public function parse(mixed $data): mixed
    {
        $value = $this->inner->parse($data);
        $issues = $this->runChecks($value);
        $this->assertNoIssues($issues);

        return $value;
    }

    public function isOptionalLike(): bool
    {
        return true;
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function getDefaultValue(): mixed
    {
        if (is_callable($this->defaultValue)) {
            return ($this->defaultValue)();
        }

        return $this->defaultValue;
    }

    public function nullable(): SchemaContract
    {
        return new NullableSchema($this);
    }

    public function optional(): SchemaContract
    {
        return $this;
    }
}
