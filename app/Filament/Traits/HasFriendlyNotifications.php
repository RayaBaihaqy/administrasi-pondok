<?php

namespace App\Filament\Traits;

use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

trait HasFriendlyNotifications
{
    /**
     * Intercept form validation errors and display a clear, friendly notification bubble.
     */
    protected function onValidationError(ValidationException $exception): void
    {
        $messages = [];
        $errors = $exception->errors();

        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = '• '.$error;
            }
        }

        $body = ! empty($messages)
            ? implode("\n", array_unique($messages))
            : 'Mohon periksa kembali isian formulir Anda.';

        Notification::make()
            ->title('Periksa Kembali Isian Formulir')
            ->body($body)
            ->danger()
            ->persistent()
            ->send();
    }

    /**
     * Formats raw SQL / QueryException errors into human-friendly Indonesian notifications.
     */
    protected function handleDatabaseException(\Throwable $exception, string $context = 'data'): void
    {
        $body = 'Terjadi kendala saat menyimpan '.$context.'. Silakan periksa kembali isian formulir.';

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1] ?? null;
            $errorMsg = $exception->getMessage();

            // Error 1062 = Duplicate entry
            if ($errorCode === 1062 || str_contains($errorMsg, 'Duplicate entry') || str_contains($errorMsg, '1062')) {
                if (preg_match("/Duplicate entry '([^']+)' for key '([^']+)'/", $errorMsg, $matches)) {
                    $duplicateVal = $matches[1];
                    $keyName = $matches[2];

                    if (str_contains($keyName, 'phone')) {
                        $body = "Nomor telepon [{$duplicateVal}] sudah terdaftar di sistem. Mohon gunakan nomor yang berbeda.";
                    } elseif (str_contains($keyName, 'email')) {
                        $body = "Alamat email [{$duplicateVal}] sudah digunakan. Mohon gunakan email yang berbeda.";
                    } elseif (str_contains($keyName, 'nis')) {
                        $body = "NISN [{$duplicateVal}] sudah terdaftar pada siswa lain. Mohon periksa kembali.";
                    } elseif (str_contains($keyName, 'name') || str_contains($keyName, 'code')) {
                        $body = "Nama/Kode [{$duplicateVal}] sudah digunakan. Mohon gunakan nama/kode yang lain.";
                    } else {
                        $body = "Data duplikat terdeteksi [{$duplicateVal}]. Nilai ini sudah ada di sistem.";
                    }
                } else {
                    $body = 'Data yang Anda masukkan sudah terdaftar di sistem (duplikat).';
                }
            }
        }

        Notification::make()
            ->title('Gagal Menyimpan Data')
            ->body($body)
            ->danger()
            ->persistent()
            ->send();
    }
}
