<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceSessionLimitService
{
    public const MAX_ACTIVE_DEVICES = 2;

    public function countActiveDevices(User $user): int
    {
        return $this->countActiveWebSessions((int) $user->id) + $this->countActiveApiTokens($user);
    }

    public function countActiveWebSessions(int $userId): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $activeSince = now()->subMinutes($lifetimeMinutes)->timestamp;

        return DB::table($this->sessionTable())
            ->where('user_id', $userId)
            ->where('last_activity', '>=', $activeSince)
            ->count();
    }

    public function countActiveApiTokens(User $user): int
    {
        return $user->tokens()->count();
    }

    public function hasReachedWebLoginLimit(User $user, int $maxDevices = self::MAX_ACTIVE_DEVICES): bool
    {
        return $this->countActiveDevices($user) >= $maxDevices;
    }

    public function canIssueFreshApiToken(User $user, int $maxDevices = self::MAX_ACTIVE_DEVICES): bool
    {
        return $this->countActiveWebSessions((int) $user->id) < $maxDevices;
    }

    public function bindCurrentSessionToUser(Request $request, int $userId): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table($this->sessionTable())
            ->where('id', $request->session()->getId())
            ->update([
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'last_activity' => now()->timestamp,
            ]);
    }

    public function deleteSessionById(?string $sessionId): void
    {
        if (config('session.driver') !== 'database' || blank($sessionId)) {
            return;
        }

        DB::table($this->sessionTable())
            ->where('id', $sessionId)
            ->delete();
    }

    public function limitMessage(int $maxDevices = self::MAX_ACTIVE_DEVICES): string
    {
        return "Akun sudah aktif di {$maxDevices} perangkat. Logout dari perangkat lain terlebih dahulu.";
    }

    private function sessionTable(): string
    {
        return (string) config('session.table', 'sessions');
    }
}
