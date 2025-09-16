<?php
declare(strict_types=1);

namespace GhostZero\Zod\Schemas;

use GhostZero\Zod\Errors\ZodError;
use GhostZero\Zod\Errors\ZodIssue;

class StringSchema extends BaseSchema
{
    private ?int $minLength = null;

    private ?int $maxLength = null;

    private ?string $pattern = null;

    private ?string $format = null;

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
        if ($this->minLength === null || $min > $this->minLength) {
            $this->minLength = $min;
        }

        $this->checks[] = function (mixed $value, array $path) use ($min, $message): ?ZodIssue {
            if (is_string($value) && mb_strlen($value) < $min) {
                return new ZodIssue('too_small', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function max(int $max, string $message = 'String is too long'): self
    {
        if ($this->maxLength === null || $max < $this->maxLength) {
            $this->maxLength = $max;
        }

        $this->checks[] = function (mixed $value, array $path) use ($max, $message): ?ZodIssue {
            if (is_string($value) && mb_strlen($value) > $max) {
                return new ZodIssue('too_big', $message, $path);
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
        $this->pattern = $pattern;
        $this->checks[] = function (mixed $value, array $path) use ($pattern, $message): ?ZodIssue {
            if (is_string($value) && preg_match($pattern, $value) !== 1) {
                return new ZodIssue('invalid_string', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function email(string $message = 'Invalid email address'): self
    {
        $this->format = 'email';
        $this->checks[] = function (mixed $value, array $path) use ($message): ?ZodIssue {
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                return new ZodIssue('invalid_string', $message, $path);
            }
            return null;
        };
        return $this;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }
}
