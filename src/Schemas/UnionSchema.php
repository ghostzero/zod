<?php
declare(strict_types=1);

namespace Nyra\Zod\Schemas;

use Nyra\Zod\Contracts\Schema as SchemaContract;
use Nyra\Zod\Errors\ZodError;
use Nyra\Zod\Errors\ZodIssue;
use InvalidArgumentException;

class UnionSchema extends BaseSchema
{
    /** @var list<SchemaContract> */
    private array $options;

    /**
     * @param list<SchemaContract> $options
     */
    public function __construct(array $options)
    {
        if ($options === []) {
            throw new InvalidArgumentException('Union requires at least one option');
        }

        $this->options = array_values($options);
    }

    public function parse(mixed $data): mixed
    {
        $collected = [];

        foreach ($this->options as $schema) {
            try {
                $parsed = $schema->parse($data);
                $issues = $this->runChecks($parsed);
                $this->assertNoIssues($issues);
                return $parsed;
            } catch (ZodError $error) {
                $collected[] = $error;
            }
        }

        $params = [
            'errors' => array_map(
                static fn (ZodError $error): array => $error->getIssues(),
                $collected
            ),
        ];

        throw new ZodError([
            new ZodIssue('invalid_union', 'Input did not match any union member', [], $params),
        ]);
    }

    /**
     * @return list<SchemaContract>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
