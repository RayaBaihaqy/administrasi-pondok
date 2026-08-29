<?php

namespace Tests\Unit;

use App\Models\PaymentType;
use App\Models\Student;
use App\Services\DueDateResolutionService;
use PHPUnit\Framework\TestCase;

class DueDateResolutionTest extends TestCase
{
    public function test_due_date_resolution_default_day(): void
    {
        $service = new DueDateResolutionService;

        $paymentType = new PaymentType([
            'default_due_day' => 10,
        ]);
        $student = new Student([
            'id' => 1,
        ]);

        $resolved = $service->resolve($student, $paymentType, '2026-08-01');
        $this->assertEquals('2026-08-10', $resolved->format('Y-m-d'));
    }
}
