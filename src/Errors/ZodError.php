<?php
declare(strict_types=1);

namespace Nyra\Zod\Errors;

use Exception;

class ZodError extends Exception
{
    /**
     * @param ZodIssue[] $issues
     */
    public function __construct(
        protected array $issues
    ) {
        parent::__construct('Invalid input');
    }

    /**
     * @return ZodIssue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }
}

