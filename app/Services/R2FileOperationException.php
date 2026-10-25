<?php

namespace App\Services;

use RuntimeException;

class R2FileOperationException extends RuntimeException
{
    /** @param array<string, array<int, array<string, string>>> $results */
    public function __construct(public readonly array $results)
    {
        $failedCount = count($results['failed'] ?? []);
        $completedCount = count($results['deleted'] ?? $results['moved'] ?? []);

        parent::__construct("{$completedCount} R2 operations completed; {$failedCount} failed.");
    }
}
