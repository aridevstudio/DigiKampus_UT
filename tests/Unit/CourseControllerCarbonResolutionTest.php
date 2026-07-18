<?php

namespace Tests\Unit;

use Tests\TestCase;

class CourseControllerCarbonResolutionTest extends TestCase
{
    public function test_course_controller_imports_carbon_for_legacy_live_class_schedule(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Mahasiswa/CourseController.php'));

        $this->assertMatchesRegularExpression(
            '/^use Illuminate\\\\Support\\\\Carbon;$/m',
            $source,
        );
    }
}
