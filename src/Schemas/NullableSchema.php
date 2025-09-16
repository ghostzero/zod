<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Contracts\Schema as SchemaContract;

class NullableSchema extends BaseSchema
{
    public function __construct(private readonly SchemaContract $inner)
    {
    }

    public function parse(mixed $data): mixed
    {
        if ($data === null) {
            $issues = $this->runChecks($data);
            $this->assertNoIssues($issues);
            return null;
        }

        $value = $this->inner->parse($data);
        $issues = $this->runChecks($value);
        $this->assertNoIssues($issues);

        return $value;
    }

    public function getInner(): SchemaContract
    {
        return $this->inner;
    }

    public function isOptionalLike(): bool
    {
        if ($this->inner instanceof BaseSchema) {
            return $this->inner->isOptionalLike();
        }

        return false;
    }

    public function hasDefault(): bool
    {
        return $this->inner instanceof BaseSchema && $this->inner->hasDefault();
    }

    public function getDefaultValue(): mixed
    {
        if ($this->inner instanceof BaseSchema && $this->inner->hasDefault()) {
            return $this->inner->getDefaultValue();
        }

        return parent::getDefaultValue();
    }
}

