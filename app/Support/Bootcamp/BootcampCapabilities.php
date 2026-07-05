<?php

namespace App\Support\Bootcamp;

/**
 * Per-gate capability snapshot for a (user, course) pair. Computed by
 * {@see \App\Services\BootcampFlowService::capabilities()}. Designed for
 * UI/badge consumption: each gate exposes a boolean + human-readable reason
 * string when blocked.
 *
 * IMPORTANT: This DTO summarises GATE OPENNESS for the (user, course) pair.
 * Resource-specific decisions (e.g. "can this student submit THIS assignment
 * based on its deadline") still go through {@see \App\Services\BootcampFlowService::assert()}
 * with the relevant context (material, payload, session_key).
 *
 * @author BootcampFlowEngine
 * @see   docs/Kerjain/adr-0002-bootcamp-flow-engine.md
 */
final class BootcampCapabilities
{
    /**
     * @param array<string,string> $lockedReasons map keyed by transition name
     */
    public function __construct(
        public readonly bool $canCompleteMaterial,
        public readonly bool $canSubmitAssignment,
        public readonly bool $canSubmitQuiz,
        public readonly bool $canJoinLiveClass,
        public readonly bool $canUploadLiveClassAttendance,
        public readonly bool $canAccessFinalProject,
        public readonly bool $canIssueCertificate,
        public readonly array $lockedReasons = [],
    ) {
    }

    /**
     * Lookup helper for views that don't know which property maps to which
     * transition string. Returns true if the user could potentially perform
     * that transition given an appropriate resource context.
     */
    public function can(string $transition): bool
    {
        return match ($transition) {
            BootcampTransition::COMPLETE_MATERIAL   => $this->canCompleteMaterial,
            BootcampTransition::SUBMIT_ASSIGNMENT   => $this->canSubmitAssignment,
            BootcampTransition::SUBMIT_QUIZ         => $this->canSubmitQuiz,
            BootcampTransition::JOIN_LIVE_CLASS     => $this->canJoinLiveClass,
            BootcampTransition::UPLOAD_ATTENDANCE   => $this->canUploadLiveClassAttendance,
            BootcampTransition::ACCESS_FINAL_PROJECT => $this->canAccessFinalProject,
            BootcampTransition::ISSUE_CERTIFICATE   => $this->canIssueCertificate,
            default => false,
        };
    }

    /**
     * Human-readable reason this gate is locked, or null when open.
     */
    public function lockedReasonFor(string $transition): ?string
    {
        return $this->lockedReasons[$transition] ?? null;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'can_complete_material' => $this->canCompleteMaterial,
            'can_submit_assignment' => $this->canSubmitAssignment,
            'can_submit_quiz' => $this->canSubmitQuiz,
            'can_join_live_class' => $this->canJoinLiveClass,
            'can_upload_attendance' => $this->canUploadLiveClassAttendance,
            'can_access_final_project' => $this->canAccessFinalProject,
            'can_issue_certificate' => $this->canIssueCertificate,
            'locked_reasons' => $this->lockedReasons,
        ];
    }
}
