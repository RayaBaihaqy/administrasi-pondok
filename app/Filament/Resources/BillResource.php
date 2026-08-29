<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\PaymentType;
use App\Models\Student;
use App\Services\PaymentService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Pembayaran';

    protected static ?string $navigationLabel = 'Tagihan';

    protected static ?string $modelLabel = 'Tagihan';

    protected static ?string $pluralModelLabel = 'Tagihan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tagihan')
                    ->schema([
                        Forms\Components\Hidden::make('parent_id'),

                        Forms\Components\Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $student = $state ? Student::find($state) : null;
                                if ($student) {
                                    $set('parent_id', $student->parent_id);

                                    $paymentTypeId = $get('payment_type_id');
                                    $academicYearId = $get('academic_year_id');
                                    if ($paymentTypeId) {
                                        $paymentType = PaymentType::find($paymentTypeId);
                                        $academicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
                                        if ($paymentType) {
                                            $pricingService = new \App\Services\PricingResolutionService;
                                            $amount = $pricingService->resolve($student, $paymentType, $academicYear);
                                            $set('amount', $amount);

                                            $dueDateService = new \App\Services\DueDateResolutionService;
                                            $dueDate = $dueDateService->resolve($student, $paymentType);
                                            $set('due_date', $dueDate->format('Y-m-d'));

                                            $isInstallment = (bool) $get('is_installment');
                                            $tenor = (int) $get('tenor_count');
                                            if ($isInstallment && $amount > 0 && $tenor > 0) {
                                                $perInstallment = (int) round($amount / $tenor);
                                                $set('installment_preview', 'Estimasi: Rp '.number_format($perInstallment, 0, ',', '.')." / bulan (selama {$tenor} bulan)");
                                            }
                                        }
                                    }
                                }
                            }),

                        Forms\Components\Select::make('payment_type_id')
                            ->label('Jenis Pembayaran')
                            ->relationship('paymentType', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (! $state) {
                                    $set('amount', null);
                                    return;
                                }

                                $paymentType = PaymentType::find($state);
                                if (! $paymentType) {
                                    return;
                                }

                                // Smart presets sesuai ketentuan sekolah/yayasan
                                if ($paymentType->code === 'AT') {
                                    $set('is_installment', true);
                                    $set('tenor_count', 10);
                                } elseif ($paymentType->code === 'PENDAFTARAN_BARU') {
                                    $set('is_installment', true);
                                    $set('tenor_count', 3);
                                }

                                $studentId = $get('student_id');
                                $academicYearId = $get('academic_year_id');
                                
                                // Default nominal langsung diambil dari master data jenis pembayaran
                                $amount = (int) ($paymentType->default_amount ?? 0);

                                if ($studentId) {
                                    $student = Student::find($studentId);
                                    $academicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
                                    if ($student) {
                                        $pricingService = new \App\Services\PricingResolutionService;
                                        $resolvedAmount = $pricingService->resolve($student, $paymentType, $academicYear);
                                        if ($resolvedAmount > 0) {
                                            $amount = $resolvedAmount;
                                        }

                                        $dueDateService = new \App\Services\DueDateResolutionService;
                                        $dueDate = $dueDateService->resolve($student, $paymentType);
                                        $set('due_date', $dueDate->format('Y-m-d'));
                                    }
                                }

                                // Otomatis isi field total tagihan dari master data (tetap dapat diubah manual oleh admin)
                                $set('amount', $amount);

                                $isInstallment = (bool) $get('is_installment');
                                $tenor = (int) $get('tenor_count');
                                if ($isInstallment && $amount > 0 && $tenor > 0) {
                                    $perInstallment = (int) round($amount / $tenor);
                                    $set('installment_preview', 'Estimasi: Rp '.number_format($perInstallment, 0, ',', '.')." / bulan (selama {$tenor} bulan)");
                                }
                            }),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::current()?->id)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $studentId = $get('student_id');
                                $paymentTypeId = $get('payment_type_id');
                                if ($studentId && $paymentTypeId) {
                                    $student = Student::find($studentId);
                                    $paymentType = PaymentType::find($paymentTypeId);
                                    $academicYear = $state ? AcademicYear::find($state) : null;
                                    if ($student && $paymentType) {
                                        $pricingService = new \App\Services\PricingResolutionService;
                                        $amount = $pricingService->resolve($student, $paymentType, $academicYear);
                                        $set('amount', $amount);
                                    }
                                }
                            })
                            ->required(),

                        Forms\Components\DatePicker::make('billing_period')
                            ->label('Periode Tagihan (Bulan/Tahun)')
                            ->default(now()->startOfMonth())
                            ->required(),

                        Forms\Components\DatePicker::make('billing_date')
                            ->label('Tanggal Diterbitkan')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->default(now()->addDays(10))
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Total Tagihan (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Tagihan')
                            ->options(Bill::STATUSES)
                            ->default(Bill::STATUS_UNPAID)
                            ->required(),

                        Forms\Components\Toggle::make('is_installment')
                            ->label('Buat Skema Cicilan (Tenor)')
                            ->helperText('Aktifkan jika total tagihan ini ingin otomatis dipecah menjadi beberapa cicilan bulanan.')
                            ->default(false)
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('tenor_count')
                            ->label('Jumlah Cicilan (Tenor)')
                            ->numeric()
                            ->default(3)
                            ->minValue(2)
                            ->maxValue(36)
                            ->prefix('X Cicilan')
                            ->required(fn (callable $get) => (bool) $get('is_installment'))
                            ->visible(fn (callable $get) => (bool) $get('is_installment'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $total = (int) $get('amount');
                                $tenor = (int) $state;
                                if ($total > 0 && $tenor > 0) {
                                    $perInstallment = (int) round($total / $tenor);
                                    $set('installment_preview', 'Estimasi: Rp '.number_format($perInstallment, 0, ',', '.')." / bulan (selama {$tenor} bulan)");
                                }
                            }),

                        Forms\Components\TextInput::make('installment_preview')
                            ->label('Estimasi Tagihan Per Bulan')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (callable $get) => (bool) $get('is_installment')),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['student', 'paymentType', 'academicYear']))
            ->columns([
                Tables\Columns\TextColumn::make('bill_number')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.class_rombel')
                    ->label('Kelas')
                    ->getStateUsing(fn (Bill $record) => $record->student?->class_rombel),

                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('billing_period')
                    ->label('Periode')
                    ->date('M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Sisa')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Bill::STATUS_OVERDUE => 'danger',
                        Bill::STATUS_UNPAID => 'warning',
                        Bill::STATUS_PAID => 'success',
                        Bill::STATUS_CANCELLED => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Bill::STATUSES[$state] ?? $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name'),

                Tables\Filters\SelectFilter::make('payment_type_id')
                    ->label('Jenis Pembayaran')
                    ->relationship('paymentType', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Bill::STATUSES),
            ])
            ->actions([
                // Action Catat Pembayaran Manual (Offline/Cash/Transfer)
                Actions\Action::make('recordPayment')
                    ->label('Bayar Manual')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Bill $record) => $record->outstanding_amount > 0 && $record->status !== Bill::STATUS_CANCELLED)
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Pembayaran (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(fn (Bill $record) => $record->outstanding_amount)
                            ->maxValue(fn (Bill $record) => $record->outstanding_amount)
                            ->helperText(fn (Bill $record) => 'Sisa tagihan: Rp '.number_format($record->outstanding_amount, 0, ',', '.')),

                        Forms\Components\Select::make('method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai / Cash',
                                'bank_transfer' => 'Transfer Bank (Manual)',
                                'qris' => 'QRIS Offline',
                            ])
                            ->default('cash')
                            ->required(),

                        Forms\Components\FileUpload::make('evidence')
                            ->label('Bukti Pembayaran (Opsional)')
                            ->directory('payment-evidences')
                            ->image()
                            ->maxSize(5120),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan / Keterangan')
                            ->rows(2),
                    ])
                    ->action(function (Bill $record, array $data) {
                        try {
                            $evidenceData = null;
                            if (! empty($data['evidence'])) {
                                $evidenceData = [
                                    'file_path' => $data['evidence'],
                                    'file_name' => basename($data['evidence']),
                                ];
                            }

                            $paymentService = new PaymentService;
                            $payment = $paymentService->recordManualPayment(
                                bill: $record,
                                amount: (int) $data['amount'],
                                method: $data['method'],
                                notes: $data['notes'] ?? null,
                                recorder: Auth::user(),
                                evidenceData: $evidenceData
                            );

                            Notification::make()
                                ->title('Pembayaran Berhasil Dicatat')
                                ->body('Pembayaran sejumlah Rp '.number_format($payment->amount, 0, ',', '.').' berhasil disimpan.')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal Mencatat Pembayaran')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // Action Download Invoice PDF
                Actions\Action::make('downloadInvoice')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (Bill $record) {
                        $documentService = new \App\Services\DocumentService;

                        return $documentService->downloadBillInvoice($record);
                    }),

                // Action Kirim WhatsApp Tagihan (Direct wa.me)
                Actions\Action::make('sendWhatsAppBill')
                    ->label('Kirim WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (Bill $record) {
                        $waService = new \App\Services\WhatsAppAutomationService;
                        $data = $waService->formatNewBillMessage($record);

                        return \App\Services\WhatsAppAutomationService::createWhatsAppUrl($data['phone_number'], $data['message']);
                    })
                    ->openUrlInNewTab(),

                Actions\EditAction::make(),
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}
