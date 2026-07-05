<?php

namespace App\Support\Bootcamp;

/**
 * Format operasional event bootcamp-style.
 *
 * Berbeda dengan {@see Course::$tipe} (gratis|berbayar); tipe_event ialah
 * dimensi tambahan untuk membedakan apakah event adalah bootcamp multi-sesi,
 * webinar single-shot, workshop hybrid, atau seminar onsite.
 *
 * Tipe ini disimpan di kolom `courses.tipe_event` (string). Setiap nilai
 * dipakai sebagai session-key namespace untuk {@see \App\Models\BootcampSession}.
 */
enum BootcampType: string
{
    case BOOTCAMP = 'bootcamp';
    case WEBINAR   = 'webinar';
    case WORKSHOP  = 'workshop';
    case SEMINAR   = 'seminar';

    /** Label lokal untuk UI badge. */
    public function label(): string
    {
        return match ($this) {
            self::BOOTCAMP => 'Bootcamp',
            self::WEBINAR   => 'Webinar',
            self::WORKSHOP  => 'Workshop',
            self::SEMINAR   => 'Seminar',
        };
    }

    /** Icon hint untuk UI. */
    public function icon(): string
    {
        return match ($this) {
            self::BOOTCAMP => 'briefcase',
            self::WEBINAR   => 'video',
            self::WORKSHOP  => 'tool',
            self::SEMINAR   => 'map-pin',
        };
    }

    /** Tone Tailwind untuk badge. */
    public function tone(): string
    {
        return match ($this) {
            self::BOOTCAMP => 'bg-blue-50 text-blue-700 border-blue-200',
            self::WEBINAR   => 'bg-purple-50 text-purple-700 border-purple-200',
            self::WORKSHOP  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::SEMINAR   => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    /** Berapa sesi default yang dibuat saat event baru dengan tipe ini? */
    public function defaultSessionCount(): int
    {
        return match ($this) {
            self::BOOTCAMP, self::WORKSHOP => 4,
            self::WEBINAR, self::SEMINAR => 1,
        };
    }

    /** Apakah event ini WAJIB memiliki link meeting per sesi? */
    public function requiresLiveClassLink(): bool
    {
        return match ($this) {
            self::BOOTCAMP, self::WEBINAR => true,
            default => false,
        };
    }

    /** Apakah event ini WAJIB memiliki ≥1 sesi? */
    public function requiresSessions(): bool
    {
        return true;
    }

    /** Apakah event ini WAJIB memiliki batas kapasitas (maks peserta)? */
    public function requiresCapacityLimit(): bool
    {
        return match ($this) {
            self::SEMINAR, self::WORKSHOP => true,
            default => false,
        };
    }

    /** Apakah event dengan tipe ini memerlukan check-in onsite? */
    public function requiresOnsiteCheckIn(): bool
    {
        return $this === self::SEMINAR;
    }

    /**
     * Lookup helper tolerant—null/string invalid → BOOTCAMP default.
     */
    public static function fromNullable(?string $value): self
    {
        return match ($value) {
            'webinar'   => self::WEBINAR,
            'workshop'  => self::WORKSHOP,
            'seminar'   => self::SEMINAR,
            default      => self::BOOTCAMP,
        };
    }
}
