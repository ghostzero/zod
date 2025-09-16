<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Contracts\Schema as SchemaContract;
use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class ObjectSchema extends BaseSchema
{
    private const UNKNOWN_STRIP = 'strip';
    private const UNKNOWN_PASSTHROUGH = 'passthrough';
    private const UNKNOWN_STRICT = 'strict';

    /** @var array<string, SchemaContract> */
    private array $shape;

    private string $unknownStrategy = self::UNKNOWN_STRIP;

    /**
     * @param array<string, SchemaContract> $shape
     */
    public function __construct(array $shape)
    {
        $this->shape = $shape;
    }

    public function parse(mixed $data): array
    {
        if (!is_array($data) || ($data !== [] && array_is_list($data))) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected object (associative array)', [])]);
        }

        $issues = [];
        $result = [];

        foreach ($this->shape as $key => $schema) {
            if (array_key_exists($key, $data)) {
                try {
                    $parsed = $schema->parse($data[$key]);
                    $result[$key] = $parsed;
                } catch (ZodError $e) {
                    foreach ($e->getIssues() as $issue) {
                        $issues[] = new ZodIssue($issue->code, $issue->message, array_merge([$key], $issue->path), $issue->params);
                    }
                }
                continue;
            }

            if ($this->isOptionalShapeMember($schema)) {
                if ($this->hasDefaultFor($schema)) {
                    $result[$key] = $this->getDefaultFor($schema);
                }
                continue;
            }

            $issues[] = new ZodIssue('missing_required', "Missing required key '$key'", [$key]);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->shape)) {
                continue;
            }

            switch ($this->unknownStrategy) {
                case self::UNKNOWN_STRICT:
                    $issues[] = new ZodIssue('unrecognized_key', "Unrecognized key '$key'", [$key]);
                    break;
                case self::UNKNOWN_PASSTHROUGH:
                    $result[$key] = $value;
                    break;
                case self::UNKNOWN_STRIP:
                default:
                    // ignore unknown keys
                    break;
            }
        }

        $issues = array_merge($issues, $this->runChecks($result));
        $this->assertNoIssues($issues);

        return $result;
    }

    public function passthrough(): self
    {
        $this->unknownStrategy = self::UNKNOWN_PASSTHROUGH;
        return $this;
    }

    public function strict(): self
    {
        $this->unknownStrategy = self::UNKNOWN_STRICT;
        return $this;
    }

    public function strip(): self
    {
        $this->unknownStrategy = self::UNKNOWN_STRIP;
        return $this;
    }

    /**
     * @param array<string, SchemaContract> $extension
     */
    public function extend(array $extension): self
    {
        $this->shape = array_merge($this->shape, $extension);
        return $this;
    }

    private function isOptionalShapeMember(SchemaContract $schema): bool
    {
        return $schema instanceof BaseSchema && $schema->isOptionalLike();
    }

    private function hasDefaultFor(SchemaContract $schema): bool
    {
        return $schema instanceof BaseSchema && $schema->hasDefault();
    }

    private function getDefaultFor(SchemaContract $schema): mixed
    {
        /** @var BaseSchema $schema */
        return $schema->getDefaultValue();
    }
}

