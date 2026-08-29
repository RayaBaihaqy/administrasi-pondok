<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\AcademicYear;
use App\Models\PaymentType;
use App\Models\Student;
use App\Services\BillingService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBills extends ListRecords
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Header Action: Generate Tagihan Terpadu (All / Angkatan / Rombel)
            Actions\Action::make('generateBulkBills')
                ->label('Generate Tagihan')
                ->color('warning')
                ->modalSubmitActionLabel('Terbitkan Tagihan')
                ->modalHeading('Generate Tagihan Siswa')
                ->modalDescription('Terbitkan tagihan secara massal untuk seluruh siswa, per angkatan (tingkat kelas), atau per rombel spesifik. Sistem otomatis mencegah tagihan duplikat.')
                ->form([
                    Forms\Components\Select::make('payment_type_id')
                        ->label('Jenis Pembayaran')
                        ->options(PaymentType::active()->pluck('name', 'id'))
                        ->default(fn () => PaymentType::where('code', 'SPP')->first()?->id)
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('target_type')
                        ->label('Target Siswa Penerima')
                        ->options([
                            'all' => 'Semua Siswa Aktif (Seluruh MTs)',
                            'class_level' => 'Per Angkatan / Tingkat Kelas',
                            'rombel' => 'Per Rombel Spesifik',
                        ])
                        ->default('all')
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('class_level')
                        ->label('Pilih Tingkat Kelas')
                        ->options([
                            7 => 'Kelas 7 (Tujuh)',
                            8 => 'Kelas 8 (Delapan)',
                            9 => 'Kelas 9 (Sembilan)',
                        ])
                        ->visible(fn (callable $get) => in_array($get('target_type'), ['class_level', 'rombel']))
                        ->required(fn (callable $get) => in_array($get('target_type'), ['class_level', 'rombel']))
                        ->reactive(),

                    Forms\Components\Select::make('rombel')
                        ->label('Pilih Rombel')
                        ->options(collect(Student::ROMBELS)->mapWithKeys(fn ($r) => [$r => "Kelas {$r}"]))
                        ->visible(fn (callable $get) => $get('target_type') === 'rombel')
                        ->required(fn (callable $get) => $get('target_type') === 'rombel')
                        ->searchable(),

                    Forms\Components\DatePicker::make('billing_period')
                        ->label('Periode Tagihan (Bulan/Tahun)')
                        ->default(now()->startOfMonth())
                        ->required(),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Tanggal Jatuh Tempo (Opsional)')
                        ->helperText('Kosongkan untuk menggunakan aturan tanggal jatuh tempo default jenis pembayaran.'),

                    Forms\Components\TextInput::make('custom_amount')
                        ->label('Nominal Kustom (Rp) (Opsional)')
                        ->numeric()
                        ->prefix('Rp')
                        ->helperText('Kosongkan untuk otomatis menggunakan tarif sesuai matriks harga per kelas.'),

                    Forms\Components\Toggle::make('is_installment')
                        ->label('Buat Skema Cicilan (Tenor)')
                        ->helperText('Aktifkan jika tagihan ini ingin dipecah otomatis menjadi beberapa cicilan bulanan.')
                        ->default(false)
                        ->reactive(),

                    Forms\Components\TextInput::make('tenor_count')
                        ->label('Jumlah Cicilan (Tenor)')
                        ->numeric()
                        ->default(3)
                        ->minValue(2)
                        ->maxValue(36)
                        ->prefix('X Cicilan')
                        ->required(fn (callable $get) => (bool) $get('is_installment'))
                        ->visible(fn (callable $get) => (bool) $get('is_installment')),

                    Forms\Components\TextInput::make('notes')
                        ->label('Catatan Tagihan (Opsional)')
                        ->placeholder('Contoh: Iuran modul semester ganjil / Tagihan kegiatan')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $paymentType = PaymentType::find($data['payment_type_id']);
                    $academicYear = AcademicYear::current();

                    if (! $academicYear) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Tidak ada tahun ajaran aktif yang diset!')
                            ->danger()
                            ->send();

                        return;
                    }

                    $classLevel = in_array($data['target_type'], ['class_level', 'rombel']) ? (int) $data['class_level'] : null;
                    $rombel = ($data['target_type'] === 'rombel') ? $data['rombel'] : null;
                    $customAmount = ! empty($data['custom_amount']) ? (int) $data['custom_amount'] : null;
                    $customDueDate = ! empty($data['due_date']) ? $data['due_date'] : null;
                    $isInstallment = ! empty($data['is_installment']);
                    $tenorCount = $isInstallment ? (int) ($data['tenor_count'] ?? 3) : 1;
                    $notes = $data['notes'] ?? null;

                    $billingService = new BillingService;
                    $res = $billingService->generateBulkBills(
                        paymentType: $paymentType,
                        academicYear: $academicYear,
                        billingPeriod: $data['billing_period'],
                        classLevel: $classLevel,
                        rombel: $rombel,
                        customAmount: $customAmount,
                        customDueDate: $customDueDate,
                        isInstallment: $isInstallment,
                        tenorCount: $tenorCount,
                        notes: $notes
                    );

                    $targetLabel = match ($data['target_type']) {
                        'class_level' => "Kelas {$classLevel}",
                        'rombel' => "Kelas {$classLevel}.{$rombel}",
                        default => 'Seluruh Siswa Aktif',
                    };

                    Notification::make()
                        ->title('Proses Generate Massal Selesai')
                        ->body("Target: {$targetLabel} ({$res['students_count']} Siswa) | Tagihan Dibuat: {$res['created']} | Dilewati: {$res['skipped']}".($res['errors'] > 0 ? " | Error: {$res['errors']}" : ''))
                        ->success()
                        ->send();
                }),

            // 2. Header Action: Buat Tagihan Manual
            Actions\CreateAction::make()
                ->label('Buat Tagihan Manual'),
        ];
    }
}
