<?php

namespace Tests\Unit;

use App\Services\ReportService;
use PHPUnit\Framework\TestCase;

class ReportServiceTest extends TestCase
{
    public function test_report_service_instantiation(): void
    {
        $service = new ReportService;
        $this->assertInstanceOf(ReportService::class, $service);
    }
}
