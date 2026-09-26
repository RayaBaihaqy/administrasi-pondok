# 🕌 Sistem Informasi Administrasi & Pembayaran MTs. Miftahul 'Ulum

Sistem Informasi Manajemen Penagihan, Pembayaran Online (Midtrans) & Kasir Offline, Rekapitulasi Laporan Keuangan, Invoice & Kuitansi PDF Resmi Berstempel, Integrasi Notifikasi WhatsApp, Kelola Admin, Audit Trail Otomatis, serta Portal Wali Siswa terpadu untuk **Yayasan Perguruan Islam Miftahul 'Ulum (Jenjang MTs. Miftahul 'Ulum)**.

Sistem ini dibangun dengan arsitektur **Modular Monolith Enterprise** menggunakan **Laravel 12**, **Filament v5**, **Livewire 3**, **Midtrans Payment Gateway API**, **DomPDF Engine**, dan **PhpSpreadsheet**.

---

## 📋 Daftar Isi
1. [Spesifikasi & Tech Stack](#-spesifikasi--tech-stack)
2. [Profil Lembaga & Rekening Resmi Madrasah](#-profil-lembaga--rekening-resmi-madrasah)
3. [Fitur Utama & Keunggulan Sistem](#-fitur-utama--keunggulan-sistem)
4. [Daftar 11 Jenis Pembayaran & Tarif Resmi](#-daftar-11-jenis-pembayaran--tarif-resmi)
5. [Sistem Pembayaran Cicilan (Sistem A)](#-sistem-pembayaran-cicilan-sistem-a)
6. [Standar Dokumen Invoice & Kuitansi PDF](#-standar-dokumen-invoice--kuitansi-pdf)
7. [Format Penomoran Dokumen & File](#-format-penomoran-dokumen--file)
8. [Akses Panel, Hak Akses & Kredensial Login](#-akses-panel-hak-akses--kredensial-login)
9. [Struktur Direktori & Penjelasan Lengkap File](#-struktur-direktori--penjelasan-lengkap-file)
10. [Panduan Instalasi & Pengoperasian](#-panduan-instalasi--pengoperasian)
11. [Perintah Artisan (CLI Commands)](#-perintah-artisan-cli-commands)
12. [Pengujian Otomatis (PHPUnit Test Suite)](#-pengujian-otomatis-phpunit-test-suite)

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
| **Spreadsheet Engine**| PhpSpreadsheet | `^5.9` | Generator Template Native `.xlsx` & Universal Spreadsheet Parser |
| **Database** | MySQL | `>= 8.0` | Relational Database Storage dengan Indexing Optimal |
| **Testing Suite** | PHPUnit | `^11.5` | Automated Unit & Feature Testing (**71/71 Passing, 285 assertions — 100%**) |

---

## 🏛 Profil Lembaga & Rekening Resmi Madrasah

Seluruh identitas lembaga dan informasi rekening pembayaran terpusat pada file konfigurasi [`config/school.php`](config/school.php) dan dapat disesuaikan secara dinamis melalui file `.env` maupun antarmuka panel:

| Parameter | Nilai Konfigurasi Default | Variabel Environment (`.env`) |
|---|---|---|
| **Nama Yayasan** | `YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM` | `SCHOOL_NAME` |
| **Nama Lembaga** | `MTs. MIFTAHUL 'ULUM` | `SCHOOL_INSTITUTION` |
| **Alamat Lembaga** | `Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung` | `SCHOOL_ADDRESS` |
| **Bank Penerima** | `Bank BRI` | `SCHOOL_BANK_NAME` |
| **Nomor Rekening** | `176901000210569` | `SCHOOL_BANK_ACCOUNT` |
| **Atas Nama Rekening** | `Madrasah Tsanawiyah Miftahul Ulum` | `SCHOOL_BANK_HOLDER` |
| **Nama Bendahara Default** | `Hj. Titi Nurhayati, S. Pd` | `SCHOOL_TREASURER_NAME` |

---

## 🌟 Fitur Utama & Keunggulan Sistem

### 1. Dual Panel Access & Multi-Identifier Login (Email / No. Telepon)
* **Login Fleksibel**: Pengguna dapat masuk menggunakan **Email** maupun **Nomor Telepon / WhatsApp** pada 1 akun yang sama.
* **Pesan Error Informatif & Spesifik**:
  * 🔴 *"Email tidak ditemukan."* jika email belum terdaftar.
  * 🔴 *"Nomor telepon tidak ditemukan."* jika nomor HP belum terdaftar.
  * 🔴 *"Password salah."* jika kata sandi keliru.
* **Admin Staff Panel (`/admin`)**:
  * **Super Admin (*Kepala Yayasan / Bendahara Utama*)**: Manajemen Akun Admin Staff, Log Audit Sistem, Kenaikan Kelas Massal, Reset Tahun Ajaran, Konfigurasi Tarif, dan Pengaturan Profil/TTD Bendahara.
  * **Admin (*Operator Keuangan / Kasir*)**: Pengelolaan Siswa, Mutasi Siswa Pindah, Import Excel `.xlsx`, Penerbitan Tagihan, Kasir Tunai, Laporan Keuangan, dan Pengiriman WhatsApp.
* **Parent Portal Panel (`/portal`)**:
  * Dirancang khusus untuk **Wali Santri** dengan tema Emerald Green yang bersih dan responsif.
  * 3 Menu Utama: **Dashboard** (Tunggakan & Bayar Online), **Anak Saya** (Profil Santri & Multi-anak Sibling), dan **Riwayat Pembayaran** (Arsip Kuitansi PDF).

### 2. Arsitektur Single Super Admin & Manajemen Akun Admin Staff
* **Arsitektur Singleton Super Admin**:
  * Sistem mengunci akun Super Admin utama (`superadmin@pondok.test`).
  * Guard model level (`User::booted`) secara mutlak menolak pembuatan akun Super Admin kedua, menolak promosi role ke `super_admin`, dan menolak penghapusan akun Super Admin utama.
* **Menu Kelola Admin (`/admin/users`)**:
  * Disediakan khusus untuk Super Admin pada grup navigasi **Manajemen Pengguna** -> **Kelola Admin**.
  * Super Admin dapat menambahkan akun staf Admin baru, mengubah profil, mereset password admin dengan modal interaktif, serta menonaktifkan/menghapus staf admin.
  * Tabel hanya menampilkan staf admin (akun super admin otomatis di-filter keluar agar tidak dapat diubah/dihapus oleh siapapun).

### 3. Sistem Notifikasi Error Ramah Pengguna (Zero Raw Error Leak)
* **Trait `HasFriendlyNotifications` & Localized Validation**:
  * Semua halaman Create & Edit di seluruh resource mengimplementasikan penanganan pesan kesalahan interaktif berbahasa Indonesia yang bersahabat.
  * Setiap kesalahan input (misalnya nomor HP duplikat, NISN ganda, email kembar, format tanggal tidak valid) ditangkap dan ditampilkan via **Pop-up / Danger Toast Notification** yang jelas dan mendalam.
  * Mencegah total kebocoran pesan database mentah, SQL exception, atau kode status 500 ke hadapan pengguna akhir.

### 4. Audit Trail Otomatis Komprehensif via Model Observers
* **Coverage Penuh Seluruh Entitas Finansial & Master Data**:
  * Model Observers (`UserObserver`, `StudentObserver`, `ParentProfileObserver`, `BillObserver`, `PaymentObserver`, `AcademicYearObserver`, `PaymentTypeObserver`) otomatis merekam setiap penambahan, pengubahan, mutasi, pembatalan, dan penghapusan data.
  * Mencatat waktu kejadian presisi, nama aktor, role, alamat IP, user agent, data sebelum perubahan (*old values*), dan data sesudah perubahan (*new values*).
  * Bersifat *immutable* (tidak dapat dimanipulasi atau dihapus).
  * Menu Audit Log tersembunyi dari sidebar agar antarmuka tetap bersih, namun dapat diakses langsung oleh Super Admin melalui URL `/admin/audit-logs` (didukung auto-redirect dari `/admin/audit-log`).

### 5. Pengaturan Profil Bendahara & Upload Tanda Tangan Digital (`/admin/profile`)
* Halaman **Profile** untuk Super Admin/Bendahara:
  * **Nama Bendahara**: Nama lengkap & gelar resmi bendahara.
  * **Upload Tanda Tangan (TTD)**: Unggah file foto/scan tanda tangan (PNG/JPG transparan).
  * **Auto-Redirect**: Setelah data profil dan TTD disimpan, sistem otomatis mengarahkan admin kembali ke **Dashboard Utama**.
* **Historical Snapshot & Immutabilitas Dokumen**:
  * Setiap Invoice & Kuitansi mengunci (*snapshot*) nama dan tanda tangan bendahara yang aktif pada saat dokumen diterbitkan.
  * Dokumen masa lalu yang terbit pada era pejabat bendahara terdahulu tetap permanen dengan tanda tangan pejabat bersangkutan.
  * Dokumen baru yang terbit setelah pergantian bendahara baru secara otomatis mengunci nama & tanda tangan bendahara baru.
* **Integrasi PDF Instan**: Render tanda tangan berbasis Base64 memastikan file PDF kuitansi dan tagihan ter-generate sangat cepat tanpa kendala akses path storage.

### 6. Manajemen Mutasi Siswa (Siswa Pindah) & Pembatalan Tagihan Otomatis
* **Aksi 1-Klik `[ Mutasi / Pindah ]`**: Mengubah status siswa aktif menjadi **Keluar / Pindah (`withdrawn`)** dengan modal konfirmasi interaktif.
* **Auto-Cancel Tagihan Menggantung**: Tagihan yang berstatus `unpaid` atau `overdue` otomatis dibatalkan (`cancelled`) dengan catatan alasan mutasi, sehingga piutang madrasah langsung bersih.
* **Integritas Pembukuan Terjaga**: Riwayat tagihan dan kuitansi pembayaran yang sudah lunas (`paid`/`success`) tetap tersimpan utuh untuk kebutuhan rekapitulasi keuangan tahunan akuntan yayasan.
* **Header Tabs Filter**: Halaman Siswa dilengkapi tab `🟢 Siswa Aktif`, `🟠 Siswa Mutasi / Pindah`, `🔵 Alumni / Lulus`, dan `Semua Siswa` dengan badge jumlah siswa dinamis.

### 7. Pemilihan Rombel Dinamis & Data Real Siswa (239 Siswa Nyata)
* Pilihan rombel kelas terkunci sampai tingkat kelas dipilih:
  * **Kelas 7**: Rombel `7.1`, `7.2`, `7.3` (61 Siswa)
  * **Kelas 8**: Rombel `8.1`, `8.2`, `8.3`, `8.4` (73 Siswa)
  * **Kelas 9**: Rombel `9.1`, `9.2`, `9.3`, `9.4` (105 Siswa)
* Terintegrasi dengan **Data Nyata Orang Tua** (nama asli, nomor WhatsApp asli, email asli) serta mendukung pengelompokan saudara kandung (*siblings*) pada satu akun portal wali.

### 8. Transisi Tahun Ajaran Baru & Kenaikan Kelas Otomatis
* Modal transisi tahun ajaran hanya membutuhkan **Tanggal Mulai** dan **Tanggal Selesai**.
* Nama tahun ajaran baru ter-generate otomatis dari tahun kedua tanggal tersebut (misal `2026/2027`).
* Eksekusi kenaikan kelas massal: Kelas 7 naik ke Kelas 8, Kelas 8 naik ke Kelas 9, Kelas 9 otomatis lulus (*graduated*). Dilengkapi *repeater* dinamis siswa tinggal kelas dengan pencarian terkelompok per kelas.

### 9. Download Template & Import Data Siswa Massal Native Excel (`.xlsx`)
* Tombol **`Download Template`**: Menghasilkan file native **Excel Workbook (`.xlsx`)** dengan format kolom **`Number` (desimal 0)** untuk NISM, Tingkat Kelas, Tahun Masuk, serta format **`Text`** untuk NISN & No. HP agar angka `0` tidak hilang.
* **Sanitasi Data Anti-`e+17`**: Parser import secara otomatis membersihkan notasi ilmiah dan formula Excel, memastikan data masuk utuh dan bersih.

### 10. Otomasi WhatsApp & Riwayat Log WhatsApp (`wa.me`)
* **100% Bebas Biaya Gateway**: Menggunakan URL generator `https://wa.me/` yang langsung membuka aplikasi WhatsApp / WhatsApp Web.
* Pesan notifikasi melampirkan link unduh dokumen PDF resmi berstempel (`/docs/invoice/{no}` dan `/docs/receipt/{no}`).
* Semua aktivitas pengiriman tagihan baru, pengingat jatuh tempo, dan bukti pembayaran sukses otomatis tercatat di menu **Log WhatsApp** (`/admin/whats-app-logs`).

---

## 💰 Daftar 11 Jenis Pembayaran & Tarif Resmi

| No | Jenis Pembayaran | Kode Pos | Tarif Resmi | Target Kelas | Skema / Frekuensi |
|---|---|---|---|---|---|
| 1 | **SPP** | `spp` | Rp 75.000 | Kelas 7, 8, 9 | Bulanan (Jatuh tempo tgl 10) |
| 2 | **Pendaftaran Siswa Baru** | `pendaftaran-siswa-baru` | Rp 1.190.000 | Kelas 7 Baru | Sekali di Awal (Mendukung Cicilan) |
| 3 | **Daftar Ulang Tahun Ajaran Baru** | `daftar-ulang-ta-baru` | Rp 440.000 | Kelas 8 & 9 | Tahunan (Awal TA) |
| 4 | **ASTS/PTS Ganjil** | `asts-pts-ganjil` | Rp 75.000 | Kelas 7, 8, 9 | Semester Ganjil |
| 5 | **ASAS Ganjil** | `asas-ganjil` | Rp 150.000 | Kelas 7, 8, 9 | Semester Ganjil |
| 6 | **LDKS** | `ldks` | Rp 450.000 | Kelas 7, 8, 9 | Sekali per Kegiatan |
| 7 | **Daftar Ulang Semester Genap** | `daftar-ulang-semester-genap` | Rp 440.000 | Kelas 7, 8, 9 | Semester Genap |
| 8 | **ASTS/PTS Genap** | `asts-pts-genap` | Rp 75.000 | Kelas 7, 8, 9 | Semester Genap |
| 9 | **ASATA/PAT** | `asata-pat` | Rp 150.000 | Kelas 7, 8, 9 | Semester Genap |
| 10 | **Study Tour** | `study-tour` | Rp 450.000 | Kelas 7, 8, 9 | Sekali per Kegiatan |
| 11 | **Akhir Tahun** | `akhir-tahun` | Rp 2.000.000 | Kelas 9 | Tahunan (Mendukung Cicilan) |

---

## 💳 Sistem Pembayaran Cicilan (Sistem A)

Sistem menerapkan arsitektur **Sistem A (*Single Master Bill with Incremental Partial Payments*)** untuk menangani pembayaran berseri atau cicilan bertahap:

1. **Satu Tagihan Induk (*Master Bill*)**:
   - Dibuat 1 baris tagihan dengan nominal penuh (contoh: Pendaftaran Rp 1.190.000 atau Akhir Tahun Rp 2.000.000).
2. **Pencatatan Riwayat Pembayaran Bertahap**:
   - Setiap setoran cicilan membuat record pembayaran baru (`Payment`) yang terhubung langsung ke tagihan induk.
   - Tagihan otomatis menghitung ulang `paid_amount` dan `outstanding_amount`.
3. **Kalkulasi Akurat pada Kuitansi Pembayaran ([receipt.blade.php](resources/views/pdf/receipt.blade.php))**:
   - **Nominal Diterima:** Jumlah yang dibayarkan pada transaksi tersebut.
   - **Total Tagihan:** Nominal penuh tagihan induk.
   - **Akumulasi Pembayaran:** Total yang telah disetorkan hingga transaksi tersebut.
   - **Sisa Tagihan:** Sisa kewajiban yang belum terbayar (menjadi Rp 0 saat lunas).

---

## 📄 Standar Dokumen Invoice & Kuitansi PDF

1. **Kop Surat Standar Madrasah**:
   - Logo madrasah di sebelah kiri.
   - Teks Yayasan: `YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM`.
   - Teks Lembaga: `MADRASAH TSANAWIYAH MIFTAHUL 'ULUM`.
   - Alamat: `Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung Kab. Bekasi`.
   - Badge Status: `LUNAS` (hijau), `BELUM LUNAS` (kuning), `TERLAMBAT` (merah).
2. **Data & Transaksi**:
   - Identitas siswa (NISN, Nama, Kelas, Rombel).
   - Rincian item tagihan dan petunjuk transfer Bank BRI.
3. **Tanda Tangan & Cap Digital**:
   - Tanda tangan digital dinamis dari akun bendahara aktif (historical snapshot).
   - Cap stempel basah digital resmi madrasah.
   - Nama terang bendahara tercetak jelas di bawah tanda tangan.

---

## 🏷 Format Penomoran Dokumen & File

| Jenis Dokumen | Format Nomor Transaksi | Contoh Nomor | Nama File Download PDF |
|---|---|---|---|
| **Tagihan Reguler (Single)** | `INV-{NIS}-{PERIODE}-{RANDOM}` | `INV-3144228827-202608-A1B2` | `INV-3144228827-202608-A1B2.pdf` |
| **Tagihan Cicilan (Master)** | `INV-{NIS}-{KODE_JENIS}` | `INV-3144228827-PENDAFTARAN` | `INV-3144228827-PENDAFTARAN.pdf` |
| **Pembayaran Manual / Kasir** | `PAY-MANUAL-{NIS}-{KODE_JENIS}-{INDEX}` | `PAY-MANUAL-3144228827-AT-01` | `PAY-MANUAL-3144228827-AT-01.pdf` |
| **Pembayaran Online (Midtrans)**| `PAY-ONLINE-{NIS}-{KODE_JENIS}-{INDEX}` | `PAY-ONLINE-3144228827-AT-01` | `PAY-ONLINE-3144228827-AT-01.pdf` |

---

## 🔑 Akses Panel, Hak Akses & Kredensial Login

| Panel | URL Akses | Role Pengguna | Email Login | No. Telepon Login | Password | Hak Akses Utama |
|---|---|---|---|---|---|---|
| **Admin Staff** | `http://127.0.0.1:8000/admin` | **Super Admin** | `superadmin@pondok.test` | `081299990001` | `password` | Akses Penuh: Kelola Admin, Audit Log, Reset TA, Tarif, Profil & TTD |
| **Admin Staff** | `http://127.0.0.1:8000/admin` | **Admin Keuangan** | `admin@pondok.test` | `081299990002` | `password` | Operasional: Siswa, Tagihan, Kasir, Laporan, Log WA |
| **Parent Portal** | `http://127.0.0.1:8000/portal` | **Wali Siswa** | `wali_3144228827@parent.test` | `081244228827` | `password` | Dashboard Mandiri: Tagihan Anak, Bayar Midtrans, Kuitansi & Invoice |
| **Parent Portal** | `http://127.0.0.1:8000/portal` | **Wali Siswa** | `wali_3144228828@parent.test` | `081244228828` | `password` | Akun Wali Siswa Demo 2 |

---

## 📁 Struktur Direktori & Penjelasan Lengkap File

```
administrasi-pondok/
├── app/
│   ├── Console/Commands/                        # Artisan Scheduled Commands (SPP Massal, Overdue Detect, Due Reminders)
│   ├── Filament/
│   │   ├── Pages/Auth/Login.php                 # Custom Login Dual Identifier (Email/No HP) & Pesan Error Spesifik
│   │   ├── Pages/Auth/EditProfile.php           # Custom Edit Profile (Nama Bendahara & Upload TTD)
│   │   ├── Pages/                               # Halaman Dashboard, RevenueReport, OutstandingReport, PaymentReport
│   │   ├── Parent/                              # Panel Portal Khusus Orang Tua / Wali Siswa (/portal)
│   │   ├── Resources/                           # Resources Panel Admin (UserResource, StudentResource, BillResource, dll.)
│   │   └── Traits/HasFriendlyNotifications.php  # Trait Global Intercept Error & Indonesian Toast Notification
│   ├── Http/Controllers/                       # DocumentController (PDF Publik) & PaymentController (Webhook Midtrans)
│   ├── Models/                                  # 17 Eloquent Models (User, Student, Bill, Payment, dll)
│   ├── Observers/                               # 7 Model Observers untuk Pencatatan Audit Trail Otomatis
│   ├── Providers/Filament/                      # AdminPanelProvider & ParentPanelProvider
│   └── Services/                                # BillingService, PaymentService, StudentImportService, DocumentService, WhatsAppAutomationService
├── database/
│   ├── migrations/                              # 26 Database Migrations
│   └── seeders/                                 # DatabaseSeeder, UserSeeder, AcademicYearSeeder, PaymentTypeSeeder, BillSeeder
├── lang/id/                                     # Terjemahan Bahasa Indonesia & Pesan Validasi
├── resources/views/pdf/                         # Template DomPDF Invoice, Receipt & Laporan
└── tests/                                       # 71 Automated Unit & Feature Tests
```

---

## 🚀 Panduan Instalasi & Pengoperasian

```bash
# 1. Clone repository & masuk ke direktori
cd c:/Projects/administrasi-pondok

# 2. Install dependensi composer
composer install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Link storage untuk tanda tangan & kuitansi
php artisan storage:link

# 5. Jalankan migrasi dan seeder data resmi klien
php artisan migrate:fresh --seed

# 6. Jalankan server lokal
php artisan serve
```

---

## 🧪 Pengujian Otomatis (PHPUnit Test Suite)

Jalankan seluruh test suite dengan perintah:
```bash
php artisan test
```
**Status: 71 tests passed, 285 assertions (100% PASS)**
