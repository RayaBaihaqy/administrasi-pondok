<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\BillItem;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected static ?string $title = 'Buat Tagihan';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $isInstallment = ! empty($data['is_installment']);

        if ($isInstallment && ! empty($data['tenor_count']) && (int) $data['tenor_count'] > 1) {
            $student = \App\Models\Student::findOrFail($data['student_id']);
            $paymentType = \App\Models\PaymentType::findOrFail($data['payment_type_id']);
            $academicYear = ! empty($data['academic_year_id']) ? \App\Models\AcademicYear::find($data['academic_year_id']) : null;

            $billingService = new \App\Services\BillingService;
            $createdBills = $billingService->createInstallmentBills(
                student: $student,
                paymentType: $paymentType,
                totalAmount: (int) $data['amount'],
                tenorCount: (int) $data['tenor_count'],
                startBillingPeriod: $data['billing_period'] ?? null,
                initialDueDate: $data['due_date'] ?? null,
                academicYear: $academicYear,
                notes: $data['notes'] ?? null
            );

            if (! empty($createdBills)) {
                return $createdBills[0];
            }
        }

        return parent::handleRecordCreation($data);
    }

    protected function afterCreate(): void
    {
        // Auto create BillItem jika belum ada
        if ($this->record && $this->record->billItems()->count() === 0) {
            BillItem::create([
                'bill_id' => $this->record->id,
                'description' => $this->record->paymentType?->name ?? 'Tagihan',
                'quantity' => 1,
                'unit_price' => $this->record->amount,
                'subtotal' => $this->record->amount,
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
