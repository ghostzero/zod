<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;
use InvalidArgumentException;

class EnumSchema extends BaseSchema
{
    /** @var list<string> */
    private array $values;

    /**
     * @param list<string> $values
     */
    public function __construct(array $values)
    {
        if ($values === []) {
            throw new InvalidArgumentException('Enum requires at least one value');
        }

        $this->values = array_values($values);
    }

    public function parse(mixed $data): string
    {
        if (!is_string($data) || !in_array($data, $this->values, true)) {
            $expected = implode("', '", $this->values);
            throw new ZodError([
                new ZodIssue('invalid_enum_value', "Expected one of '{$expected}'", []),
            ]);
        }

        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);

        return $data;
    }

    /**
     * @return list<string>
     */
    public function values(): array
    {
        return $this->values;
    }
}

