<?php

namespace App\Support\Bootcamp;

/**
 * Transition verbs (user actions) that BootcampFlowService::assert() validates.
 * Adding a new transition MUST also wire its guard in BootcampFlowService.
 */
final class BootcampTransition
{
    public const COMPLETE_MATERIAL = 'complete_material';
    public const SUBMIT_ASSIGNMENT = 'submit_assignment';
    public const SUBMIT_QUIZ = 'submit_quiz';
    public const JOIN_LIVE_CLASS = 'join_live_class';
    public const UPLOAD_ATTENDANCE = 'upload_attendance';
    public const ACCESS_FINAL_PROJECT = 'access_final_project';
    public const ISSUE_CERTIFICATE = 'issue_certificate';

    public const ALL = [
        self::COMPLETE_MATERIAL,
        self::SUBMIT_ASSIGNMENT,
        self::SUBMIT_QUIZ,
        self::JOIN_LIVE_CLASS,
        self::UPLOAD_ATTENDANCE,
        self::ACCESS_FINAL_PROJECT,
        self::ISSUE_CERTIFICATE,
    ];
}
