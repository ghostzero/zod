<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class StringSchema extends BaseSchema
{
    public function parse(mixed $data): string
    {
        if (!is_string($data)) {
            throw new ZodError([
                new ZodIssue('invalid_type', 'Expected string', []),
            ]);
        }

        $issues = $this->runChecks($data);
        $this->assertNoIssues($issues);
        return $data;
    }

    public function min(int $min, string $message = 'String is too short'): self
    {
        $this->checks[] = function (mixed $value) use ($min, $message): ?ZodIssue {
            if (is_string($value) && mb_strlen($value) < $min) {
                return new ZodIssue('too_small', $message, []);
            }
            return null;
        };
        return $this;
    }

    public function max(int $max, string $message = 'String is too long'): self
    {
        $this->checks[] = function (mixed $value) use ($max, $message): ?ZodIssue {
            if (is_string($value) && mb_strlen($value) > $max) {
                return new ZodIssue('too_big', $message, []);
            }
            return null;
        };
        return $this;
    }

    public function nonempty(string $message = 'String must be nonempty'): self
    {
        return $this->min(1, $message);
    }

    public function regex(string $pattern, string $message = 'Invalid string'): self
    {
        $this->checks[] = function (mixed $value) use ($pattern, $message): ?ZodIssue {
            if (is_string($value) && preg_match($pattern, $value) !== 1) {
                return new ZodIssue('invalid_string', $message, []);
            }
            return null;
        };
        return $this;
    }

    public function email(string $message = 'Invalid email address'): self
    {
        $this->checks[] = function (mixed $value) use ($message): ?ZodIssue {
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                return new ZodIssue('invalid_string', $message, []);
            }
            return null;
        };
        return $this;
    }
}

