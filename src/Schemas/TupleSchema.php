<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Contracts\Schema as SchemaContract;
use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class TupleSchema extends BaseSchema
{
    /** @var list<SchemaContract> */
    private array $items;

    private ?SchemaContract $rest = null;

    /**
     * @param list<SchemaContract> $items
     */
    public function __construct(array $items)
    {
        $this->items = array_values($items);
    }

    public function rest(SchemaContract $schema): self
    {
        $this->rest = $schema;
        return $this;
    }

    public function parse(mixed $data): array
    {
        if (!is_array($data) || array_is_list($data) === false) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected tuple (indexed array)', [])]);
        }

        $expected = count($this->items);
        $count = count($data);

        if ($this->rest === null && $count !== $expected) {
            throw new ZodError([
                new ZodIssue(
                    'invalid_tuple_length',
                    "Expected tuple length {$expected}, got {$count}",
                    [],
                    ['expected' => $expected, 'received' => $count]
                ),
            ]);
        }

        if ($this->rest !== null && $count < $expected) {
            throw new ZodError([
                new ZodIssue(
                    'invalid_tuple_length',
                    "Expected tuple length of at least {$expected}, got {$count}",
                    [],
                    ['expected' => $expected, 'received' => $count]
                ),
            ]);
        }

        $issues = [];
        $result = [];

        foreach ($this->items as $index => $schema) {
            if (!array_key_exists($index, $data)) {
                $issues[] = new ZodIssue('missing_required', "Missing tuple index {$index}", [$index]);
                continue;
            }

            try {
                $result[$index] = $schema->parse($data[$index]);
            } catch (ZodError $e) {
                foreach ($e->getIssues() as $issue) {
                    $issues[] = new ZodIssue($issue->code, $issue->message, array_merge([$index], $issue->path), $issue->params);
                }
            }
        }

        if ($this->rest !== null) {
            for ($index = $expected; $index < $count; $index++) {
                try {
                    $result[$index] = $this->rest->parse($data[$index]);
                } catch (ZodError $e) {
                    foreach ($e->getIssues() as $issue) {
                        $issues[] = new ZodIssue($issue->code, $issue->message, array_merge([$index], $issue->path), $issue->params);
                    }
                }
            }
        }

        if (!empty($issues)) {
            throw new ZodError($issues);
        }

        $result = array_values($result);
        $issues = $this->runChecks($result);
        $this->assertNoIssues($issues);

        return $result;
    }
}


