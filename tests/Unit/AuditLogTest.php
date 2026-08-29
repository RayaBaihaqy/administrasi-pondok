<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use PHPUnit\Framework\TestCase;

class AuditLogTest extends TestCase
{
    public function test_audit_log_instantiation(): void
    {
        $log = new AuditLog;
        $this->assertInstanceOf(AuditLog::class, $log);
    }
}
