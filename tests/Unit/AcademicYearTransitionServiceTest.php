<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\AcademicYearTransitionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AcademicYearTransitionServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_transition_service_instantiation(): void
    {
        $service = new AcademicYearTransitionService;
        $this->assertInstanceOf(AcademicYearTransitionService::class, $service);
    }

    public function test_transition_service_handles_retained_student(): void
    {
        $service = new AcademicYearTransitionService;
        $student = Student::where('class_level', 7)->first();

        if ($student) {
            $initialClass = $student->class_level;
            $result = $service->startNewAcademicYear(
                name: '2099/2100-TEST',
                startDate: '2099-07-15',
                endDate: '2100-07-14',
                retainedStudentIds: [$student->id]
            );

            $student->refresh();
            $this->assertEquals($initialClass, $student->class_level);
            // Cleanup test academic year
            $result['new_year']->delete();
            AcademicYear::where('name', '2026/2027')->update(['is_active' => true]);
        }
    }

    public function test_transition_service_auto_generates_academic_year_name(): void
    {
        $service = new AcademicYearTransitionService;
        $result = $service->startNewAcademicYear(
            name: null,
            startDate: '2027-07-15',
            endDate: '2028-07-14'
        );

        $this->assertEquals('2027/2028', $result['new_year']->name);

        // Cleanup
        $result['new_year']->delete();
        AcademicYear::where('name', '2026/2027')->update(['is_active' => true]);
    }
}
