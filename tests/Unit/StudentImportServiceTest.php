<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\StudentImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class StudentImportServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_download_template_returns_streamed_response(): void
    {
        $service = new StudentImportService;
        $response = $service->downloadTemplate();

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function test_import_from_spreadsheet_creates_student_and_parent(): void
    {
        $service = new StudentImportService;

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import_').'.csv';
        $content = "NISN,NISM,Nama,JK,Kelas,Rombel,TahunMasuk,TglLahir,AlamatSiswa,HpSiswa,Ayah,Ibu,Wali,HpWali,Job,AlamatWali\n";
        $content .= "9998887771,12345,TEST SISWA BARU,Laki-laki,7,1,2026,2014-01-01,Alamat Test,081211112222,Bapak Test,Ibu Test,Bapak Test,081233334444,Wiraswasta,Alamat Wali Test\n";
        file_put_contents($tempFile, $content);

        $result = $service->importFromSpreadsheet($tempFile);

        $this->assertEquals(1, $result['success_count']);

        $student = Student::where('nis', '9998887771')->first();
        $this->assertNotNull($student);
        $this->assertEquals('TEST SISWA BARU', $student->full_name);
        $this->assertEquals(7, $student->class_level);
        $this->assertNotNull($student->parentProfile);
        $this->assertEquals('081233334444', $student->parentProfile->phone);

        @unlink($tempFile);
    }
}
