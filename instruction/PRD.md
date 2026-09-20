# PRD — Sistem Administrasi Pembayaran Madrasah & Pondok
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> **Product Requirements Document (PRD)**
>
> Sistem Administrasi Pembayaran Madrasah Tsanawiyah Miftahul 'Ulum berbasis web modern.

---

## 1. Document Information

| Item | Detail |
|---|---|
| Nama Produk | Sistem Administrasi & Pembayaran MTs. Miftahul 'Ulum |
| Dokumen | Product Requirements Document (PRD) |
| Versi | 2.0 — Production Ready |
| Status | ✅ Confirmed Specification |
| Institusi | MTs. Miftahul 'Ulum / Yayasan Perguruan Islam Miftahul 'Ulum |
| Target Pengguna | Super Admin (Kepala Yayasan / Kepala Bendahara), Admin (Kasir / Staf Keuangan), Orang Tua / Wali Santri |
| Backend | Laravel 12 (PHP 8.2+) |
| Admin Panel | Filament 5 |
| Parent Portal | Blade + Tailwind CSS (Emerald Green Theme) |
| Payment Gateway | Midtrans (Snap & Webhook) |
| Spreadsheet Engine | PhpSpreadsheet (Native `.xlsx`) |
| PDF Engine | DomPDF |
| Database | MySQL |

---

# 2. Executive Summary

Sistem Administrasi Pembayaran MTs. Miftahul 'Ulum adalah aplikasi web terpadu untuk mendigitalisasi dan mengotomatisasi tata kelola keuangan madrasah. Sistem ini menggantikan pencatatan manual berbasis spreadsheet Excel menjadi sistem transaksi terpusat, aman, dan dapat diaudit.

Sistem dirancang untuk tiga kelompok pengguna utama:
1. **Super Admin (Kepala Yayasan / Kepala Bendahara)**: Memantau performa keuangan secara eksekutif, mengelola master tarif 11 pos biaya, konfigurasi tahun ajaran & kenaikan kelas, mengaudit aktivitas sistem, serta memperbarui nama dan tanda tangan digital bendahara pada dokumen resmi.
2. **Admin (Operator / Kasir Bendahara)**: Menjalankan operasional keuangan harian, penerbitan tagihan satuan & massal, penerimaan loket kasir tunai/manual, import data siswa via Excel `.xlsx`, dan pengiriman notifikasi WhatsApp 1-klik (`wa.me`).
3. **Orang Tua / Wali Siswa**: Mengakses Portal Mandiri (`/portal`) dengan autentikasi ganda (Email / No. HP), memantau kewajiban pendidikan anak kandung, membayar instan via Midtrans Snap (QRIS, Virtual Account, Gerai Retail), serta mengunduh Invoice dan Kuitansi sah berstempel & bertanda tangan.

---

# 3. User Roles & Authentication

### 3.1 Role Hierarchy & Permissions
Sistem menerapkan role-based access control (RBAC) dengan 3 peran:

```text
Super Admin (super_admin)
  │── Memiliki seluruh wewenang Admin
  │── Mengelola Master Pos Pembayaran & Matriks Tarif 11 Kategori
  │── Mengelola Master Tahun Ajaran & Eksekusi Kenaikan Kelas Massal
  │── Mengakses Audit Trail Lengkap (Immutable Log)
  └── Mengelola Profil & Unggah Tanda Tangan Digital Bendahara (/admin/profile)

Admin (admin)
  │── Manajemen Data Siswa (Tingkat 7, 8, 9 & Rombel Dinamis)
  │── Import Massal Siswa dari Excel Asli (.xlsx)
  │── Manajemen Data Wali Murid & Auto-provisioning Akun
  │── Penerbitan Tagihan (Individual, Massal, dan Skema Cicilan)
  │── Loket Pembayaran Kasir Tunai / Manual & Upload Bukti Bayar
  │── Pengiriman Notifikasi WhatsApp 1-Klik (wa.me)
  └── Unduh & Cetak Laporan Keuangan (Pemasukan, Tunggakan, Riwayat Transaksi)

Orang Tua / Wali (parent)
  │── Akses Portal Mandiri (/portal)
  │── Isolasi Multi-Tenant: Hanya dapat melihat data anak kandung
  │── Pembayaran Online 1-Klik (Midtrans Snap: QRIS, VA, CC, Retail)
  └── Unduh Dokumen PDF Resmi (Invoice Tagihan & Kuitansi Sah)
```

### 3.2 Dual Identifier Login
Form login pada Admin Panel (`/admin/login`) dan Portal Wali (`/portal/login`) mendukung dua jenis identitas masuk:
- **Email**: format email standar (contoh: `wali@example.com`).
- **Nomor Telepon / WhatsApp**: format lokal maupun internasional (`08xx`, `+628xx`, `628xx`).
- **Pesan Kesalahan Presisi**:
  - Jika input berupa email dan tidak terdaftar: `"Email tidak ditemukan."`
  - Jika input berupa nomor telepon dan tidak terdaftar: `"Nomor telepon tidak ditemukan."`
  - Jika identitas terdaftar namun kata sandi tidak cocok: `"Password salah."`

---

# 4. Master Pos Pembayaran & Matriks Tarif (11 Kategori Resmi)

Sistem mengunci 11 kategori biaya resmi madrasah dengan nominal presisi (disimpan sebagai integer Rupiah):

| No | Kode Pos | Nama Pos Pembayaran | Tipe Billing | Sasaran Kelas | Nominal Resmi |
|---|---|---|---|---|---|
| 1 | `SPP` | SPP Bulanan | Bulanan (*Monthly*) | 7, 8, 9 | **Rp 75.000** / bulan |
| 2 | `PPDB` | Pendaftaran Siswa Baru | Satu Kali (*One-Time*) | Kelas 7 (Siswa Baru) | **Rp 1.190.000** |
| 3 | `DU_TA_8_9` | Daftar Ulang TA Baru Siswa Kelas 8 dan 9 | Tahunan (*Annual*) | Kelas 8, 9 | **Rp 440.000** |
| 4 | `ASTS_GANJIL` | ASTS / PTS Ganjil | Semester Ganjil | 7, 8, 9 | **Rp 75.000** |
| 5 | `ASAS_GANJIL` | ASAS Ganjil | Semester Ganjil | 7, 8, 9 | **Rp 150.000** |
| 6 | `LDKS` | LDKS (Latihan Dasar Kepemimpinan Siswa) | Satu Kali (*One-Time*) | Kelas 7 / 8 | **Rp 450.000** |
| 7 | `DU_GENAP` | Daftar Ulang Semester Genap | Semester Genap | 7, 8, 9 | **Rp 440.000** |
| 8 | `ASTS_GENAP` | ASTS / PTS Genap | Semester Genap | 7, 8, 9 | **Rp 75.000** |
| 9 | `ASATA_PAT` | ASATA / PAT | Semester Genap | 7, 8, 9 | **Rp 150.000** |
| 10 | `STUDY_TOUR` | Study Tour | Satu Kali (*One-Time*) | Kelas 8 / 9 | **Rp 450.000** |
| 11 | `AKHIR_TAHUN` | Akhir Tahun (Wisuda / Pelepasan) | Satu Kali (*One-Time*) | Kelas 9 | **Rp 2.000.000** |

---

# 5. Manajemen Akademik & Data Siswa

### 5.1 Jenjang Kelas & Rombel Dinamis
Struktur madrasah terdiri atas 3 jenjang kelas MTs dengan pembagian rombel khusus:
- **Kelas 7**: Rombel `7.1`, `7.2`, `7.3`
- **Kelas 8**: Rombel `8.1`, `8.2`, `8.3`, `8.4`
- **Kelas 9**: Rombel `9.1`, `9.2`, `9.3`, `9.4`
- **Perilaku Form**: Pilihan rombel difilter secara dinamis setelah tingkat kelas dipilih.

### 5.2 Status Siswa & Manajemen Mutasi (Siswa Pindah)
- **Status Bawaan**: Saat siswa baru ditambahkan atau diimpor, status otomatis diset ke **Aktif** (`active`).
- **Status yang Didukung**: `active` (Aktif), `withdrawn` (Mutasi / Pindah), `graduated` (Alumni / Lulus), dan `inactive` (Nonaktif).
- **Alur Mutasi Siswa Keluar / Pindah**:
  - Super Admin / Admin dapat memicu aksi `[ Mutasi / Pindah ]` dari tabel siswa.
  - Dialog konfirmasi menampilkan total tagihan belum lunas yang akan dibatalkan, serta meminta input tanggal mutasi dan alasan pindah.
  - Seluruh tagihan aktif yang belum dibayar (`unpaid` dan `overdue`) otomatis diubah statusnya menjadi **Dibatalkan (`cancelled`)** dengan catatan alasan mutasi.
  - Seluruh data tagihan dan pembayaran yang sudah lunas (`paid`) **tetap dipertahankan / dikunci** untuk kebutuhan laporan rekapitulasi keuangan tahunan akuntan yayasan.
  - Siswa berstatus `withdrawn` secara otomatis dikecualikan dari generator tagihan SPP bulanan mendatang.
  - Tersedia opsi `[ Aktifkan Kembali ]` untuk mengembalikan status siswa menjadi aktif dengan audit trail lengkap.
- **Header Tabs Filter Status**: Halaman siswa dilengkapi tab filter cepat `🟢 Siswa Aktif`, `🟠 Siswa Mutasi / Pindah`, `🔵 Alumni / Lulus`, dan `Semua Siswa` dengan badge jumlah dinamis.

### 5.3 Import Data Siswa Massal Native Excel (`.xlsx`)
- Format file: Microsoft Excel OpenXML Spreadsheet (`.xlsx`).
- Template resmi: `template_import_siswa_mts_miftahul_ulum.xlsx`.
- Format kolom template:
  - **NISM, Tingkat Kelas, Tahun Masuk**: Format Number (0 Desimal) untuk mencegah notasi ilmiah `e+17`.
  - **NISN, No. Telepon**: Format Text.
  - Header kolom yang bersih: `NISN`, `NISM`, `Nama Lengkap`, `Jenis Kelamin (L/P)`, `Tingkat Kelas`, `Rombel`, `Tahun Masuk`, `Nama Orang Tua / Wali`, `No. WhatsApp / HP Orang Tua`, `Email Orang Tua`, `Alamat`.
- Otomasi akun & Multi-anak (*Siblings*): Sistem otomatis membuat akun login orang tua berbasis Nomor HP / Email. Jika orang tua memiliki lebih dari satu anak di madrasah, seluruh anak otomatis ditautkan ke akun orang tua yang sama (*single parent portal*).

### 5.4 Transisi Tahun Ajaran Baru & Kenaikan Kelas Massal
- Pembuatan Tahun Ajaran Baru: Super Admin cukup memasukkan `start_date` dan `end_date`, nama tahun ajaran (contoh: `2026/2027`) digenerate otomatis.
- Kenaikan Kelas Otomatis:
  - Kelas 7 naik ke Kelas 8.
  - Kelas 8 naik ke Kelas 9.
  - Kelas 9 otomatis lulus (*graduated*).
  - Pengecualian tinggal kelas didukung via form *repeater* dinamis.

---

# 6. Tata Kelola Profil & Tanda Tangan Digital Bendahara

- **Halaman Khusus**: Tersedia di panel Super Admin pada menu `Profile` (`/admin/profile`).
- **Form Input**:
  1. **Nama Bendahara**: Nama lengkap dan gelar pejabat bendahara aktif.
  2. **Tanda Tangan Digital**: Upload berkas gambar tanda tangan (format PNG/JPG transparan).
- **Auto-Redirect ke Dashboard**: Setelah proses penyimpanan profil bendahara berhasil, sistem secara otomatis mengarahkan admin kembali ke **Dashboard Utama** (`/admin`).
- **Dampak Finansial & Dokumen**:
  - Nama dan tanda tangan digital ini secara dinamis dirender ke seluruh berkas PDF:
    - **Kuitansi Pembayaran Sah** (`/docs/receipt/{no}`)
    - **Invoice Tagihan Resmi** (`/docs/invoice/{no}`)
  - Pergantian bendahara tidak merusak kuitansi lama dan langsung memberlakukan tanda tangan baru pada dokumen berikutnya.

---

# 7. Penagihan (Billing) & Skema Pembayaran

### 7.1 Generator Tagihan Massal & Satuan
- Tagihan dapat diterbitkan untuk:
  1. Seluruh siswa aktif madrasah.
  2. Siswa pada tingkat kelas tertentu (misal: Khusus Kelas 7).
  3. Siswa pada rombel tertentu (misal: Khusus Kelas 7.1).
- Dilengkapi mekanisme *Idempotency* untuk mencegah tagihan ganda pada periode yang sama.

### 7.2 Skema Cicilan Tagihan Bernilai Besar (Sistem A)
- Tagihan dengan nominal besar (seperti Pendaftaran Siswa Baru Rp 1.190.000 atau Akhir Tahun Rp 2.000.000) dapat dipecah menjadi beberapa angsuran bulanan (tenor 2 s/d 36 bulan).
- Perhitungan presisi dengan *rounding adjustment* pada angsuran terakhir.

### 7.3 Status Tagihan
- 🟡 **Belum Bayar (`unpaid`)**: Belum lunas dan belum melewati jatuh tempo.
- 🟢 **Lunas (`paid`)**: Sisa tagihan telah Rp 0.
- 🔴 **Terlambat (`overdue`)**: Melewati jatuh tempo dan masih memiliki sisa tunggakan.
- ⚪ **Dibatalkan (`cancelled`)**: Tagihan dibatalkan secara sistem (misal: akibat siswa mutasi keluar/pindah).

---

# 8. Kanal Pembayaran & Kuitansi Sah

### 8.1 Pembayaran Online Midtrans Snap
- Metode pembayaran yang didukung: QRIS (GoPay, ShopeePay, DANA, OVO, LinkAja), Virtual Account (BRI, BCA, BNI, Mandiri, Permata), Kartu Kredit/Debit, dan Gerai Retail (Indomaret/Alfamart).
- Verifikasi otomatis secara *real-time* via webhook listener (`/api/midtrans/webhook`).

### 8.2 Loket Kasir Pembayaran Tunai / Manual
- Admin menerima pembayaran tunai atau transfer langsung ke rekening BRI madrasah:
  - Bank BRI: `176901000210569` a/n *Madrasah Tsanawiyah Miftahul Ulum*.
- Validasi *anti-overpayment* dan fitur unggah bukti bayar fisik.

### 8.3 Kuitansi & Invoice PDF Resmi
- Format nomor kuitansi unik: `PAY-MAN-{NIS}-{YYYYMMDD}-{RAND}` atau `PAY-MID-{NIS}-{YYYYMMDD}-{RAND}`.
- Format nomor invoice: `INV-{NIS}-{YYYYMM}-{RAND}`.
- Memuat kop surat resmi yayasan, stempel bulat digital madrasah, tanda tangan digital bendahara, dan nominal terbilang bahasa Indonesia.

---

# 9. Notifikasi WhatsApp 1-Klik (`wa.me`)

- Tombol **"Kirim WA"** pada panel admin langsung menyusun format pesan resmi institusi:
  - Salam pembuka islami.
  - Rincian identitas santri & rincian nominal tagihan / pembayaran.
  - Batas waktu jatuh tempo & instruksi pembayaran.
  - Tautan langsung unduh dokumen Invoice / Kuitansi PDF publik tanpa perlu login.
- Normalisasi nomor handphone otomatis ke format internasional `628xx`.

---

# 10. Audit Trail & Laporan Keuangan

### 10.1 Audit Trail (Eksklusif Super Admin)
- Mencatat seluruh operasi CRUD finansial & administratif (termasuk mutasi siswa dan pembatalan tagihan).
- Mencatat pelaku (*actor*), IP address, User Agent, waktu kejadian, serta perbandingan nilai sebelum (*old*) dan sesudah (*new*).
- Bersifat permanen (*immutable*).

### 10.2 Laporan Keuangan Eksekutif
- **Laporan Pemasukan**: Rekapitulasi per pos pembayaran dan per metode bayar.
- **Laporan Tunggakan**: Rekapitulasi daftar siswa yang belum melunasi kewajiban beserta status keterlambatan.
- **Laporan Riwayat Pembayaran**: Log seluruh transaksi masuk.
- Ekspor berkas: PDF Berstempel Resmi dan Excel / CSV Clean.

---

# 11. Kualitas & Pengujian Otomatis

- Seluruh alur bisnis dilindungi oleh rangkaian uji otomatis (*Automated Test Suite*): **48 Tests, 175 Assertions Passing 100%**.
- Standar penulisan kode terstandarisasi dengan **Laravel Pint**.