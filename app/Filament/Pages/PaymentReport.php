<?php

namespace App\Filament\Pages;

use App\Models\Payment;
use App\Services\ReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PaymentReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Pembayaran';

    protected static ?string $title = 'Laporan Pembayaran Transaksi';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.payment-report';

    public function table(Table $table): Table
    {
        $reportService = new ReportService;

        return $table
            ->query($reportService->getPaymentReportQuery())
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bill.bill_number')
                    ->label('No. Tagihan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.class_rombel')
                    ->label('Kelas')
                    ->getStateUsing(fn (Payment $record) => $record->student?->class_rombel),

                Tables\Columns\TextColumn::make('bill.paymentType.name')
                    ->label('Jenis Pembayaran')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal (Rp)')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->label('Sumber')
                    ->color(fn (string $state): string => match ($state) {
                        Payment::SOURCE_MIDTRANS => 'primary',
                        Payment::SOURCE_MANUAL => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Payment::SOURCES[$state] ?? $state),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Payment::STATUS_SUCCESS => 'success',
                        Payment::STATUS_PENDING => 'warning',
                        Payment::STATUS_FAILED => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Payment::STATUSES[$state] ?? $state),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Waktu Bayar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('bill.academicYear', 'name'),

                Tables\Filters\SelectFilter::make('payment_type_id')
                    ->label('Jenis Pembayaran')
                    ->relationship('bill.paymentType', 'name'),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber Pembayaran')
                    ->options(Payment::SOURCES),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Transaksi')
                    ->options(Payment::STATUSES),
            ])
            ->defaultSort('paid_at', 'desc');
    }
}
