<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Contracts\Schema as SchemaContract;
use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Errors\ZodIssue;
use BadMethodCallException;

class PreprocessSchema extends BaseSchema
{
    /** @var callable(mixed):mixed */
    private $preprocess;

    /**
     * @param callable(mixed):mixed $preprocess
     */
    public function __construct(callable $preprocess, private readonly SchemaContract $inner)
    {
        $this->preprocess = $preprocess;
    }

    public function getInner(): SchemaContract
    {
        return $this->inner;
    }

    public function parse(mixed $data): mixed
    {
        $processed = ($this->preprocess)($data);
        $value = $this->inner->parse($processed);

        $issues = $this->runChecks($value);
        $this->assertNoIssues($issues);

        return $value;
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
            return $this->inner->getDefaultValue();
        }

        return parent::getDefaultValue();
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ($this->inner instanceof BaseSchema && method_exists($this->inner, $name)) {
            $result = $this->inner->$name(...$arguments);
            if ($result === $this->inner) {
                return $this;
            }
            return $result;
        }

        throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
    }
}

