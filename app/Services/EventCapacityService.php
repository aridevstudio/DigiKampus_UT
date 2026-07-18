<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EventSeatReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/** Slot capacity for one-shot seminar/webinar events. */
class EventCapacityService
{
    private const CAPACITY_ONLY_TYPES = ['seminar', 'webinar'];
    private const RESERVATION_MINUTES = 30;

    public function isCapacityOnlyEvent(Course $course): bool
    {
        return strtolower((string) $course->kategori) === 'webinar'
            || in_array(strtolower((string) $course->tipe_event), self::CAPACITY_ONLY_TYPES, true);
    }

    public function capacityFor(Course $course): ?int
    {
        if (!$this->isCapacityOnlyEvent($course)) {
            return null;
        }

        $capacity = (int) ($course->kapasitas_maksimal ?? 0);
        if ($capacity <= 0) {
            $capacity = (int) ($course->kuota_peserta ?? 0);
        }

        return $capacity > 0 ? $capacity : null;
    }

    /** @return array{managed: bool, capacity: ?int, filled: int, remaining: ?int, is_full: bool} */
    public function availabilityFor(Course $course): array
    {
        $capacity = $this->capacityFor($course);
        if (!$this->isCapacityOnlyEvent($course)) {
            return ['managed' => false, 'capacity' => null, 'filled' => 0, 'remaining' => null, 'is_full' => false];
        }

        $filled = $this->occupiedSeats($course);
        $remaining = $capacity === null ? null : max(0, $capacity - $filled);

        return [
            'managed' => true,
            'capacity' => $capacity,
            'filled' => $filled,
            'remaining' => $remaining,
            'is_full' => $remaining === 0 && $capacity !== null,
        ];
    }

    /** Reserve one seat before redirecting the student to Midtrans. */
    public function reserveForPayment(int $courseId, int $mahasiswaId, int $paymentTransactionId): void
    {
        $course = Course::query()->lockForUpdate()->findOrFail($courseId);
        $capacity = $this->capacityFor($course);
        if (!$this->isCapacityOnlyEvent($course) || $capacity === null) {
            return;
        }

        $this->releaseExpiredForCourse($course);

        if (Enrollment::query()->where('id_mahasiswa', $mahasiswaId)->where('id_course', $courseId)->exists()) {
            throw ValidationException::withMessages(['course' => 'Anda sudah terdaftar pada event ini.']);
        }

        $existing = EventSeatReservation::query()
            ->where('id_payment_transaction', $paymentTransactionId)
            ->where('id_course', $courseId)
            ->lockForUpdate()
            ->first();
        if ($existing?->status === 'reserved') {
            return;
        }

        if ($this->occupiedSeats($course) >= $capacity) {
            throw ValidationException::withMessages([
                'course' => 'Slot ' . ucfirst((string) $course->tipe_event) . ' sudah penuh.',
            ]);
        }

        EventSeatReservation::updateOrCreate(
            ['id_payment_transaction' => $paymentTransactionId, 'id_course' => $courseId],
            [
                'id_mahasiswa' => $mahasiswaId,
                'status' => 'reserved',
                'expires_at' => now()->addMinutes(self::RESERVATION_MINUTES),
                'confirmed_at' => null,
                'released_at' => null,
            ],
        );

        $this->syncCounter($course);
    }

    /** Confirm the reservation after the matching enrollment is created. */
    public function confirmForEnrollment(int $courseId, int $mahasiswaId, int $paymentTransactionId): void
    {
        $course = Course::query()->lockForUpdate()->findOrFail($courseId);
        if (!$this->isCapacityOnlyEvent($course) || $this->capacityFor($course) === null) {
            return;
        }

        $reservation = EventSeatReservation::query()
            ->where('id_payment_transaction', $paymentTransactionId)
            ->where('id_course', $courseId)
            ->where('id_mahasiswa', $mahasiswaId)
            ->lockForUpdate()
            ->first();

        if (!$reservation) {
            // Legacy transaction made before reservations existed: still enforce
            // the limit atomically at settlement.
            $this->reserveSeatForEnrollment($course, $mahasiswaId);
            return;
        }

        if ($reservation->status === 'released') {
            throw ValidationException::withMessages([
                'course' => 'Reservasi slot event sudah kadaluarsa. Hubungi admin untuk verifikasi pembayaran.',
            ]);
        }

        $reservation->forceFill([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'expires_at' => null,
        ])->save();
        $this->syncCounter($course);
    }

    public function releaseForTransaction(int $paymentTransactionId): void
    {
        if (!Schema::hasTable('event_seat_reservations')) {
            return;
        }

        DB::transaction(function () use ($paymentTransactionId) {
            $reservations = EventSeatReservation::query()
                ->where('id_payment_transaction', $paymentTransactionId)
                ->where('status', 'reserved')
                ->get();

            foreach ($reservations as $reservation) {
                $course = Course::query()->lockForUpdate()->find($reservation->id_course);
                if (!$course) {
                    continue;
                }

                $reservation->forceFill([
                    'status' => 'released',
                    'released_at' => now(),
                ])->save();
                $this->syncCounter($course);
            }
        });
    }

    private function reserveSeatForEnrollment(Course $course, int $mahasiswaId): void
    {
        $capacity = $this->capacityFor($course);
        if ($capacity === null || Enrollment::query()->where('id_mahasiswa', $mahasiswaId)->where('id_course', $course->id_course)->count() > 1) {
            return;
        }

        if ($this->occupiedSeats($course) > $capacity) {
            throw ValidationException::withMessages(['course' => 'Slot event sudah penuh.']);
        }

        $this->syncCounter($course);
    }

    private function releaseExpiredForCourse(Course $course): void
    {
        EventSeatReservation::query()
            ->where('id_course', $course->id_course)
            ->where('status', 'reserved')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'released', 'released_at' => now()]);
    }

    private function occupiedSeats(Course $course): int
    {
        $enrollmentCount = Enrollment::query()
            ->where('id_course', $course->id_course)
            ->whereIn('status', ['aktif', 'in_progress', 'selesai'])
            ->count();
        $reservedCount = Schema::hasTable('event_seat_reservations')
            ? EventSeatReservation::query()
                ->where('id_course', $course->id_course)
                ->where('status', 'reserved')
                ->where('expires_at', '>', now())
                ->count()
            : 0;

        return $enrollmentCount + $reservedCount;
    }

    private function syncCounter(Course $course): void
    {
        $course->forceFill(['slot_terisi' => $this->occupiedSeats($course)])->save();
    }
}
