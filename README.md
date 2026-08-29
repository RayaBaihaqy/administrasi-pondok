# 🕌 Sistem Informasi Administrasi & Pembayaran Yayasan Perguruan Islam Miftahul 'Ulum

Sistem Informasi Manajemen Penagihan, Pembayaran Online (Midtrans) & Kasir Offline, Rekapitulasi Laporan Keuangan, Invoice & Kuitansi PDF Resmi Berstempel, Integrasi WhatsApp, serta Portal Wali Siswa terpadu untuk **Yayasan Perguruan Islam Miftahul 'Ulum (Jenjang MTs. Miftahul 'Ulum)**.

Sistem ini dibangun dengan arsitektur **Modular Monolith Enterprise** menggunakan **Laravel 12**, **Filament v5**, **Livewire 3**, **Midtrans Payment Gateway API**, dan **DomPDF Engine**.

---

## 📋 Daftar Isi
1. [Spesifikasi & Tech Stack](#-spesifikasi--tech-stack)
2. [Fitur Utama & Keunggulan Sistem](#-fitur-utama--keunggulan-sistem)
3. [Format Penomoran Dokumen & File](#-format-penomoran-dokumen--file)
4. [Akses Panel & Kredensial Login Demo](#-akses-panel--kredensial-login-demo)
5. [Struktur Direktori & Penjelasan Lengkap Setiap File](#-struktur-direktori--penjelasan-lengkap-setiap-file)
6. [Skema Basis Data (18 Tabel Utama)](#-skema-basis-data-18-tabel-utama)
7. [Alur Logika Bisnis & Fitur Unggulan](#-alur-logika-bisnis--fitur-unggulan)
8. [Panduan Instalasi & Pengoperasian](#-panduan-instalasi--pengoperasian)
9. [Perintah Artisan (CLI Commands)](#-perintah-artisan-cli-commands)
10. [Pengujian Otomatis (PHPUnit Test Suite)](#-pengujian-otomatis-phpunit-test-suite)

---

## 🛠 Spesifikasi & Tech Stack

| Komponen | Teknologi | Versi | Fungsi & Peranan |
|---|---|---|---|
| **Core Framework** | Laravel | `^12.0` | Framework PHP Modern & Backend Engine |
| **Runtime Engine** | PHP CLI | `8.4` | Interpreter server backend utama |
| **Admin & User UI** | Filament | `5.0` | Multi-panel Staff/Admin (`/admin`) & Portal Wali (`/portal`) |
| **Reactivity Engine** | Livewire & SPA | `^3.0` | Single Page Application Mode Navigation (`->spa()`) |
| **Payment Gateway** | Midtrans PHP SDK | `^2.6` | Snap Token & Callback Verification (SHA512 Signature) |
| **PDF Document** | DomPDF | `^3.1` | Generasi Invoice, Kuitansi Berstempel & Rekapitulasi PDF |
| **Spreadsheet System**| Pure RFC-4180 CSV | Standard | Export/Import Data Siswa dengan UTF-8 BOM untuk MS Excel |
| **Database** | MySQL | `>= 8.0` | Relational Database Storage dengan Indexing Optimal |
| **Testing Suite** | PHPUnit | `^11.5` | Automated Unit & Feature Testing (19/19 Passing - 100%) |

---

## 🌟 Fitur Utama & Keunggulan Sistem

### 1. Dual Panel Access (Multi-Tenant Role Base)
* **Admin Staff Panel (`/admin`)**:
  * **Super Admin (*Kepala Bendahara*)**: Hak akses penuh mencakup Master Beasiswa Tunai, Log Audit Sistem, Kenaikan Kelas Massal, Reset Tahun Ajaran, dan Konfigurasi Tarif.
  * **Admin (*Operator Keuangan*)**: Pengelolaan Data Siswa, Import Excel, Penerbitan Tagihan, Kasir Pembayaran Tunai, Laporan Keuangan, dan Pengiriman WhatsApp.
* **Parent Portal Panel (`/portal`)**:
  * Dirancang khusus untuk **Wali Siswa** dengan tema Emerald Green yang bersih dan responsif.
  * **Struktur Navigasi Bersih (3 Menu Utama)**:
    1. 📊 **Dashboard**: 2 Kartu Ringkasan (*Total Tunggakan Tagihan* & *Jumlah Anak Terdaftar*) serta Tabel Interaktif Tagihan Aktif yang dilengkapi tombol **Bayar Online (Midtrans Snap)** dan **Unduh Invoice PDF**.
    2. 👦 **Anak Saya**: Menampilkan profil lengkap anak kandung (Nama, NISN, Kelas, Rombel, Status).
    3. 📜 **Riwayat Pembayaran**: Riwayat seluruh transaksi lunas beserta tombol **Unduh Kuitansi PDF**.

### 2. Jenjang MTs (Kelas 7, 8, 9) & 9 Master Jenis Pembayaran Resmi
1. **SPP Bulanan**: Rp 350.000 (Kelas 7), Rp 375.000 (Kelas 8), Rp 400.000 (Kelas 9) — Jatuh tempo default tgl 10.
2. **PTS (Penilaian Tengah Semester)**: Rp 100.000.
3. **PAS (Penilaian Akhir Semester Ganjil)**: Rp 150.000.
4. **PAT (Penilaian Akhir Tahun Genap)**: Rp 150.000.
5. **LDKS (Latihan Dasar Kepemimpinan Siswa)**: Khusus Kelas 7 — Rp 300.000.
6. **ST (Studi Tour)**: Khusus Kelas 8 — Rp 750.000 (dapat dicicil).
7. **AT (Kegiatan Akhir Tahun & Perpisahan)**: Khusus Kelas 9 — Rp 2.000.000 (*Smart preset cicilan 10x*).
8. **Daftar Ulang**: Kenaikan Kelas 8 & 9 — Rp 500.000 (dapat dicicil).
9. **Pendaftaran Siswa Baru (Uang Masuk)**: Khusus Kelas 7 — Rp 1.500.000 (*Smart preset cicilan 3x*).

### 3. Master Beasiswa Tunai Siswa (Cash Disbursement)
* Modul Beasiswa dikonfigurasi sebagai **Bantuan Finansial Tunai Langsung** kepada siswa berprestasi / kurang mampu.
* Nominal beasiswa dicairkan langsung secara fisik ke siswa/wali tanpa memotong tagihan SPP di sistem secara sepihak, menjaga integritas rekonsiliasi kas riil madrasah.

### 4. Otomasi WhatsApp & 1-Klik Kirim Pesan (`wa.me`)
* **100% Bebas Biaya Langganan Gateway**: Menggunakan integrasi URL generator resmi `https://wa.me/` yang langsung membuka aplikasi WhatsApp / WhatsApp Web.
* Dilengkapi pencatatan **Log WhatsApp** otomatis (`whatsapp_logs`) untuk audit riwayat pengingat tagihan H-2 jatuh tempo, notifikasi tagihan baru, dan konfirmasi bukti bayar lunas.
* Tombol **Kirim WhatsApp** langsung tersedia di tabel Tagihan dan tabel Pembayaran.

### 5. Generate Tagihan Terpadu (Massal & Fleksibel)
* Tombol **"Generate Tagihan"** di halaman Tagihan (`/admin/bills`).
* **Fleksibilitas Target Penerima**:
  * 🔘 *Semua Siswa Aktif* (Seluruh MTs).
  * 🔘 *Per Angkatan / Tingkat Kelas* (Kelas 7, 8, atau 9).
  * 🔘 *Per Rombel Spesifik* (e.g., 7.1, 8.2, 9.1, dst.).
* **Otomatisasi & Proteksi**:
  * Otomatis membaca matriks tarif resmi per tingkat kelas.
  * Mendukung penagihan reguler (Single) maupun skema cicilan (Tenor).
  * Dilengkapi proteksi *Idempotency* (melewati siswa yang sudah memiliki tagihan yang sama untuk mencegah tagihan ganda).

### 6. Transisi Tahun Ajaran & Pengecualian Siswa Tinggal Kelas
* Tombol **"Tahun Ajaran Baru & Kenaikan Kelas"** di halaman Master Tahun Ajaran.
* Dilengkapi *Repeater dinamis* dengan tombol **`+ Tambah Siswa Tinggal Kelas`** dan pencarian siswa yang dikelompokkan rapi per kategori (*Kelas 7, Kelas 8, Kelas 9*).
* **Otomatisasi Sistem**:
  * Siswa normal: Kelas 7 ➡️ 8, Kelas 8 ➡️ 9, Kelas 9 ➡️ Lulus.
  * Siswa tinggal kelas: Mempertahankan tingkat kelasnya saat ini dan otomatis menerbitkan tagihan Daftar Ulang sesuai tingkatannya.

### 7. Download Template & Import Excel Data Siswa Massal
* Tombol **`Download Template`**: Menghasilkan file spreadsheet `.csv` dengan *UTF-8 BOM* (langsung rapi saat dibuka di Microsoft Excel) lengkap dengan 16 kolom data pokok siswa & wali murid serta 2 baris contoh pengisian.
* Tombol **`Import Excel`**: Mengunggah spreadsheet yang telah diisi, secara otomatis mendaftarkan data siswa, profil wali, akun login portal, serta penempatan kelas di tahun ajaran aktif.

### 8. Dokumen PDF Resmi Berstempel & TTD Digital
* **Invoice PDF & Kuitansi PDF Resmi**: Dibuat menggunakan DomPDF Engine dengan layout presisi, kop surat resmi Yayasan Miftahul 'Ulum, stempel resmi yayasan, dan tanda tangan digital Bendahara.

---

## 🏷 Format Penomoran Dokumen & File

| Jenis Dokumen | Format Nomor Transaksi | Contoh Nomor | Nama File Download PDF |
|---|---|---|---|
| **Tagihan Reguler (Single)** | `INV-{NIS}-{PERIODE}-{RANDOM}` | `INV-3144228827-202608-A1B2` | `INV-3144228827-202608-A1B2.pdf` |
| **Tagihan Cicilan (Tenor)** | `INV-{NIS}-{KODE_JENIS}-{INDEX}` | `INV-3144228827-AT-01` | `INV-3144228827-AT-01.pdf` |
| **Pembayaran Manual / Kasir** | `PAY-MANUAL-{NIS}-{KODE_JENIS}-{INDEX}` | `PAY-MANUAL-3144228827-AT-01` | `PAY-MANUAL-3144228827-AT-01.pdf` |
| **Pembayaran Online (Midtrans)**| `PAY-ONLINE-{NIS}-{KODE_JENIS}-{INDEX}` | `PAY-ONLINE-3144228827-AT-01` | `PAY-ONLINE-3144228827-AT-01.pdf` |

---

## 🔑 Akses Panel & Kredensial Login Demo

| Panel | URL Akses | Role Pengguna | Email Login | Password | Hak Akses Utama |
|---|---|---|---|---|---|
| **Admin Staff** | `http://127.0.0.1:8000/admin` | **Super Admin** | `superadmin@pondok.test` | `password` | Akses Penuh: Master Beasiswa, Audit Log, Reset Tahun Ajaran, Konfigurasi Tarif |
| **Admin Staff** | `http://127.0.0.1:8000/admin` | **Admin Keuangan** | `admin@pondok.test` | `password` | Import Siswa, Kelola Tagihan, Kasir Offline, Laporan Keuangan, Kirim WhatsApp |
| **Parent Portal** | `http://127.0.0.1:8000/portal` | **Wali Siswa** | `wali_3144228827@parent.test` | `password` | Dashboard Tunggakan, Bayar Tagihan Online (Midtrans Snap), Kuitansi & Invoice PDF |
| **Parent Portal** | `http://127.0.0.1:8000/portal` | **Wali Siswa** | `wali_3144228828@parent.test` | `password` | Akun Wali Siswa Demo 2 |

---

## 📁 Struktur Direktori & Penjelasan Lengkap Setiap File

```
administrasi-pondok/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── GenerateMonthlyBillsCommand.php   # Perintah artisan `spp:generate` untuk penerbitan SPP massal bulanan
│   │       ├── DetectOverdueBillsCommand.php     # Perintah `bills:detect-overdue` untuk deteksi jatuh tempo & notifikasi WA
│   │       └── SendDueRemindersCommand.php       # Perintah `bills:send-reminders` untuk pengingat H-2 jatuh tempo via WA
│   │
│   ├── Filament/
│   │   ├── Pages/                               # Halaman Khusus Panel Admin (/admin)
│   │   │   ├── Dashboard.php                    # Dashboard analitik utama admin staff
│   │   │   ├── OutstandingReport.php            # Laporan Tunggakan Siswa (Filter Kelas/Tanggal + PDF & CSV Export)
│   │   │   ├── PaymentReport.php                # Laporan Rekapitulasi Pembayaran
│   │   │   └── RevenueReport.php                # Laporan Pemasukan Kas (Filter Periode/Metode + PDF & CSV Export)
│   │   │
│   │   ├── Parent/                              # Panel Portal Khusus Orang Tua / Wali Siswa (/portal)
│   │   │   ├── Pages/
│   │   │   │   └── Dashboard.php                # Dashboard utama wali murid (Statistik & Tabel Tagihan Aktif)
│   │   │   ├── Resources/
│   │   │   │   ├── MyBillsResource.php          # Resource tagihan (hidden dari sidebar, dialihkan ke dashboard)
│   │   │   │   ├── MyPaymentsResource.php       # Riwayat transaksi pembayaran lunas + Unduh Bukti Bayar PDF
│   │   │   │   └── MyStudentsResource.php       # Data profil anak terdaftar dari akun wali
│   │   │   └── Widgets/
│   │   │       ├── ParentOverviewWidget.php     # 2 Kartu Ringkasan (Total Tunggakan & Jumlah Anak Terdaftar)
│   │   │       └── UnpaidBillsWidget.php        # Tabel Tagihan Aktif + Tombol Bayar Online (Midtrans) & Invoice PDF
│   │   │
│   │   ├── Resources/                           # Resources Panel Admin Staff (/admin)
│   │   │   ├── AcademicYearResource.php         # Master Tahun Ajaran, Kenaikan Kelas, & Pengecualian Tinggal Kelas
│   │   │   ├── AuditLogResource.php             # Jejak aktivitas sistem dan mutasi data (Khusus Super Admin)
│   │   │   ├── BillResource.php                 # Kelola Tagihan, Generate Tagihan Massal (All/Angkatan/Rombel), Kasir Bayar, & Kirim WA
│   │   │   ├── ParentResource.php               # Data Master Profil Orang Tua / Wali Murid
│   │   │   ├── PaymentResource.php              # Log Transaksi Pembayaran + Unduh Kuitansi PDF Resmi + Kirim WA
│   │   │   ├── PaymentTypeResource.php          # Master 9 Jenis Pembayaran MTs & Konfigurasi Tarif per Kelas
│   │   │   ├── StudentPaymentOverrideResource.php # Master Beasiswa Tunai Siswa (Khusus Super Admin)
│   │   │   ├── StudentResource.php              # Data Siswa, Tombol Download Template & Tombol Import Excel Massal
│   │   │   └── WhatsAppLogResource.php          # Riwayat Log Notifikasi WhatsApp & 1-Klik Kirim Ulang Pesan WA
│   │   │
│   │   └── Widgets/                             # Widget Grafik & Kartu Statistik Dashboard Admin
│   │       ├── OutstandingBillsWidget.php       # Tabel daftar siswa dengan tunggakan terbesar
│   │       ├── OutstandingStatsWidget.php       # Kartu statistik ringkasan total tunggakan
│   │       ├── PaymentChart.php                 # Grafik tren realisasi pembayaran SPP bulanan
│   │       ├── PaymentChartNonSpp.php           # Grafik tren realisasi pembayaran Non-SPP
│   │       ├── RecentPayments.php               # Tabel 10 transaksi pembayaran terbaru masuk
│   │       ├── RevenueByMethodWidget.php        # Distribusi pemasukan berdasarkan metode bayar (Tunai vs Online)
│   │       ├── RevenueByPaymentTypeWidget.php   # Distribusi pemasukan berdasarkan jenis pembayaran
│   │       ├── RevenueStatsWidget.php           # Kartu statistik ringkasan total pemasukan
│   │       ├── StatsOverview.php                # Statistik utama ringkasan finansial madrasah
│   │       └── UnpaidBillsWidget.php            # Tabel ringkasan tagihan belum lunas
│   │
│   ├── Http/
│   │   └── Controllers/
│   │       └── PaymentController.php            # Endpoint Webhook Callback Midtrans (Verifikasi SHA512 Signature)
│   │
│   ├── Models/                                  # Eloquent Models & Aturan Bisnis Basis Data
│   │   ├── AcademicYear.php                     # Model Tahun Ajaran (`academic_years`)
│   │   ├── AuditLog.php                         # Model Log Audit Aktivitas Pengguna (`audit_logs`)
│   │   ├── Bill.php                             # Model Tagihan Siswa (`bills`)
│   │   ├── BillItem.php                         # Model Rincian Item Tagihan (`bill_items`)
│   │   ├── Invoice.php                          # Model Data Invoice PDF (`invoices`)
│   │   ├── ParentProfile.php                    # Model Profil Orang Tua / Wali Siswa (`parents`)
│   │   ├── Payment.php                          # Model Transaksi Pembayaran Kasir & Gateway (`payments`)
│   │   ├── PaymentDueDateOverride.php           # Model Penyesuaian Jatuh Tempo Khusus (`payment_due_date_overrides`)
│   │   ├── PaymentEvidence.php                  # Model Bukti Pembayaran Manual (`payment_evidences`)
│   │   ├── PaymentGatewayTransaction.php        # Model Log Mentah Payload Webhook Midtrans (`payment_gateway_transactions`)
│   │   ├── PaymentType.php                      # Model Master Jenis Pembayaran (`payment_types`)
│   │   ├── PaymentTypePrice.php                 # Model Matriks Tarif per Tingkat Kelas (`payment_type_prices`)
│   │   ├── Receipt.php                          # Model Data Kuitansi / Bukti Bayar PDF (`receipts`)
│   │   ├── Student.php                          # Model Data Siswa MTs (`students`)
│   │   ├── StudentAcademicYear.php              # Model Riwayat Penempatan Kelas & Tahun Ajaran (`student_academic_years`)
│   │   ├── StudentPaymentOverride.php           # Model Data Beasiswa Tunai Siswa (`student_payment_overrides`)
│   │   ├── User.php                             # Model Pengguna & Role Checkers (`users`)
│   │   └── WhatsAppLog.php                      # Model Log Pesan WhatsApp (`whatsapp_logs`)
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php               # Konfigurasi global aplikasi Laravel & Blade Macros
│   │   └── Filament/
│   │       ├── AdminPanelProvider.php           # Konfigurasi & Registrasi Panel Admin Staff (`/admin`)
│   │       └── ParentPanelProvider.php          # Konfigurasi & Registrasi Panel Portal Wali Murid (`/portal`)
│   │
│   └── Services/                                # Core Domain Business Logic Services
│       ├── AcademicYearTransitionService.php    # Layanan pergantian tahun ajaran, kenaikan kelas, & tinggal kelas
│       ├── BillingService.php                   # Generator tagihan otomatis, cicilan tenor, & SPP massal
│       ├── DocumentService.php                  # Layanan pembuatan Invoice & Kuitansi PDF berstempel serta CSV
│       ├── DueDateResolutionService.php         # Layanan kalkulasi tanggal jatuh tempo efektif tagihan
│       ├── PaymentService.php                   # Layanan pemrosesan Midtrans Snap Token & pembayaran kasir
│       ├── PricingResolutionService.php         # Layanan resolusi tarif dan hirarki harga kelas
│       ├── ReportService.php                    # Layanan query builder laporan pemasukan dan tunggakan
│       ├── StudentImportService.php             # Layanan download template & parser import massal data siswa
│       └── WhatsAppAutomationService.php        # Layanan generator teks pesan resmi wa.me & logger WhatsApp
│
├── database/
│   ├── migrations/                              # 22 File Migrasi Skema Basis Data
│   └── seeders/
│       ├── AcademicYearSeeder.php               # Seeder Tahun Ajaran 2026/2027 Aktif
│       ├── BillSeeder.php                       # Seeder Tagihan SPP & Non-SPP untuk demo
│       ├── DatabaseSeeder.php                   # Master Seeder Orchestrator
│       ├── PaymentTypeSeeder.php                # Seeder 9 Master Jenis Pembayaran Resmi MTs
│       └── UserSeeder.php                       # Seeder Akun Staff & 241 Siswa Aktif Lengkap dengan Akun Wali
│
├── resources/
│   └── views/
│       ├── filament/
│       │   └── pages/                           # Blade templates untuk halaman laporan kustom Filament
│       │       ├── dashboard.blade.php
│       │       ├── outstanding-report.blade.php
│       │       ├── payment-report.blade.php
│       │       └── revenue-report.blade.php
│       ├── payment/
│       │   └── status.blade.php                 # Halaman Redirect Status Pembayaran Online (Sukses/Pending/Gagal)
│       └── pdf/                                 # Blade templates resmi DomPDF (Berstempel & TTD Digital)
│           ├── invoice.blade.php                # Desain PDF Invoice Tagihan Resmi
│           ├── outstanding-rekap.blade.php      # Desain PDF Rekapitulasi Laporan Tunggakan
│           ├── receipt.blade.php                # Desain PDF Kuitansi Bukti Pembayaran Resmi Berstempel
│           └── revenue-rekap.blade.php          # Desain PDF Rekapitulasi Laporan Pemasukan
│
├── routes/
│   ├── console.php                              # Task Scheduler untuk Artisan Cron Commands
│   └── web.php                                  # Rute Webhook Midtrans Callback & Redirection
│
└── tests/
    ├── Feature/
    │   └── ExampleTest.php                      # Pengujian Feature Redirection & Endpoint
    └── Unit/                                    # Unit Testing Bisnis Logic (100% Isolated DatabaseTransactions)
        ├── AcademicYearTransitionServiceTest.php # Pengujian transisi tahun ajaran & siswa tinggal kelas
        ├── AuditLogTest.php                     # Pengujian integritas logging audit aktivitas
        ├── BillingServiceTest.php               # Pengujian formula nominal tagihan & cicilan
        ├── BillModelTest.php                    # Pengujian helper status tagihan (isPaid, isOverdue)
        ├── DocumentServiceTest.php              # Pengujian PDF DomPDF & CSV builder
        ├── DueDateResolutionTest.php            # Pengujian resolusi tanggal jatuh tempo
        ├── PaymentServiceTest.php               # Pengujian verifikasi pembayaran & Midtrans
        ├── PricingResolutionTest.php            # Pengujian hierarki tarif pembayaran
        ├── ReportServiceTest.php                # Pengujian builder laporan keuangan
        ├── StudentImportServiceTest.php         # Pengujian download template & import massal spreadsheet
        └── WhatsAppAutomationServiceTest.php    # Pengujian generator link WhatsApp & pencatatan log
```

---

## 🗄 Skema Basis Data (18 Tabel Utama)

1. **`users`**: Data autentikasi login (`super_admin`, `admin`, `parent`).
2. **`parents`**: Data profil orang tua / wali murid (`full_name`, `phone`, `contact_email`, `address`).
3. **`students`**: Data pokok siswa (`nis`, `nism`, `full_name`, `gender`, `class_level`, `rombel`, `status`).
4. **`academic_years`**: Master tahun ajaran akademik (e.g., 2026/2027) & status aktif.
5. **`student_academic_years`**: Riwayat riil penempatan kelas dan rombel siswa per tahun ajaran.
6. **`payment_types`**: Master jenis pembayaran (SPP, PTS, PAS, PAT, LDKS, ST, AT, Daftar Ulang, Uang Masuk).
7. **`payment_type_prices`**: Matriks penetapan harga tarif per tingkat kelas dan rombel.
8. **`student_payment_overrides`**: Master beasiswa tunai siswa.
9. **`payment_due_date_overrides`**: Master penyesuaian khusus tanggal jatuh tempo.
10. **`bills`**: Master data tagihan siswa (`amount`, `paid_amount`, `outstanding_amount`, `status`, `due_date`).
11. **`bill_items`**: Rincian sub-item pada sebuah tagihan.
12. **`payments`**: Transaksi pembayaran yang tercatat (`paid_amount`, `method`, `status`, `paid_at`).
13. **`payment_gateway_transactions`**: Log mentah respons payload dari Midtrans Webhook.
14. **`payment_evidences`**: Berkas bukti transfer untuk verifikasi manual kasir.
15. **`invoices`**: Arsip nomor dan metadata invoice tagihan.
16. **`receipts`**: Arsip nomor dan metadata kuitansi bukti pembayaran berstempel.
17. **`whatsapp_logs`**: Log rekaman pesan WhatsApp notifikasi tagihan dan kuitansi.
18. **`audit_logs`**: Jejak audit komprehensif seluruh aksi mutasi data oleh pengguna.

---

## 🚀 Panduan Instalasi & Pengoperasian (Handover Guide)

Bagi rekan pengembang atau asisten yang menjalankan proyek ini di mesin lokal baru:

### 1. Kebutuhan Sistem
* PHP `>= 8.2` (Disarankan PHP 8.4)
* Extension PHP: `pdo_mysql`, `gd`, `zip`, `mbstring`, `fileinfo`, `xml`
* Composer `>= 2.0`
* MySQL / MariaDB Server `>= 8.0`

### 2. Langkah Cepat Menjalankan Proyek (Fresh Setup)
```bash
# 1. Masuk ke direktori proyek
cd administrasi-pondok

# 2. Install dependency backend PHP
composer install

# 3. Buat file konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Buat database MySQL di phpMyAdmin / MySQL CLI
# CREATE DATABASE db_administrasi_pondok;

# 5. Sesuaikan koneksi database di file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=db_administrasi_pondok
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Buat symbolic link untuk file attachment storage
php artisan storage:link

# 7. Jalankan seluruh migrasi skema dan seed data lengkap
php artisan migrate:fresh --seed

# 8. Jalankan local development server
php artisan serve
```

### 3. Tips Troubleshooting Cepat
* **Aset Logo / CSS tidak muncul**: Jalankan `php artisan storage:link` dan `php artisan optimize:clear`.
* **Database Connection Refused**: Pastikan service MySQL (XAMPP / MySQL Service) dalam status *Running* di port `3306`.
* **Login Gagal**: Jalankan `php artisan db:seed --class=UserSeeder` untuk mereset seluruh akun demo. Password default semua akun adalah: `password`.

---

## ⚡ Perintah Artisan (CLI Commands)

```bash
# 1. Menjalankan deteksi tagihan yang melewati jatuh tempo (Update Overdue & Siapkan Log WA)
php artisan bills:detect-overdue

# 2. Mengirimkan pengingat tagihan jatuh tempo H-2 via WhatsApp
php artisan bills:send-reminders

# 3. Menerbitkan tagihan SPP bulanan massal untuk seluruh siswa aktif
php artisan spp:generate

# 4. Membersihkan seluruh cache bootstrap aplikasi
php artisan optimize:clear
```

---

## 🧪 Pengujian Otomatis (PHPUnit Test Suite)

Aplikasi dilengkapi pengujian unit dan fitur komprehensif yang berjalan dengan transaksi terisolasi (`DatabaseTransactions`):

```bash
# Menjalankan seluruh test suite
php artisan test
```

**Hasil Pengujian:**
```text
   PASS  Tests\Unit\AcademicYearTransitionServiceTest (2 tests)
   PASS  Tests\Unit\AuditLogTest (1 test)
   PASS  Tests\Unit\BillModelTest (1 test)
   PASS  Tests\Unit\BillingServiceTest (3 tests)
   PASS  Tests\Unit\DocumentServiceTest (1 test)
   PASS  Tests\Unit\DueDateResolutionTest (1 test)
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Unit\PaymentServiceTest (1 test)
   PASS  Tests\Unit\PricingResolutionTest (1 test)
   PASS  Tests\Unit\ReportServiceTest (1 test)
   PASS  Tests\Unit\StudentImportServiceTest (2 tests)
   PASS  Tests\Unit\WhatsAppAutomationServiceTest (3 tests)
   PASS  Tests\Feature\ExampleTest (1 test)

  Tests:    19 passed (39 assertions)
  Duration: ~3.00s (100% Success)
```

---
*Dikembangkan dengan dedikasi untuk efisiensi, akuntabilitas, dan kemudahan tata kelola administrasi keuangan **Yayasan Perguruan Islam Miftahul 'Ulum**.*
