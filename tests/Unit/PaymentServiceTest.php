<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_payment_service_instantiation(): void
    {
        $service = new PaymentService;
        $this->assertInstanceOf(PaymentService::class, $service);
    }
}
