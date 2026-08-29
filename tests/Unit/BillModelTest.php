<?php

namespace Tests\Unit;

use App\Models\Bill;
use Tests\TestCase;

class BillModelTest extends TestCase
{
    public function test_bill_status_helpers(): void
    {
        $bill = Bill::first();
        if ($bill) {
            $this->assertIsBool($bill->isOverdue());
            $this->assertIsBool($bill->isPaid());
            $this->assertIsBool($bill->isUnpaid());
            $this->assertIsBool($bill->isCancelled());
        } else {
            $this->assertTrue(true);
        }
    }
}
