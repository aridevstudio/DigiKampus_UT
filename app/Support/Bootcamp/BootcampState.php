<?php

namespace App\Support\Bootcamp;

/**
 * Single source of truth for bootcamp progression states.
 * Mirrors the 11-state spec in docs/Kerjain/adr-0002-bootcamp-flow-engine.md.
 *
 * ADIT/ATR: zero migration; states are PURELY computed-on-read from existing
 * Enrollment + MaterialProgress + QuizAttempt + BootcampLiveClassAttendance rows
 * plus the current time window. Never store this value — always re-derive.
 */
final class BootcampState
{
    public const REGISTERED = 'registered';
    public const ENROLLED = 'enrolled';
    public const LEARNING = 'learning';
    public const MODULE_IN_PROGRESS = 'module_in_progress';
    public const LIVE_CLASS_ACTIVE = 'live_class_active';
    public const LIVE_CLASS_COMPLETED = 'live_class_completed';
    public const ATTENDANCE_PENDING = 'attendance_pending';
    public const ATTENDANCE_REJECTED = 'attendance_rejected';
    public const ATTENDANCE_VERIFIED = 'attendance_verified';
    public const FINAL_PROJECT_LOCKED = 'final_project_locked';
    public const FINAL_PROJECT_UNLOCKED = 'final_project_unlocked';
    public const COMPLETED = 'completed';

    /**
     * Ordered list used to derive "is current state at-or-after N?" comparisons
     * for view badges and CTA gating. Adjust here ONLY — never hardcode order
     * elsewhere.
     */
    public const ORDERED = [
        self::REGISTERED,
        self::ENROLLED,
        self::LEARNING,
        self::MODULE_IN_PROGRESS,
        self::LIVE_CLASS_ACTIVE,
        self::LIVE_CLASS_COMPLETED,
        self::ATTENDANCE_PENDING,
        self::ATTENDANCE_REJECTED,
        self::ATTENDANCE_VERIFIED,
        self::FINAL_PROJECT_LOCKED,
        self::FINAL_PROJECT_UNLOCKED,
        self::COMPLETED,
    ];

    public static function isValid(string $state): bool
    {
        return in_array($state, self::ORDERED, true);
    }
}
