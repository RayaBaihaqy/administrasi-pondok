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
| Panel Administrasi | Filament 5.x |
| Portal Wali Santri | Laravel Blade + Tailwind CSS (Emerald Green Theme) |
| Database Relasional | MySQL 8.0+ / MariaDB 10.4+ |
| Payment Gateway | Midtrans Snap API & Webhook Notification Listener |
| PDF Generator Engine | DomPDF (`barryvdh/laravel-dompdf`) |
| Spreadsheet Engine | PhpSpreadsheet (`phpoffice/phpspreadsheet`) |
| Code Styling & Linter | Laravel Pint (155 files formatted) |
| Automated Test Suite | Pest PHP / PHPUnit (48 tests, 175 assertions) |

---

# 2. Arsitektur Tingkat Tinggi (High-Level Architecture)

Aplikasi dibangun menggunakan pola **Modular Monolith** dengan pemisahan domain yang jelas:

```text
┌─────────────────────────────────────────────────────────────────────────┐
│                           CLIENT / BROWSER                              │
├───────────────────────────────┬─────────────────────────────────────────┤
│ Admin & Super Admin Panel     │ Parent / Wali Portal                    │
│ URL: /admin                   │ URL: /portal                            │
│ Framework: Filament 5         │ Framework: Blade + Tailwind CSS         │
└───────────────┬───────────────┴───────────────────┬─────────────────────┘
                │                                   │
                ▼                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                       LARAVEL 12 APPLICATION LAYER                      │
├─────────────────────────────────────────────────────────────────────────┤
│ 1. Authentication Layer (Dual-Identifier: Email / Phone + Bcrypt)        │
│ 2. Authorization & RBAC (Filament Policies, Parent-Student Scoping)      │
│ 3. Domain Services:                                                     │
│    ├── BillService (Bulk generation, installment calculation, pricing)  │
│    ├── PaymentService (Midtrans Snap token, manual cashier, validation) │
│    ├── PromotionService (Academic year transition & mass promotion)     │
│    ├── StudentImportService (Native Excel .xlsx & parent provisioning)  │
│    └── WhatsAppNotificationService (wa.me payload formatting & logging) │
│ 4. Document Rendering Layer (DomPDF: Invoice & Kuitansi Sah)             │
│ 5. Audit Trail Observer (Immutable tracking on all financial mutations)  │
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

# 3. Lapisan Autentikasi & Otorisasi (Auth Architecture)

### 3.1 Dual-Identifier Login Architecture
Sistem mengimplementasikan otentikasi kustom yang menerima identifier berupa **Email** atau **Nomor Telepon / WhatsApp**:
- **Normalisasi Nomor Telepon**: Format lokal (`0812...`, `0857...`) dinormalisasi secara terpadu agar cocok dengan database (`08xx` atau format internasional `628xx`).
- **Penanganan Pesan Kesalahan**:
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

### 3.2 Otorisasi & Isolasi Multi-Tenant Portal Wali
- **Panel Admin (`/admin`)**: Dibatasi hanya untuk akun ber-role `super_admin` dan `admin`.
- **Portal Wali (`/portal`)**: Dibatasi untuk akun ber-role `parent`.
- **Query Scoping**: Portal wali murid menerapkan *global scope* yang secara ketat memfilter data siswa hanya yang memiliki relasi `parent_id == Auth::user()->parent->id`.

---

# 4. Tata Kelola Profil & Tanda Tangan Digital Bendahara

### 4.1 Arsitektur Tanda Tangan Digital & Redirect Profil
Tanda tangan digital bendahara dikelola secara dinamis melalui halaman Profile (`/admin/profile`):
- Berkas tanda tangan disimpan di disk privat/publik (`storage/app/public/signatures/...`).
- Model `User` menyediakan *helper method* untuk mengambil data tanda tangan dalam format Base64 yang siap dirender secara instan oleh DomPDF tanpa kendala URL lokal:
  ```php
  public function getSignatureBase64(): ?string
  {
      if (!$this->signature_path || !Storage::disk('public')->exists($this->signature_path)) {
          return null;
      }
      $image = Storage::disk('public')->get($this->signature_path);
      $mime = Storage::disk('public')->mimeType($this->signature_path);
      return 'data:' . $mime . ';base64,' . base64_encode($image);
  }
  ```
- **Auto-Redirect Handler**: `app/Filament/Pages/Auth/EditProfile.php` meng-override `getRedirectUrl(): ?string` untuk mengarahkan pengguna langsung kembali ke URL dashboard Filament (`filament()->getUrl()`) sesaat setelah penyimpanan profil berhasil.
- Dokumen PDF (`receipt.blade.php` dan `invoice.blade.php`) secara otomatis menggunakan tanda tangan dan nama bendahara aktif dari `User::getActiveTreasurer()`.

---

# 5. Arsitektur Domain & Layanan Inti

### 5.1 Penagihan & Matriks Tarif (Billing Architecture)
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

### 5.2 Skema Cicilan (Installment Engine - Sistem A)
- Mendukung pemecahan tagihan menjadi $N$ kali angsuran bulanan.
- *Rounding Adjustment*: Sisa pembagian desimal ditambahkan pada angsuran ke-$N$ untuk memastikan $\sum \text{Installment} \equiv \text{Total Amount}$.

### 5.3 Import Siswa Native Excel (.xlsx)
- Memanfaatkan library `phpoffice/phpspreadsheet` dengan ekstensi PHP `ext-zip`.
- Generator template menghasilkan file asli `.xlsx` dengan format sel:
  - `NumberFormat::FORMAT_NUMBER` (0 Desimal) untuk NISM, Tingkat Kelas, dan Tahun Masuk.
  - `NumberFormat::FORMAT_TEXT` untuk NISN dan Nomor Telepon.
- Anti Scientific Notation Parser: Sistem membaca nilai mentah sel (*raw value* / *formatted string*) untuk mencegah konversi otomatis menjadi `e+17`.

### 5.4 Rombel Dinamis & Transisi Tahun Ajaran
- Pemilihan rombel difilter dinamis berdasarkan tingkat kelas:
  - Tingkat 7 $\rightarrow$ Rombel 7.1, 7.2, 7.3
  - Tingkat 8 $\rightarrow$ Rombel 8.1, 8.2, 8.3, 8.4
  - Tingkat 9 $\rightarrow$ Rombel 9.1, 9.2, 9.3, 9.4
- Pembuatan tahun ajaran baru secara otomatis menghitung nama tahun dari rentang tanggal (`start_date` dan `end_date`), mengaktifkan periode baru, menaikkan tingkat kelas siswa aktif, dan meluluskan siswa tingkat 9.

### 5.5 Manajemen Mutasi Siswa (Student Mutation Engine)
- **Metode Model Domain**: `Student::mutateOut(string $reason, ?string $date)` & `Student::revertMutation()`.
- **Transisi Status & Pembatalan Tagihan**:
  ```text
  [Active Student] ──(mutateOut)──> [Withdrawn Student]
                                          │
                                          ├── Unpaid/Overdue Bills ──> Cancelled (notes appended)
                                          └── Paid Bills / Payments ──> Locked / Preserved for Audit
  ```
- **Filter Header Tabs**: `ListStudents` menggunakan `Filament\Schemas\Components\Tabs\Tab` untuk mengelompokkan `active`, `withdrawn`, `graduated`, dan `all` dengan penghitung query terindeks.

---

# 6. Integrasi Payment Gateway (Midtrans)

### 6.1 Midtrans Snap Flow
1. Wali siswa mengklik tombol "Bayar Online" di Portal Wali.
2. Server membuat transaksi pembayaran berstatus `pending` dan meminta Snap Token ke API Midtrans.
3. Popup Midtrans Snap terbuka di layar (QRIS, VA, CC, Retail).
4. Setelah transaksi diselesaikan oleh wali siswa, Midtrans mengirimkan HTTP POST Notification Webhook ke endpoint `/api/midtrans/webhook`.

### 6.2 Idempotensi Webhook Listener
- Webhook memverifikasi `signature_key` menggunakan rumus:
  $$\text{SHA512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey})$$
- Transaksi diproses dalam *Database Transaction* (`DB::transaction`) untuk mencegah *race condition* dan *double crediting*.
- Setelah status pembayaran menjadi `paid`, sisa tagihan (*outstanding balance*) diupdate dan berkas kuitansi sah diterbitkan.

---

# 7. Audit Trail & Keamanan Sistem

- **Immutable Audit Log**: Setiap mutasi pada model `Bill`, `Payment`, `Student`, dan `PaymentTypePrice` dicatat ke tabel `audit_logs` secara otomatis melalui Model Observer.
- **Pencatatan Lengkap**: Menyimpan `user_id`, `actor_role`, `action` (create/update/delete), `auditable_type`, `auditable_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, dan `user_agent`.
- **Enkripsi Kata Sandi**: Menggunakan algoritma Bcrypt dengan cost factor 12 rounds.

---

# 8. Otomasi Perintah Terjadwal (Artisan Scheduler)

| Perintah Artisan | Frekuensi / Waktu | Deskripsi |
|---|---|---|
| `php artisan spp:generate` | Bulanan (Tgl 1, 00:00) | Menerbitkan tagihan SPP bulanan otomatis untuk seluruh siswa aktif |
| `php artisan bills:detect-overdue` | Harian (00:30) | Mendeteksi dan mengubah status tagihan belum lunas yang melewati tempo menjadi `overdue` |
| `php artisan bills:send-reminders` | Harian (07:00) | Mengirimkan log pengingat tagihan H-2 jatuh tempo via WhatsApp |

---

# 9. Struktur Direktori Utama

```text
c:\Projects\administrasi-pondok\
├── app/
│   ├── Filament/               # Resource, Page, & Widget Filament Panel Admin
│   │   ├── Pages/              # Dashboard, AcademicYearManagement, Profile (EditProfile)
│   │   └── Resources/          # StudentResource, BillResource, PaymentResource, dll.
│   ├── Http/
│   │   ├── Controllers/        # PortalController, DocumentController, WebhookController
│   │   └── Middleware/         # RoleMiddleware, CheckParentAccess
│   ├── Models/                 # Eloquent Models (User, Student, Bill, Payment, dll.)
│   └── Services/               # BillService, PaymentService, ImportService, WhatsAppService
├── config/
│   ├── school.php              # Konfigurasi Identitas Madrasah, Bank BRI, Default Due Date
│   └── midtrans.php            # Konfigurasi Midtrans Server & Client Keys
├── database/
│   ├── migrations/             # 25 file migrasi database
│   └── seeders/                # DatabaseSeeder, AcademicYearSeeder, PaymentTypeSeeder, dll.
├── resources/
│   └── views/
│       ├── documents/          # Template PDF DomPDF (invoice.blade.php, receipt.blade.php)
│       └── portal/             # Blade Views Portal Wali Santri (Emerald Green)
├── routes/
│   ├── web.php                 # Web & Portal Routes
│   └── api.php                 # Midtrans Webhook Route
└── tests/                      # 48 Automated Test Suites (Pest / PHPUnit)
```