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
| Total Migrasi | 25 Migrasi Database Aktif |
| Konvensi Format Uang | Unsigned Big Integer (Rupiah murni tanpa floating point) |
| Integritas Transaksional | Foreign Key Constraints + Database Transactions (`DB::transaction`) |

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
└────────────────┘                 │(NISN, NISM, 7-9│
                                   └───────┬────────┘
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
Menyimpan akun autentikasi seluruh pengguna aplikasi (Super Admin, Admin, dan Orang Tua).

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
| `email` | VARCHAR(255) | Ya | INDEX | Email kontak utama |
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
| `nisn` | VARCHAR(30) | Tidak | UNIQUE | Nomor Induk Siswa Nasional |
| `nism` | VARCHAR(30) | Ya | INDEX | Nomor Induk Siswa Madrasah |
| `full_name` | VARCHAR(255) | Tidak | INDEX | Nama lengkap siswa |
| `gender` | ENUM('L','P') | Tidak | — | Jenis Kelamin (L = Laki-laki, P = Perempuan) |
| `class_level` | TINYINT UNSIGNED | Tidak | INDEX | Tingkat Kelas (`7`, `8`, `9`) |
| `rombel` | VARCHAR(10) | Tidak | INDEX | Rombel dinamis (`7.1`-`7.3`, `8.1`-`8.4`, `9.1`-`9.4`) |
| `entry_year` | YEAR | Ya | — | Tahun Masuk |
| `status` | ENUM | Tidak | INDEX | `active`, `graduated`, `withdrawn`, `inactive` |
| `email` | VARCHAR(255) | Ya | — | Email kontak siswa |
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
| `is_active` | BOOLEAN | Tidak | INDEX | Status keaktifan pos tagihan |
| `allows_installment` | BOOLEAN | Tidak | — | Apakah mendukung cicilan |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.7 `payment_type_prices`
Menyimpan matriks tarif resmi berdasarkan tingkat kelas dan rombel.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Tarif |
| `payment_type_id` | BIGINT UNSIGNED | Tidak | FK (`payment_types.id`) | Relasi pos pembayaran |
| `academic_year_id` | BIGINT UNSIGNED | Tidak | FK (`academic_years.id`) | Relasi tahun ajaran |
| `class_level` | TINYINT UNSIGNED | Ya | INDEX | Tingkat Kelas (7, 8, 9 atau null untuk all) |
| `rombel` | VARCHAR(10) | Ya | INDEX | Rombel spesifik atau null untuk all rombel |
| `amount` | BIGINT UNSIGNED | Tidak | — | Nominal tarif resmi (Rupiah) |
| `created_at` | TIMESTAMP | Ya | — | Waktu pembuatan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.8 `bills`
Menyimpan tagihan yang diterbitkan kepada siswa.

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Tagihan |
| `bill_number` | VARCHAR(100) | Tidak | UNIQUE | Nomor unik tagihan (`BILL-...`) |
| `student_id` | BIGINT UNSIGNED | Tidak | FK (`students.id`), INDEX | Siswa penerima tagihan |
| `payment_type_id` | BIGINT UNSIGNED | Tidak | FK (`payment_types.id`) | Pos pembayaran |
| `academic_year_id` | BIGINT UNSIGNED | Tidak | FK (`academic_years.id`) | Tahun ajaran tagihan |
| `month` | TINYINT UNSIGNED | Ya | INDEX | Bulan tagihan (1-12 untuk SPP) |
| `year` | YEAR | Ya | INDEX | Tahun kalender |
| `due_date` | DATE | Tidak | INDEX | Batas akhir pembayaran |
| `amount` | BIGINT UNSIGNED | Tidak | — | Total nilai tagihan (Rupiah) |
| `paid_amount` | BIGINT UNSIGNED | Tidak | — | Total yang sudah dibayar |
| `status` | ENUM | Tidak | INDEX | `unpaid`, `paid`, `overdue`, `cancelled` |
| `installment_number` | TINYINT UNSIGNED | Ya | — | Angsuran ke-N (untuk cicilan) |
| `total_installments` | TINYINT UNSIGNED | Ya | — | Total tenor angsuran |
| `notes` | TEXT | Ya | — | Catatan khusus / alasan pembatalan / mutasi |
| `created_at` | TIMESTAMP | Ya | — | Waktu penerbitan |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.9 `payments`
Menyimpan catatan transaksi pembayaran yang berhasil masuk (baik via loket kasir maupun online Midtrans).

| Kolom | Tipe Data | Nullable | Index / Constraint | Keterangan |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Primary Key | ID Transaksi |
| `payment_number` | VARCHAR(100) | Tidak | UNIQUE | Nomor Kuitansi (`PAY-...`) |
| `bill_id` | BIGINT UNSIGNED | Tidak | FK (`bills.id`), INDEX | Tagihan yang dibayar |
| `user_id` | BIGINT UNSIGNED | Tidak | FK (`users.id`) | Akun pencatat / pembayar |
| `amount` | BIGINT UNSIGNED | Tidak | — | Nominal yang disetorkan |
| `payment_method` | ENUM | Tidak | INDEX | `midtrans`, `cash`, `bank_transfer` |
| `payment_type` | ENUM | Tidak | — | `full` (lunas langsung) / `installment` |
| `status` | ENUM | Tidak | INDEX | `pending`, `paid`, `failed`, `expired` |
| `midtrans_order_id` | VARCHAR(100) | Ya | INDEX | Order ID transaksi Midtrans |
| `midtrans_snap_token`| VARCHAR(255) | Ya | — | Token Snap Popup |
| `midtrans_payment_type`| VARCHAR(50)| Ya | — | Jenis channel (qris, bank_transfer, dll.) |
| `evidence_path` | VARCHAR(255) | Ya | — | Path berkas slip/bukti transfer |
| `paid_at` | TIMESTAMP | Ya | INDEX | Waktu lunas diverifikasi |
| `notes` | TEXT | Ya | — | Catatan transaksi kasir |
| `created_at` | TIMESTAMP | Ya | — | Waktu transaksi |
| `updated_at` | TIMESTAMP | Ya | — | Waktu pembaruan |

---

## 3.10 `invoices` & `receipts`
Menyimpan arsip dokumen PDF resmi tagihan dan kuitansi pembayaran beserta snapshot historis pejabat bendahara (Immutability).

- **`invoices`**: `id`, `invoice_number` (UNIQUE), `bill_id` (FK), `file_path`, `treasurer_name` (Snapshot Nama), `treasurer_signature_path` (Snapshot TTD), `generated_at`, timestamps.
- **`receipts`**: `id`, `receipt_number` (UNIQUE), `payment_id` (FK), `file_path`, `treasurer_name` (Snapshot Nama), `treasurer_signature_path` (Snapshot TTD), `generated_at`, timestamps.

---

## 3.11 `audit_logs` & `whatsapp_logs`
Menyimpan log audit keamanan dan riwayat pengiriman notifikasi WhatsApp.

- **`audit_logs`**: `id`, `user_id` (FK nullable), `actor_name`, `actor_role`, `action`, `auditable_type`, `auditable_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, timestamps.
- **`whatsapp_logs`**: `id`, `recipient_phone`, `recipient_name`, `message_type`, `message_content`, `sent_at`, `status`, timestamps.