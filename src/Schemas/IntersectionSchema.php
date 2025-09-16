<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Contracts\Schema as SchemaContract;
use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class IntersectionSchema extends BaseSchema
{
    public function __construct(
        private readonly SchemaContract $left,
        private readonly SchemaContract $right,
    ) {
    }

    public function parse(mixed $data): mixed
    {
        $issues = [];
        $parsedLeft = null;
        $parsedRight = null;

        try {
            $parsedLeft = $this->left->parse($data);
        } catch (ZodError $error) {
            $issues = array_merge($issues, $error->getIssues());
        }

        try {
            $parsedRight = $this->right->parse($data);
        } catch (ZodError $error) {
            $issues = array_merge($issues, $error->getIssues());
        }

        if (!empty($issues)) {
            throw new ZodError($issues);
        }

        $result = $this->mergeResults($parsedLeft, $parsedRight);
        $checks = $this->runChecks($result);
        $this->assertNoIssues($checks);

        return $result;
    }

    public function isOptionalLike(): bool
    {
        return ($this->left instanceof BaseSchema && $this->left->isOptionalLike())
            || ($this->right instanceof BaseSchema && $this->right->isOptionalLike());
    }

    public function hasDefault(): bool
    {
        return ($this->left instanceof BaseSchema && $this->left->hasDefault())
            || ($this->right instanceof BaseSchema && $this->right->hasDefault());
    }

    public function getDefaultValue(): mixed
    {
        if ($this->right instanceof BaseSchema && $this->right->hasDefault()) {
            return $this->right->getDefaultValue();
        }

        if ($this->left instanceof BaseSchema && $this->left->hasDefault()) {
            return $this->left->getDefaultValue();
        }

        return parent::getDefaultValue();
    }

    private function mergeResults(mixed $left, mixed $right): mixed
    {
        if (is_array($left) && is_array($right)) {
            return array_merge($left, $right);
        }

        if (is_array($left)) {
            return $left;
        }

        return $right;
    }
}

