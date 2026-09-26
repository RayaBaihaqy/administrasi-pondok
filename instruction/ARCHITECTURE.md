# ARCHITECTURE.md — Arsitektur Sistem Administrasi & Pembayaran
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> **Technical Architecture Document**
>
> Dokumentasi arsitektur perangkat lunak, pola desain, alur data, integrasi eksternal, dan struktur teknis Sistem Administrasi & Pembayaran MTs. Miftahul 'Ulum.

---

# 1. Dokumen & Spesifikasi Teknis

| Komponen | Spesifikasi & Versi |
|---|---|
| Framework Backend | Laravel 12.x (PHP 8.2+) |
| Panel Administrasi | Filament 5.x (`/admin`) |
| Portal Wali Santri | Filament 5 Multi-Panel (`/portal` - Emerald Green Theme) |
| Database Relasional | MySQL 8.0+ / MariaDB 10.4+ |
| Payment Gateway | Midtrans Snap API & Webhook Notification Listener |
| PDF Generator Engine | DomPDF (`barryvdh/laravel-dompdf`) |
| Spreadsheet Engine | PhpSpreadsheet (`phpoffice/phpspreadsheet`) |
| Code Styling & Linter | Laravel Pint (Clean formatted) |
| Automated Test Suite | Pest PHP / PHPUnit (71 tests, 285 assertions — 100% PASS) |

---

# 2. Arsitektur Tingkat Tinggi (High-Level Architecture)

Aplikasi dibangun menggunakan pola **Modular Monolith** dengan pemisahan domain yang jelas:

```text
┌─────────────────────────────────────────────────────────────────────────┐
│                           CLIENT / BROWSER                              │
├───────────────────────────────┬─────────────────────────────────────────┤
│ Admin & Super Admin Panel     │ Parent / Wali Portal                    │
│ URL: /admin                   │ URL: /portal                            │
│ Framework: Filament 5 (Admin) │ Framework: Filament 5 (Parent Panel)    │
└───────────────┬───────────────┴───────────────────┬─────────────────────┘
                │                                   │
                ▼                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                       LARAVEL 12 APPLICATION LAYER                      │
├─────────────────────────────────────────────────────────────────────────┤
│ 1. Authentication Layer (Dual-Identifier: Email / Phone + Bcrypt)        │
│ 2. Authorization & RBAC (Singleton Super Admin, Admin Management)        │
│ 3. Error Handling Layer (HasFriendlyNotifications + lang/id validation)  │
│ 4. Domain Services:                                                     │
│    ├── BillingService (Bulk generation, installment calculation, prices)│
│    ├── PaymentService (Midtrans Snap token, manual cashier, webhook)    │
│    ├── AcademicYearTransitionService (TA transition & mass promotion)   │
│    ├── StudentImportService (Native Excel .xlsx & parent provisioning)  │
│    └── WhatsAppAutomationService (wa.me payload formatting & logging)   │
│ 5. Document Rendering Layer (DomPDF: Invoice & Kuitansi Sah)             │
│ 6. Automatic Audit Observers (All models: User, Student, Parent, etc.)  │
└───────────────────────────────────┬─────────────────────────────────────┘
                                    │
                ┌───────────────────┴───────────────────┐
                ▼                                       ▼
┌───────────────────────────────┐       ┌───────────────────────────────┐
│     MYSQL DATABASE ENGINE     │       │   EXTERNAL SERVICES / APIS    │
├───────────────────────────────┤       ├───────────────────────────────┤
│ - users & parents             │       │ - Midtrans Snap Payment API   │
│ - students & academic_years   │       │ - Midtrans Webhook Callback   │
│ - payment_types & prices      │       │ - WhatsApp Web API (wa.me)    │
│ - bills & payments            │       │ - Local Storage (Tanda Tangan)│
│ - audit_logs & whatsapp_logs  │       │                               │
└───────────────────────────────┘       └───────────────────────────────┘
```

---

# 3. Lapisan Autentikasi, Otorisasi & Manajemen Pengguna (Auth Architecture)

### 3.1 Dual-Identifier Login Architecture
Sistem mengimplementasikan otentikasi kustom yang menerima identifier berupa **Email** atau **Nomor Telepon / WhatsApp**:
- **Normalisasi Nomor Telepon**: Format lokal (`0812...`, `0857...`) dinormalisasi secara terpadu agar cocok dengan database (`08xx` atau format internasional `628xx`).
- **Penanganan Pesan Kesalahan Terisolasi**:
  ```php
  // Logika Autentikasi Terisolasi:
  if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
      $user = User::where('email', $loginInput)->first();
      if (!$user) return back()->withErrors(['email' => 'Email tidak ditemukan.']);
  } else {
      $normalizedPhone = preg_replace('/[^0-9]/', '', $loginInput);
      $user = User::where('phone', $normalizedPhone)->orWhere('phone', $loginInput)->first();
      if (!$user) return back()->withErrors(['phone' => 'Nomor telepon tidak ditemukan.']);
  }
  
  if (!Hash::check($password, $user->password)) {
      return back()->withErrors(['password' => 'Password salah.']);
  }
  ```

### 3.2 Arsitektur Singleton Super Admin & Model Guard
- **Single Super Admin Rule**: Sistem hanya mengizinkan 1 akun Super Admin utama (`superadmin@pondok.test`).
- **Model Lifecycle Hooks (`User::booted`)**:
  - `creating`: Mencegah penambahan akun ber-role `super_admin` jika sudah ada akun `super_admin`.
  - `updating`: Mencegah modifikasi/promosi role user lain menjadi `super_admin`.
  - `deleting`: Mencegah penghapusan akun ber-role `super_admin`.

### 3.3 Manajemen Akun Staf Admin (`UserResource`)
- **Navigasi**: Terletak di bawah grup **Manajemen Pengguna** -> **Kelola Admin**.
- **Otorisasi**: Eksklusif hanya dapat diakses oleh Super Admin (`canViewAny`, `canCreate`, `canEdit`, `canDelete`).
- **Filter Tabel**: Tabel query dimodifikasi khusus `where('role', User::ROLE_ADMIN)` sehingga akun Super Admin tidak tampil di tabel dan tidak dapat diubah/dihapus dari resource ini.
- **Fitur Reset Password**: Modal action khusus untuk Super Admin mengatur kata sandi baru bagi staf admin.

### 3.4 Otorisasi & Isolasi Multi-Tenant Portal Wali
- **Panel Admin (`/admin`)**: Dibatasi hanya untuk akun internal (`super_admin` dan `admin`).
- **Portal Wali (`/portal`)**: Dibatasi untuk akun ber-role `parent`.
- **Query Scoping**: Portal wali murid menerapkan *scoping* query di setiap resource (`MyBillsResource`, `MyPaymentsResource`, `MyStudentsResource`) yang secara ketat memfilter data hanya milik `parent_id == Auth::user()->parentProfile?->id`.

---

# 4. Lapisan Penanganan Error Ramah Pengguna (Friendly Error Architecture)

### 4.1 Trait `HasFriendlyNotifications`
Seluruh form resource Create & Edit mengimplementasikan trait `App\Filament\Traits\HasFriendlyNotifications`:
```php
trait HasFriendlyNotifications
{
    protected function onValidationError(ValidationException $exception): void
    {
        $errors = $exception->validator->errors()->all();
        $body = count($errors) > 1
            ? implode("\n• ", array_merge(['Mohon periksa kembali input berikut:'], $errors))
            : ($errors[0] ?? 'Terdapat kesalahan pada isian form.');

        Notification::make()
            ->title('Gagal Menyimpan Data')
            ->body($body)
            ->danger()
            ->persistent()
            ->send();
    }
}
```

### 4.2 Kamus Validasi Bahasa Indonesia (`lang/id/validation.php`)
Menyediakan pesan error terjemahan yang jelas dan manusiawi untuk setiap aturan validasi (`required`, `unique`, `email`, `numeric`, `min`, `max`, `after`, dsb.), sehingga pengguna tidak pernah melihat pesan bawaan bahasa Inggris atau teks teknis database.

---

# 5. Tata Kelola Profil & Tanda Tangan Digital Bendahara

### 5.1 Arsitektur Tanda Tangan Digital & Snapshot Historis (Document Immutability)
Tanda tangan digital bendahara dikelola secara dinamis melalui halaman Profile (`/admin/profile`):
- Berkas tanda tangan disimpan di disk privat/publik (`storage/app/public/signatures/...`).
- Model `User`, `Invoice`, dan `Receipt` menyediakan *helper method* `getSignatureBase64()` untuk mengambil data tanda tangan dalam format Base64 yang siap dirender secara instan oleh DomPDF tanpa kendala URL lokal.
- **Auto-Redirect Handler**: `app/Filament/Pages/Auth/EditProfile.php` meng-override `getRedirectUrl(): ?string` untuk mengarahkan pengguna langsung kembali ke URL dashboard Filament (`filament()->getUrl()`) sesaat setelah penyimpanan profil berhasil.
- **Historical Snapshotting (Document Immutability)**:
  - Saat `Invoice` atau `Receipt` dibuat (`creating` lifecycle hook), sistem mengunci `treasurer_name` dan `treasurer_signature_path` dari bendahara aktif saat itu ke dalam basis data.
  - Dokumen masa lalu yang terbit di era pejabat bendahara terdahulu tetap abadi dengan nama dan tanda tangan pejabat bersangkutan.

---

# 6. Arsitektur Domain & Layanan Inti

### 6.1 Penagihan & Matriks Tarif (Billing Architecture)
- **11 Pos Pembayaran Resmi**:
  1. `SPP`: Rp 75.000 / bln
  2. `PPDB`: Rp 1.190.000 (Satu Kali)
  3. `DU_TA_8_9`: Rp 440.000 (Tahunan)
  4. `ASTS_GANJIL`: Rp 75.000 (Semester Ganjil)
  5. `ASAS_GANJIL`: Rp 150.000 (Semester Ganjil)
  6. `LDKS`: Rp 450.000 (Satu Kali)
  7. `DU_GENAP`: Rp 440.000 (Semester Genap)
  8. `ASTS_GENAP`: Rp 75.000 (Semester Genap)
  9. `ASATA_PAT`: Rp 150.000 (Semester Genap)
  10. `STUDY_TOUR`: Rp 450.000 (Satu Kali)
  11. `AKHIR_TAHUN`: Rp 2.000.000 (Kelas 9)
- **Resolusi Harga Berlaku (Effective Price Resolution)**:
  ```text
  Class + Rombel Price Matrix ──> Class Price Matrix ──> Academic Year Price ──> Payment Type Default
  ```

### 6.2 Skema Cicilan (Installment Engine - Sistem A)
- Mendukung pemecahan tagihan menjadi $N$ kali angsuran bulanan.
- *Rounding Adjustment*: Sisa pembagian desimal ditambahkan pada angsuran ke-$N$ untuk memastikan $\sum \text{Installment} \equiv \text{Total Amount}$.

### 6.3 Import Siswa Native Excel (.xlsx)
- Memanfaatkan library `phpoffice/phpspreadsheet` dengan format sel terstandarisasi:
  - `NumberFormat::FORMAT_NUMBER` (0 Desimal) untuk NISM, Tingkat Kelas, dan Tahun Masuk.
  - `NumberFormat::FORMAT_TEXT` untuk NISN dan Nomor Telepon.
- Anti Scientific Notation Parser: Sistem membersihkan notasi ilmiah `e+17` dan formula Excel secara otomatis.

### 6.4 Rombel Dinamis & Transisi Tahun Ajaran
- Pemilihan rombel difilter dinamis berdasarkan tingkat kelas (Kelas 7: 7.1-7.3; Kelas 8: 8.1-8.4; Kelas 9: 9.1-9.4).
- Pembuatan tahun ajaran baru secara otomatis menghitung nama tahun dari rentang tanggal (`start_date` dan `end_date`), mengaktifkan periode baru, menaikkan tingkat kelas siswa aktif, dan meluluskan siswa tingkat 9.

### 6.5 Manajemen Mutasi Siswa (Student Mutation Engine)
- **Metode Model Domain**: `Student::mutateOut(string $reason, ?string $date)` & `Student::revertMutation()`.
- **Transisi Status & Pembatalan Tagihan**:
  ```text
  [Active Student] ──(mutateOut)──> [Withdrawn Student]
                                          │
                                          ├── Unpaid/Overdue Bills ──> Cancelled (notes appended)
                                          └── Paid Bills / Payments ──> Locked / Preserved for Audit
  ```

---

# 7. Integrasi Payment Gateway (Midtrans)

### 7.1 Midtrans Snap Flow
1. Wali siswa mengklik tombol "Bayar Online" di Portal Wali.
2. Server membuat transaksi pembayaran berstatus `pending` dan meminta Snap Token ke API Midtrans.
3. Popup Midtrans Snap terbuka di layar (QRIS, VA, CC, Retail).
4. Setelah transaksi diselesaikan oleh wali siswa, Midtrans mengirimkan HTTP POST Notification Webhook ke endpoint `/payment/callback`.

### 7.2 Idempotensi Webhook Listener
- Webhook memverifikasi `signature_key` menggunakan SHA512 signature check dengan `hash_equals()`.
- Transaksi diproses dalam *Database Transaction* (`DB::transaction`).
- Setelah status pembayaran menjadi `paid`, sisa tagihan diupdate dan bukti pembayaran WhatsApp otomatis dicatat ke log.

---

# 8. Audit Trail Otomatis via Model Observers

- **Automatic Observer Tracking**: Seluruh entitas penting diawasi oleh Model Observers (`app/Observers/`) yang didaftarkan di `AppServiceProvider`:
  - `UserObserver`: mencatat pembuatan, update, dan penghapusan akun admin.
  - `StudentObserver`: mencatat penambahan siswa, perubahan rombel, mutasi keluar, dan aktivasi kembali.
  - `ParentProfileObserver`: mencatat perubahan kontak wali murid.
  - `BillObserver`: mencatat penerbitan tagihan, revisi nominal, dan pembatalan tagihan.
  - `PaymentObserver`: mencatat transaksi pembayaran kasir manual maupun online.
  - `AcademicYearObserver` & `PaymentTypeObserver`: mencatat konfigurasi akademik dan tarif.
- **Pencatatan Lengkap**: Menyimpan `user_id`, `actor_role`, `action`, `auditable_type`, `auditable_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, dan `user_agent`.

---

# 9. Struktur Direktori Utama

```text
c:\Projects\administrasi-pondok\
├── app/
│   ├── Console/Commands/       # SPP Generator, Overdue Detect, Due Reminders
│   ├── Filament/               # Resource, Page, & Widget Filament Multi-Panel
│   │   ├── Pages/              # Dashboard, Reports, Profile (EditProfile)
│   │   ├── Parent/             # Multi-Panel Portal Khusus Orang Tua / Wali (/portal)
│   │   ├── Resources/          # UserResource, StudentResource, BillResource, dll.
│   │   └── Traits/             # HasFriendlyNotifications Trait
│   ├── Http/Controllers/       # DocumentController (PDF Publik) & PaymentController (Callback Midtrans)
│   ├── Models/                 # 17 Eloquent Models (User, Student, Bill, Payment, dll.)
│   ├── Observers/              # 7 Model Observers (Audit Trail Otomatis)
│   ├── Providers/Filament/     # AdminPanelProvider & ParentPanelProvider
│   └── Services/               # BillingService, PaymentService, ImportService, WhatsAppAutomationService
├── config/
│   ├── school.php              # Konfigurasi Identitas Madrasah, Rekening BRI & Pejabat Bendahara
│   └── midtrans.php            # Konfigurasi Midtrans Server & Client Keys
├── database/
│   ├── migrations/             # 26 file migrasi database
│   └── seeders/                # DatabaseSeeder, UserSeeder, AcademicYearSeeder, dll.
├── lang/id/                    # Terjemahan Bahasa Indonesia & Pesan Validasi
├── resources/views/
│   ├── pdf/                    # Template PDF DomPDF (invoice.blade.php, receipt.blade.php)
│   └── payment/                # View status respons Midtrans (status.blade.php)
├── routes/
│   ├── web.php                 # Web, Portal, Public PDF Docs & Midtrans Callback Route
│   └── console.php             # Schedule artisan command hooks
└── tests/                      # 71 Automated Test Suites (PHPUnit)
```