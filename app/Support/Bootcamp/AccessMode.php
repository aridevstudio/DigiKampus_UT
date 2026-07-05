<?php

namespace App\Support\Bootcamp;

/**
 * Mode akses event bootcamp-style.
 *
 * USER-FACING SEMANTICS (binary, per spec 2026-07-15):
 *   ONLINE  → wajib memiliki link meeting (online_link / link_zoom / youtube_playlist)
 *   OFFLINE → wajib memiliki lokasi_event + kapasitas_maksimal
 *
 * INTERNAL CASES (kept for match-safety with existing call sites):
 *   ONSITE  → @deprecated, legacy data; semantically OFFLINE
 *   HYBRID  → @deprecated, legacy data; semantically OFFLINE
 *
 * Helper methods {@see isOnlineLike()} dan {@see isOfflineLike()} adalah
 * cara yang aman untuk branch tanpa enumerasi semua case — gunakan ini
 * daripada match langsung ketika yang dibutuhkan hanya "online-ish" vs
 * "offline-ish".
 *
 * Legacy values 'onsite' dan 'hybrid' di-collapse ke OFFLINE oleh
 * {@see fromNullable()} sehingga data lama tidak rusak saat UI/Service
 * membaca mode_event.
 */
enum AccessMode: string
{
    case ONLINE  = 'online';
    case OFFLINE = 'offline';
    case ONSITE  = 'onsite'; // @deprecated — collapsed to OFFLINE for user semantics
    case HYBRID  = 'hybrid'; // @deprecated — collapsed to OFFLINE for user semantics

    public function label(): string
    {
        return match ($this) {
            self::ONLINE           => 'Online',
            self::OFFLINE, self::ONSITE, self::HYBRID => 'Offline',
        };
    }

    public function isOnlineLike(): bool
    {
        return $this === self::ONLINE;
    }

    public function isOfflineLike(): bool
    {
        return in_array($this, [self::OFFLINE, self::ONSITE, self::HYBRID], true);
    }

    /** Apakah mode ini memerlukan link meeting? */
    public function requiresMeetingLink(): bool
    {
        return $this->isOnlineLike();
    }

    /** Apakah mode ini memerlukan alamat lokasi? */
    public function requiresLocation(): bool
    {
        return $this->isOfflineLike();
    }

    /**
     * Lookup helper tolerant — null / invalid / legacy (onsite, hybrid) → OFFLINE;
     * 'online' / 'offline' / semua nilai lain → ONLINE.
     */
    public static function fromNullable(?string $value): self
    {
        return match ($value) {
            'offline', 'onsite', 'hybrid' => self::OFFLINE,
            default => self::ONLINE,
        };
    }

    /**
     * Hanya user-facing cases (dipakai untuk dropdown UI). Jangan pakai
     * {@see cases()} di view karena akan include ONSITE/HYBRID deprecated
     * yang label-nya collapse ke "Offline" — muncul duplikat.
     *
     * @return array<int, self>
     */
    public static function userCases(): array
    {
        return [self::ONLINE, self::OFFLINE];
    }
}
