<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\PaymentType;
use App\Models\Student;
use App\Services\PricingResolutionService;
use PHPUnit\Framework\TestCase;

class PricingResolutionTest extends TestCase
{
    public function test_pricing_fallback_to_default_amount(): void
    {
        $service = new PricingResolutionService;

        $paymentType = new PaymentType([
            'default_amount' => 500000,
        ]);
        $student = new Student([
            'id' => 1,
            'class_level' => 7,
            'rombel' => 'A',
        ]);

        // When no DB rows match, fallback to default_amount
        $academicYear = new AcademicYear;
        $academicYear->id = 1;

        $resolved = $service->resolve($student, $paymentType, $academicYear);
        $this->assertEquals(500000, $resolved);
    }
}
