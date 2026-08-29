<?php

namespace Tests\Unit;

use App\Services\DocumentService;
use PHPUnit\Framework\TestCase;

class DocumentServiceTest extends TestCase
{
    public function test_document_service_instantiation(): void
    {
        $service = new DocumentService;
        $this->assertInstanceOf(DocumentService::class, $service);
    }
}
