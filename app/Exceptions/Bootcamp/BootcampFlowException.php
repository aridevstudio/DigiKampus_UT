<?php

namespace App\Exceptions\Bootcamp;

use RuntimeException;

/**
 * Thrown by BootcampFlowService when a transition is rejected.
 * The renderer in bootstrap/app.php converts it to either:
 *   - JSON 4xx response for API/expectsJson callers, or
 *   - back()->with('error', message) for web form submissions.
 *
 * Controllers should NOT catch and re-shape this exception unless they need
 * a non-default HTTP code or response body (e.g., 422 validation errors).
 */
class BootcampFlowException extends RuntimeException
{
    public function __construct(
        public readonly string $transition,
        public readonly int $httpStatus,
        string $message,
    ) {
        parent::__construct($message, $httpStatus);
    }

    public static function forTransition(string $transition, string $message, int $httpStatus = 403): self
    {
        return new self($transition, $httpStatus, $message);
    }
}
