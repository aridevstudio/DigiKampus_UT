<?php

namespace Tests\Unit;

use App\Models\Bootcamp;
use App\Models\BootcampLiveClassAttendance;
use App\Models\CourseMaterial;
use App\Services\BootcampFlowService;
use Tests\TestCase;

class BootcampLiveClassAttendanceTest extends TestCase
{
    public function test_pending_attendance_is_identified_without_db_access(): void
    {
        $attendance = new BootcampLiveClassAttendance([
            'status' => BootcampLiveClassAttendance::STATUS_PENDING,
        ]);

        $this->assertTrue($attendance->isPending());
        $this->assertFalse($attendance->isVerified());
        $this->assertFalse($attendance->isRejected());
    }

    public function test_final_project_material_is_not_a_prerequisite_material(): void
    {
        $service = new BootcampFlowService();
        $finalProject = new CourseMaterial([
            'tipe' => 'tugas',
            'konten' => json_encode(['is_tugas' => true, 'is_final_project' => true]),
        ]);
        $regularAssignment = new CourseMaterial([
            'tipe' => 'tugas',
            'konten' => json_encode(['is_tugas' => true, 'deadline' => '2030-01-01 12:00:00']),
        ]);

        $this->assertTrue($service->isFinalProjectMaterial($finalProject));
        $this->assertFalse($service->isFinalProjectMaterial($regularAssignment));
    }

    public function test_bootcamp_accepts_a_custom_thumbnail_path(): void
    {
        $bootcamp = new Bootcamp(['thumbnail' => 'bootcamp-thumbnails/data-analytics.webp']);

        $this->assertTrue($bootcamp->isFillable('thumbnail'));
        $this->assertSame('bootcamp-thumbnails/data-analytics.webp', $bootcamp->thumbnail);
    }

    public function test_manual_offline_check_in_is_immediately_verified(): void
    {
        $checkedInAt = now();
        $attributes = BootcampLiveClassAttendance::manualCheckInAttributes(99, 'Check-in panitia di lokasi.', $checkedInAt);

        $this->assertSame(BootcampLiveClassAttendance::STATUS_VERIFIED, $attributes['status']);
        $this->assertSame(99, $attributes['reviewed_by']);
        $this->assertSame('Check-in panitia di lokasi.', $attributes['catatan_reviewer']);
        $this->assertSame($checkedInAt, $attributes['reviewed_at']);
    }
}
