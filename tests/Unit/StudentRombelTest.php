<?php

namespace Tests\Unit;

use App\Models\Student;
use Tests\TestCase;

class StudentRombelTest extends TestCase
{
    public function test_get_rombels_for_class_returns_empty_when_null(): void
    {
        $this->assertEquals([], Student::getRombelsForClass(null));
        $this->assertEquals([], Student::getRombelsForClass(''));
    }

    public function test_get_rombels_for_class_7(): void
    {
        $rombels7 = Student::getRombelsForClass(7);
        $this->assertArrayHasKey('1', $rombels7);
        $this->assertArrayHasKey('2', $rombels7);
        $this->assertArrayHasKey('3', $rombels7);
        $this->assertEquals('Kelas 7.1', $rombels7['1']);
        $this->assertEquals('Kelas 7.2', $rombels7['2']);
        $this->assertEquals('Kelas 7.3', $rombels7['3']);
    }

    public function test_get_rombels_for_class_8_and_9(): void
    {
        $rombels8 = Student::getRombelsForClass(8);
        $this->assertArrayHasKey('1', $rombels8);
        $this->assertArrayHasKey('4', $rombels8);
        $this->assertEquals('Kelas 8.1', $rombels8['1']);
        $this->assertEquals('Kelas 8.4', $rombels8['4']);

        $rombels9 = Student::getRombelsForClass(9);
        $this->assertArrayHasKey('1', $rombels9);
        $this->assertArrayHasKey('4', $rombels9);
        $this->assertEquals('Kelas 9.1', $rombels9['1']);
        $this->assertEquals('Kelas 9.4', $rombels9['4']);
    }
}
