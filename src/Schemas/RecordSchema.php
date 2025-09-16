<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Contracts\Schema as SchemaContract;
use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class RecordSchema extends BaseSchema
{
    public function __construct(
        private readonly SchemaContract $valueSchema,
        private readonly ?SchemaContract $keySchema = null
    ) {
    }

    public function parse(mixed $data): array
    {
        if (!is_array($data) || ($data !== [] && array_is_list($data))) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected record (associative array)', [])]);
        }

        $issues = [];
        $result = [];

        foreach ($data as $key => $value) {
            $stringKey = is_string($key) ? $key : (string) $key;

            if ($this->keySchema !== null) {
                try {
                    $this->keySchema->parse($stringKey);
                } catch (ZodError $e) {
                    foreach ($e->getIssues() as $issue) {
                        $issues[] = new ZodIssue($issue->code, $issue->message, array_merge([$stringKey], $issue->path), $issue->params);
                    }
                    continue;
                }
            }

            try {
                $parsedValue = $this->valueSchema->parse($value);
                $result[$stringKey] = $parsedValue;
            } catch (ZodError $e) {
                foreach ($e->getIssues() as $issue) {
                    $issues[] = new ZodIssue($issue->code, $issue->message, array_merge([$stringKey], $issue->path), $issue->params);
                }
            }
        }

        $issues = array_merge($issues, $this->runChecks($result));
        $this->assertNoIssues($issues);

        return $result;
    }
}

