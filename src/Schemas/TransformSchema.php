<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Contracts\Schema as SchemaContract;

class TransformSchema extends BaseSchema
{
    /** @var callable(mixed):mixed */
    private $transform;

    /**
     * @param callable(mixed):mixed $transform
     */
    public function __construct(
        private readonly SchemaContract $inner,
        callable $transform
    ) {
        $this->transform = $transform;
    }

    public function parse(mixed $data): mixed
    {
        $value = $this->inner->parse($data);
        $transformed = ($this->transform)($value);

        $issues = $this->runChecks($transformed);
        $this->assertNoIssues($issues);

        return $transformed;
    }

    public function isOptionalLike(): bool
    {
        return $this->inner instanceof BaseSchema && $this->inner->isOptionalLike();
    }

    public function hasDefault(): bool
    {
        return $this->inner instanceof BaseSchema && $this->inner->hasDefault();
    }

    public function getDefaultValue(): mixed
    {
        if ($this->inner instanceof BaseSchema && $this->inner->hasDefault()) {
            $value = $this->inner->getDefaultValue();
            return ($this->transform)($value);
        }

        return parent::getDefaultValue();
    }
}

