<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Services\StudentImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_headers_contain_simplified_column_names(): void
    {
        $this->assertContains('Tingkat Kelas', StudentImportService::HEADERS);
        $this->assertContains('Rombel', StudentImportService::HEADERS);
        $this->assertContains('Tahun Masuk', StudentImportService::HEADERS);

        $this->assertNotContains('Tingkat Kelas (7/8/9)', StudentImportService::HEADERS);
        $this->assertNotContains('Rombel (1/2/3/A/B/C)', StudentImportService::HEADERS);
        $this->assertNotContains('Tahun Masuk (Contoh: 2026)', StudentImportService::HEADERS);
    }

    public function test_clean_cell_handles_scientific_notation_and_excel_quotes(): void
    {
        $service = new StudentImportService;

        // Scientific notation conversion
        $this->assertEquals('121232000000000000', $service->cleanCell('1.21232E+17'));
        $this->assertEquals('121232000000000000', $service->cleanCell('1,21232E+17'));

        // Excel formula string
        $this->assertEquals('121232160048260099', $service->cleanCell('="121232160048260099"'));
        $this->assertEquals('081234567891', $service->cleanCell('="081234567891"'));

        // Excel leading single quote
        $this->assertEquals('081234567891', $service->cleanCell("'081234567891"));

        // Null and blanks
        $this->assertNull($service->cleanCell(null));
        $this->assertNull($service->cleanCell('   '));
    }

    public function test_import_with_scientific_notation_nism_converts_properly(): void
    {
        $service = new StudentImportService;

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import_').'.csv';
        $content = "NISN,NISM,Nama,JK,Kelas,Rombel,TahunMasuk,TglLahir,AlamatSiswa,HpSiswa,Ayah,Ibu,Wali,HpWali,Job,AlamatWali\n";
        $content .= "9998887772,1.21232E+17,TEST SISWA SCIENTIFIC,Laki-laki,7,1,2026,2014-01-01,Alamat Test,081211112222,Bapak Test,Ibu Test,Bapak Test,081233334444,Wiraswasta,Alamat Wali Test\n";
        file_put_contents($tempFile, $content);

        $result = $service->importFromSpreadsheet($tempFile);

        $this->assertEquals(1, $result['success_count']);

        $student = Student::where('nis', '9998887772')->first();
        $this->assertNotNull($student);
        $this->assertStringNotContainsString('E+', $student->nism);
        $this->assertEquals('121232000000000000', $student->nism);

        @unlink($tempFile);
    }

    public function test_import_from_xml_spreadsheet_xls_works(): void
    {
        $service = new StudentImportService;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'."\n";
        $xml .= '<Worksheet ss:Name="Template">'."\n";
        $xml .= '<Table>'."\n";
        $xml .= '<Row><Cell><Data ss:Type="String">NISN</Data></Cell><Cell><Data ss:Type="String">NISM</Data></Cell><Cell><Data ss:Type="String">Nama</Data></Cell><Cell><Data ss:Type="String">JK</Data></Cell><Cell><Data ss:Type="String">Kelas</Data></Cell><Cell><Data ss:Type="String">Rombel</Data></Cell><Cell><Data ss:Type="String">Tahun</Data></Cell><Cell><Data ss:Type="String">Tgl</Data></Cell><Cell><Data ss:Type="String">Alamat</Data></Cell><Cell><Data ss:Type="String">HpS</Data></Cell><Cell><Data ss:Type="String">Ayah</Data></Cell><Cell><Data ss:Type="String">Ibu</Data></Cell><Cell><Data ss:Type="String">Wali</Data></Cell><Cell><Data ss:Type="String">HpW</Data></Cell><Cell><Data ss:Type="String">Job</Data></Cell><Cell><Data ss:Type="String">AlamatW</Data></Cell></Row>'."\n";
        $xml .= '<Row><Cell><Data ss:Type="String">9998887773</Data></Cell><Cell ss:StyleID="Number0"><Data ss:Type="String">121232160048260099</Data></Cell><Cell><Data ss:Type="String">TEST SISWA XML XLS</Data></Cell><Cell><Data ss:Type="String">Laki-laki</Data></Cell><Cell><Data ss:Type="Number">7</Data></Cell><Cell><Data ss:Type="String">1</Data></Cell><Cell><Data ss:Type="Number">2026</Data></Cell><Cell><Data ss:Type="String">2014-01-01</Data></Cell><Cell><Data ss:Type="String">Alamat</Data></Cell><Cell><Data ss:Type="String">081211112222</Data></Cell><Cell><Data ss:Type="String">Ayah</Data></Cell><Cell><Data ss:Type="String">Ibu</Data></Cell><Cell><Data ss:Type="String">Wali</Data></Cell><Cell><Data ss:Type="String">081233334444</Data></Cell><Cell><Data ss:Type="String">PNS</Data></Cell><Cell><Data ss:Type="String">Alamat</Data></Cell></Row>'."\n";
        $xml .= '</Table></Worksheet></Workbook>';

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import_xml_').'.xls';
        file_put_contents($tempFile, $xml);

        $result = $service->importFromSpreadsheet($tempFile);

        $this->assertEquals(1, $result['success_count']);

        $student = Student::where('nis', '9998887773')->first();
        $this->assertNotNull($student);
        $this->assertEquals('TEST SISWA XML XLS', $student->full_name);
        $this->assertEquals('121232160048260099', $student->nism);
        $this->assertEquals(7, $student->class_level);

        @unlink($tempFile);
    }

    public function test_import_from_native_xlsx_works(): void
    {
        $service = new StudentImportService;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        foreach (StudentImportService::HEADERS as $index => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue("{$colLetter}1", $header);
        }

        // Data
        $sheet->setCellValueExplicit('A2', '9998887774', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('B2', 121232160048260099);
        $sheet->setCellValue('C2', 'TEST SISWA XLSX NATIVE');
        $sheet->setCellValue('D2', 'Laki-laki');
        $sheet->setCellValue('E2', 7);
        $sheet->setCellValue('F2', '1');
        $sheet->setCellValue('G2', 2026);
        $sheet->setCellValue('H2', '2014-01-01');
        $sheet->setCellValue('I2', 'Alamat');
        $sheet->setCellValueExplicit('J2', '081211112222', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('K2', 'Ayah');
        $sheet->setCellValue('L2', 'Ibu');
        $sheet->setCellValue('M2', 'Wali');
        $sheet->setCellValueExplicit('N2', '081233334444', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('O2', 'PNS');
        $sheet->setCellValue('P2', 'Alamat');

        $tempFile = tempnam(sys_get_temp_dir(), 'test_import_native_').'.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        $result = $service->importFromSpreadsheet($tempFile);

        $this->assertEquals(1, $result['success_count']);

        $student = Student::where('nis', '9998887774')->first();
        $this->assertNotNull($student);
        $this->assertEquals('TEST SISWA XLSX NATIVE', $student->full_name);
        $this->assertEquals('121232160048260099', $student->nism);
        $this->assertEquals(7, $student->class_level);

        @unlink($tempFile);
    }
}
