# SCHEMA.md — Spesifikasi Skema Database & Model Data
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> **Database Schema & Data Model Specification**
>
> Dokumen ini mendefinisikan struktur tabel, relasi antar entitas, kolom, tipe data, indeks, dan aturan integritas data pada Sistem Administrasi & Pembayaran MTs. Miftahul 'Ulum.

---

# 1. Informasi Basis Data

| Item | Detail |
|---|---|
| Sistem Database | MySQL 8.0+ / MariaDB 10.4+ |
| ORM | Laravel Eloquent |
| Karakter Set / Collation | `utf8mb4_unicode_ci` |
| Total Migrasi | 26 Migrasi Database Aktif |
| Konvensi Format Uang | Unsigned Big Integer (Rupiah murni tanpa floating point) |
| Integritas Transaksional | Foreign Key Constraints + Database Transactions (`DB::transaction`) |
| Audit Trail Trigger | Model Observers (`app/Observers/`) |

---

# 2. Diagram Hubungan Entitas (Entity-Relationship Overview)

```text
┌────────────────┐       1:1       ┌────────────────┐
│     users      ├─────────────────┤    parents     │
│ (phone, sign)  │                 │                │
└───────┬────────┘                 └───────┬────────┘
        │                                  │
        │ 1:N                              │ 1:N
        ▼                                  ▼
┌────────────────┐                 ┌────────────────┐
│   audit_logs   │                 │    students    │
│(Observers Auto)│                 │(NIS, NISM, 7-9)│
└────────────────┘                 └───────┬────────┘
                                           │
         ┌─────────────────────────────────┼─────────────────────────────────┐
         │ 1:N                             │ 1:N                             │ 1:N
         ▼                                 ▼                                 ▼
┌────────────────────────┐       ┌──────────────────┐       ┌────────────────────────┐
│student_academic_years  │       │      bills       │       │payment_due_date_overrid│
└────────────────────────┘       │(unpaid/paid/over)│       └────────────────────────┘
                                 └─────────┬────────┘
                                           │
         ┌─────────────────────────────────┼─────────────────────────────────┐
         │ 1:N                             │ 1:1                             │
         ▼                                 ▼                                 ▼
┌────────────────┐                 ┌────────────────┐               ┌────────────────┐
│    payments    │                 │    invoices    │               │  whatsapp_logs │
│(midtrans/cash) │                 └────────────────┘               └────────────────┘
└───────┬────────┘
        │ 1:1
        ▼
┌────────────────┐
│    receipts    │
└────────────────┘
```

---

# 3. Spesifikasi Rinci Tabel Database

---

## 3.1 `users`
Menyimpan akun autentikasi seluruh pengguna aplikasi (Super Admin Singleton, Admin Staff, dan Orang Tua).

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key (Auto Increment) | ID Pengguna |
| `name` | VARCHAR(255) | Tidak | — | Nama akun / Nama lengkap pejabat |
| `email` | VARCHAR(255) | Tidak | UNIQUE | Alamat email login |
| `phone` | VARCHAR(30) | Ya | UNIQUE, INDEX | Nomor HP / WhatsApp login |
| `password` | VARCHAR(255) | Tidak | — | Kata sandi hash (Bcrypt 12 rounds) |
| `role` | ENUM | Tidak | INDEX | `super_admin`, `admin`, `parent` |
| `signature_path` | VARCHAR(255) | Ya | — | Path berkas tanda tangan digital bendahara |
| `email_verified_at` | TIMESTAMP | Ya | — | Waktu verifikasi email |
| `remember_token` | VARCHAR(100) | Ya | — | Token persistensi sesi login |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.2 `parents`
Menyimpan data profil orang tua / wali santri yang terhubung dengan akun login.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Profil Wali |
| `user_id` | BIGINT UNSIGNED | Tidak | FK (`users.id`), UNIQUE | Relasi akun login |
| `full_name` | VARCHAR(255) | Tidak | INDEX | Nama lengkap orang tua / wali |
| `phone` | VARCHAR(30) | Ya | INDEX | Nomor HP / WhatsApp kontak |
| `contact_email` | VARCHAR(255) | Ya | INDEX | Email kontak utama |
| `address` | TEXT | Ya | — | Alamat domisili |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.3 `academic_years`
Menyimpan data periode tahun ajaran madrasah.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Periode |
| `name` | VARCHAR(20) | Tidak | UNIQUE | Nama tahun ajaran (contoh: `2026/2027`) |
| `start_date` | DATE | Tidak | — | Tanggal mulai periode |
| `end_date` | DATE | Tidak | — | Tanggal berakhir periode |
| `is_active` | BOOLEAN | Tidak | INDEX | Status aktif (`1` = Aktif, `0` = Nonaktif) |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.4 `students`
Menyimpan data induk santri / siswa madrasah.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Santri |
| `parent_id` | BIGINT UNSIGNED | Ya | FK (`parents.id`), INDEX | Relasi ke wali murid |
| `nis` | VARCHAR(30) | Tidak | UNIQUE | Nomor Induk Siswa Nasional (NISN) |
| `nism` | VARCHAR(30) | Ya | INDEX | Nomor Induk Siswa Madrasah |
| `full_name` | VARCHAR(255) | Tidak | INDEX | Nama lengkap siswa |
| `gender` | ENUM('male','female') | Tidak | — | Jenis Kelamin |
| `birth_date` | DATE | Ya | — | Tanggal lahir |
| `class_level` | TINYINT UNSIGNED | Tidak | INDEX | Tingkat Kelas (`7`, `8`, `9`) |
| `rombel` | VARCHAR(10) | Tidak | INDEX | Rombel dinamis (`7.1`-`7.3`, `8.1`-`8.4`, `9.1`-`9.4`) |
| `entry_year` | YEAR | Ya | — | Tahun Masuk |
| `status` | ENUM | Tidak | INDEX | `active`, `graduated`, `withdrawn`, `inactive` |
| `phone` | VARCHAR(30) | Ya | — | Nomor telepon kontak siswa |
| `address` | TEXT | Ya | — | Alamat tempat tinggal |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.5 `student_academic_years`
Menyimpan riwayat penempatan kelas dan rombel siswa di setiap tahun ajaran (Histori Akademik).

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Riwayat |
| `student_id` | BIGINT UNSIGNED | Tidak | FK (`students.id`), INDEX | ID Siswa |
| `academic_year_id` | BIGINT UNSIGNED | Tidak | FK (`academic_years.id`), INDEX | ID Tahun Ajaran |
| `class_level` | TINYINT UNSIGNED | Tidak | — | Kelas pada periode tersebut |
| `rombel` | VARCHAR(10) | Tidak | — | Rombel pada periode tersebut |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.6 `payment_types`
Master 11 kategori pos biaya pembayaran resmi madrasah.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Jenis Pembayaran |
| `code` | VARCHAR(50) | Tidak | UNIQUE | Kode unik pos (`SPP`, `PPDB`, `LDKS`, dll.) |
| `name` | VARCHAR(255) | Tidak | INDEX | Nama pos pembayaran |
| `description` | TEXT | Ya | — | Keterangan rinci pos biaya |
| `billing_type` | ENUM | Tidak | INDEX | `monthly`, `one_time`, `annual`, `semester` |
| `default_amount` | BIGINT UNSIGNED | Tidak | — | Nominal bawaan (Rupiah) |
| `default_due_day` | TINYINT UNSIGNED | Tidak | — | Tanggal jatuh tempo default (tgl 1-31) |
| `is_active` | BOOLEAN | Tidak | INDEX | Status keaktifan pos tagihan |
| `allows_installment` | BOOLEAN | Tidak | — | Apakah mendukung cicilan |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.7 `bills`
Menyimpan tagihan yang diterbitkan kepada siswa.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Tagihan |
| `bill_number` | VARCHAR(100) | Tidak | UNIQUE | Nomor unik tagihan (`INV-...`) |
| `student_id` | BIGINT UNSIGNED | Tidak | FK (`students.id`), INDEX | Siswa penerima tagihan |
| `parent_id` | BIGINT UNSIGNED | Ya | FK (`parents.id`), INDEX | Wali siswa |
| `payment_type_id` | BIGINT UNSIGNED | Tidak | FK (`payment_types.id`) | Pos pembayaran |
| `academic_year_id` | BIGINT UNSIGNED | Tidak | FK (`academic_years.id`) | Tahun ajaran tagihan |
| `billing_period` | DATE | Ya | INDEX | Periode bulan tagihan |
| `due_date` | DATE | Tidak | INDEX | Batas akhir pembayaran |
| `amount` | BIGINT UNSIGNED | Tidak | — | Total nilai tagihan (Rupiah) |
| `paid_amount` | BIGINT UNSIGNED | Tidak | — | Total yang sudah dibayar |
| `outstanding_amount`| BIGINT UNSIGNED | Tidak | INDEX | Sisa tagihan belum dibayar |
| `status` | ENUM | Tidak | INDEX | `unpaid`, `paid`, `overdue`, `cancelled` |
| `is_installment` | BOOLEAN | Tidak | — | Apakah bagian dari skema cicilan |
| `installment_number` | TINYINT UNSIGNED | Ya | — | Angsuran ke-N (untuk cicilan) |
| `tenor_count` | TINYINT UNSIGNED | Ya | — | Total tenor angsuran |
| `notes` | TEXT | Ya | — | Catatan khusus / alasan mutasi / pembatalan |
| `created_at` | TIMESTAMP | Ya | — | Waktu penerbitan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.8 `payments`
Menyimpan catatan transaksi pembayaran yang berhasil masuk (baik via loket kasir maupun online Midtrans).

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Transaksi |
| `payment_number` | VARCHAR(100) | Tidak | UNIQUE | Nomor Kuitansi (`PAY-...`) |
| `bill_id` | BIGINT UNSIGNED | Tidak | FK (`bills.id`), INDEX | Tagihan yang dibayar |
| `student_id` | BIGINT UNSIGNED | Tidak | FK (`students.id`), INDEX | Siswa pembayar |
| `parent_id` | BIGINT UNSIGNED | Ya | FK (`parents.id`), INDEX | Wali siswa |
| `recorder_id` | BIGINT UNSIGNED | Ya | FK (`users.id`) | Staf internal pencatat |
| `amount` | BIGINT UNSIGNED | Tidak | — | Nominal yang disetorkan |
| `source` | ENUM | Tidak | INDEX | `midtrans`, `manual` |
| `method` | VARCHAR(50) | Ya | INDEX | `cash`, `bank_transfer`, `qris`, `cstore`, dll. |
| `status` | ENUM | Tidak | INDEX | `pending`, `success`, `failed`, `expired` |
| `transaction_reference`| VARCHAR(100)| Ya | INDEX | Referensi ID transaksi / Midtrans Order ID |
| `snap_token` | VARCHAR(255) | Ya | — | Token Midtrans Snap |
| `evidence_path` | VARCHAR(255) | Ya | — | Path slip bukti transfer fisik |
| `notes` | TEXT | Ya | — | Catatan kasir |
| `paid_at` | TIMESTAMP | Ya | INDEX | Waktu pembayaran terverifikasi |
| `created_at` | TIMESTAMP | Ya | — | Waktu transaksi |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.9 `invoices` & `receipts`
Menyimpan arsip dokumen PDF resmi tagihan dan kuitansi pembayaran beserta snapshot historis pejabat bendahara (Immutability).

- **`invoices`**: `id`, `bill_id` (FK), `invoice_number` (UNIQUE), `file_path`, `treasurer_name` (Snapshot Nama), `treasurer_signature_path` (Snapshot TTD), timestamps.
- **`receipts`**: `id`, `payment_id` (FK), `receipt_number` (UNIQUE), `file_path`, `treasurer_name` (Snapshot Nama), `treasurer_signature_path` (Snapshot TTD), timestamps.

---

## 3.10 `audit_logs` & `whatsapp_logs`
Menyimpan log audit keamanan dan riwayat pengiriman notifikasi WhatsApp.

- **`audit_logs`**: `id`, `user_id` (FK nullable), `user_name`, `user_role`, `action`, `auditable_type`, `auditable_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, timestamps.
- **`whatsapp_logs`**: `id`, `student_id` (FK nullable), `parent_id` (FK nullable), `phone_number`, `recipient_name`, `message_type`, `amount`, `message_content`, `status`, `sent_at`, timestamps.