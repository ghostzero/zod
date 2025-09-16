<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;
use InvalidArgumentException;

class NumberSchema extends BaseSchema
{
    private bool $int = false;

    public function parse(mixed $data): int|float
    {
        if (!is_int($data) && !is_float($data)) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected number', [])]);
        }

        if ($this->int && !is_int($data)) {
            throw new ZodError([new ZodIssue('invalid_type', 'Expected integer', [])]);
        }

        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);
        return $data;
    }

    public function min(float $min, string $message = 'Number is too small'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($min, $message): ?ZodIssue {
            if ((is_int($value) || is_float($value)) && $value < $min) {
                return new ZodIssue('too_small', $message, $path, ['minimum' => $min]);
            }
            return null;
        };
        return $this;
    }

    public function max(float $max, string $message = 'Number is too big'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($max, $message): ?ZodIssue {
            if ((is_int($value) || is_float($value)) && $value > $max) {
                return new ZodIssue('too_big', $message, $path, ['maximum' => $max]);
            }
            return null;
        };
        return $this;
    }

    public function int(string $message = 'Expected integer'): self
    {
        $this->int = true;
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if (!is_int($value)) {
                return new ZodIssue('invalid_type', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function positive(string $message = 'Expected positive number'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if ((is_int($value) || is_float($value)) && $value <= 0) {
                return new ZodIssue('too_small', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function nonnegative(string $message = 'Expected non-negative number'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if ((is_int($value) || is_float($value)) && $value < 0) {
                return new ZodIssue('too_small', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function negative(string $message = 'Expected negative number'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if ((is_int($value) || is_float($value)) && $value >= 0) {
                return new ZodIssue('too_big', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function nonpositive(string $message = 'Expected non-positive number'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if ((is_int($value) || is_float($value)) && $value > 0) {
                return new ZodIssue('too_big', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function multipleOf(float $multiple, string $message = 'Number is not a multiple of required value'): self
    {
        if ($multiple == 0.0) {
            throw new InvalidArgumentException('Multiple must be non-zero');
        }

        $this->checks[] = function (mixed $value, array $path) use ($multiple, $message): ?ZodIssue {
            if ((is_int($value) || is_float($value))) {
                $quotient = $value / $multiple;
                if (!is_finite($quotient) || abs($quotient - round($quotient)) > 1e-9) {
                    return new ZodIssue('not_multiple_of', $message, $path, ['multiple' => $multiple]);
                }
            }
            return null;
        };
        return $this;
    }

    public function finite(string $message = 'Expected finite number'): self
    {
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if (is_float($value) && !is_finite($value)) {
                return new ZodIssue('not_finite', $message, $path);
            }
            return null;
        };
        return $this;
    }
}

