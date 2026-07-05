<?php

namespace App\Support\Bootcamp;

/**
 * Computed-on-read snapshot of a user's progression on a single bootcamp course.
 * Returned by BootcampFlowService::progression(). Never persisted.
 *
 * @property-read string $state        One of BootcampState::ORDERED
 * @property-read array  $milestones   Associative list of completed milestone keys
 * @property-read array  $context      Extra debug data (attendance keys, pending materials, etc.)
 */
class BootcampProgression
{
    /**
     * @param array<string,mixed> $milestones
     * @param array<string,mixed> $context
     */
    public function __construct(
        public readonly string $state,
        public readonly array $milestones = [],
        public readonly array $context = [],
    ) {
        if (!BootcampState::isValid($state)) {
            throw new \InvalidArgumentException("Unknown bootcamp state: {$state}");
        }
    }

    public function is(string $state): bool
    {
        return $this->state === $state;
    }

    public function isAtOrAfter(string $state): bool
    {
        $currentRank = array_search($this->state, BootcampState::ORDERED, true);
        $targetRank = array_search($state, BootcampState::ORDERED, true);
        if ($currentRank === false || $targetRank === false) {
            return false;
        }
        return $currentRank >= $targetRank;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'state' => $this->state,
            'milestones' => $this->milestones,
            'context' => $this->context,
        ];
    }
}
