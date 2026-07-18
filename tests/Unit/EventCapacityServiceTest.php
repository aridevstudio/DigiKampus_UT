<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Services\EventCapacityService;
use Tests\TestCase;

class EventCapacityServiceTest extends TestCase
{
    public function test_only_seminar_and_webinar_are_managed_as_capacity_only_events(): void
    {
        $service = app(EventCapacityService::class);

        $this->assertTrue($service->isCapacityOnlyEvent(new Course(['tipe_event' => 'seminar'])));
        $this->assertTrue($service->isCapacityOnlyEvent(new Course(['tipe_event' => 'webinar'])));
        $this->assertTrue($service->isCapacityOnlyEvent(new Course(['kategori' => 'webinar'])));
        $this->assertFalse($service->isCapacityOnlyEvent(new Course(['tipe_event' => 'bootcamp'])));
        $this->assertFalse($service->isCapacityOnlyEvent(new Course(['tipe_event' => 'workshop'])));
    }

    public function test_capacity_prefers_new_capacity_field_then_legacy_quota(): void
    {
        $service = app(EventCapacityService::class);

        $this->assertSame(40, $service->capacityFor(new Course([
            'tipe_event' => 'webinar',
            'kapasitas_maksimal' => 40,
            'kuota_peserta' => 25,
        ])));
        $this->assertSame(25, $service->capacityFor(new Course([
            'tipe_event' => 'seminar',
            'kuota_peserta' => 25,
        ])));
        $this->assertNull($service->capacityFor(new Course(['tipe_event' => 'seminar'])));
    }
}
