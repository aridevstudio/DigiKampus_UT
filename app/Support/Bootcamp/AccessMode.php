<?php

namespace App\Support\Bootcamp;

/**
 * Mode akses event bootcamp-style.
 *
 *   ONLINE   → wajib memiliki link Zoom / Meet
 *   ONSITE   → wajib memiliki lokasi_event + kapasitas
 *   HYBRID   → kombinasi keduanya (link meeting + lokasi)
 */
enum AccessMode: string
{
    case ONLINE = 'online';
    case ONSITE = 'onsite';
    case HYBRID = 'hybrid';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::ONSITE => 'Onsite',
            self::HYBRID => 'Hybrid',
        };
    }

    /** Apakah mode ini memerlukan link meeting per sesi? */
    public function requiresMeetingLink(): bool
    {
        return match ($this) {
            self::ONLINE, self::HYBRID => true,
            self::ONSITE => false,
        };
    }

    /** Apakah mode ini memerlukan alamat lokasi? */
    public function requiresLocation(): bool
    {
        return match ($this) {
            self::ONSITE, self::HYBRID => true,
            self::ONLINE => false,
        };
    }

    public static function fromNullable(?string $value): self
    {
        return match ($value) {
            'onsite' => self::ONSITE,
            'hybrid' => self::HYBRID,
            default   => self::ONLINE,
        };
    }
}
