# SCHEMA.md — Sistem Administrasi Pembayaran Pondok

> **Database Schema & Data Model Specification**
>
> Dokumen ini mendefinisikan struktur data, entity, relationship, constraint, status, index, dan aturan persistence untuk Sistem Administrasi Pembayaran Pondok.

---

# 1. Document Information

| Item | Detail |
|---|---|
| Nama Sistem | Sistem Administrasi Pembayaran Pondok |
| Dokumen | SCHEMA |
| Versi | 1.0 — Prototype |
| Database | MySQL |
| ORM | Laravel Eloquent |
| Existing Project | Laravel 12 + Filament 5 + Midtrans |
| Status | Target Schema / Prototype |

---

# 2. Purpose

`SCHEMA.md` menjadi sumber referensi utama untuk:

- entity database;
- tabel;
- kolom;
- tipe data;
- primary key;
- foreign key;
- relationship;
- cardinality;
- unique constraint;
- index;
- status;
- data lifecycle;
- financial data integrity.

Dokumen ini harus digunakan bersama:

- `PRD.md`
- `ARCHITECTURE.md`
- `DESIGN.md`
- `RULES.md`
- `AGENTS.md`

---

# 3. Requirement Status Legend

### ✅ CONFIRMED

Requirement yang sudah disepakati.

### ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Keputusan sementara untuk prototype.

**Tetap wajib diimplementasikan.**

Status temporary tidak berarti:

> "boleh tidak dibuat."

Status temporary berarti:

> "buat sekarang, tetapi tandai karena masih perlu dikonfirmasi client."

### 🔶 OPEN

Belum ada keputusan final.

Jangan mengarang business rule baru.

### ❌ OUT OF SCOPE

Tidak termasuk prototype.

---

# 4. Database Design Principles

Database harus:

1. menjaga integritas financial data;
2. menggunakan foreign key;
3. menggunakan index pada kolom yang sering dicari;
4. menggunakan unique constraint untuk business identity;
5. memisahkan master data dan transaction data;
6. mempertahankan histori pembayaran;
7. tidak menghapus transaksi finansial secara sembarangan;
8. mendukung satu orang tua dengan banyak santri;
9. mendukung cicilan;
10. mendukung pembayaran online dan manual;
11. mendukung tahun ajaran;
12. mendukung configurable payment type;
13. mendukung pricing berdasarkan kelompok;
14. mendukung individual pricing override;
15. mendukung individual due date override;
16. mendukung audit;
17. mendukung email tracking;
18. mendukung document tracking.

---

# 5. Existing Schema Context

Project existing saat ini menggunakan model:

```text
User
Product
Order
OrderDetail
Payment
```

README existing menyebut struktur model tersebut secara eksplisit.

Project existing juga memiliki:

```text
database/
├── migrations/
└── seeders/
```

serta controller:

```text
OrderController
PaymentController
```

dengan `PaymentController` digunakan untuk webhook Midtrans.

---

# 6. Existing Schema — Reference

Struktur existing secara konseptual:

```text
users
  ↓
orders
  ↓
order_details
  ↓
products

orders
  ↓
payments
```

Existing `products` digunakan sebagai konsep layanan/barang/tagihan.

Existing `orders` digunakan sebagai tagihan/order utama.

Existing `order_details` menyimpan item dalam order.

Existing `payments` menyimpan transaksi Midtrans.

Struktur ini merupakan **pondasi existing**, bukan target final schema.

---

# 7. Target Domain Model

Target schema prototype menggunakan entity utama:

```text
users
roles / role representation
parents
students
academic_years
payment_types
payment_type_prices
student_payment_overrides
payment_due_date_overrides
bills
bill_items
payments
payment_evidences
invoices
receipts
email_logs
notifications
audit_logs
```

Tidak semua entity harus menjadi tabel terpisah apabila implementasi existing memiliki pendekatan yang lebih tepat.

Namun domain concept tersebut harus tetap dapat direpresentasikan.

---

# 8. Entity Relationship Overview

High-level relationship:

```text
                    ┌──────────────┐
                    │    users     │
                    └──────┬───────┘
                           │
                ┌──────────┴──────────┐
                │                     │
                ↓                     ↓
           Super Admin/Admin       Parent
                                      │
                                      │ 1:N
                                      ↓
                                  Students
                                      │
                       ┌──────────────┼──────────────┐
                       │              │              │
                       ↓              ↓              ↓
                Academic Year    Bills          Overrides
                                      │
                         ┌────────────┼────────────┐
                         │            │            │
                         ↓            ↓            ↓
                    Bill Items    Payments      Invoice
                                      │
                                      ↓
                                   Receipt
```

---

# 9. `users`

## Purpose

Menyimpan seluruh akun yang dapat login ke aplikasi.

User dapat berupa:

- Super Admin;
- Admin;
- Orang Tua.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID user |
| `name` | VARCHAR(255) | NO | | Nama pengguna |
| `email` | VARCHAR(255) | NO | UNIQUE | Email login |
| `password` | VARCHAR(255) | NO | | Password hash |
| `role` | VARCHAR/ENUM | NO | INDEX | Role pengguna |
| `email_verified_at` | TIMESTAMP | YES | | Waktu verifikasi email |
| `remember_token` | VARCHAR(100) | YES | | Laravel remember token |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |

---

## Role Values

Prototype:

```text
super_admin
admin
parent
```

Naming final dapat disesuaikan dengan implementation.

---

## Constraints

- `email` wajib unique.
- Password harus disimpan dalam bentuk hash.
- Role wajib valid.
- User tidak boleh memiliki lebih dari satu role jika implementation menggunakan single-role model.

---

# 10. `parents`

## Purpose

Menyimpan profil orang tua/wali.

Parent memiliki account pada `users`.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID parent |
| `user_id` | BIGINT UNSIGNED | NO | FK, UNIQUE | User account |
| `full_name` | VARCHAR(255) | NO | INDEX | Nama lengkap |
| `phone` | VARCHAR(30) | YES | INDEX | Nomor HP |
| `email` | VARCHAR(255) | YES | INDEX | Email kontak |
| `address` | TEXT | YES | | Alamat |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |

---

## Relationship

```text
User 1 ───── 1 Parent
```

Satu parent profile hanya terhubung dengan satu account.

---

# 11. `students`

## Purpose

Menyimpan data santri.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID internal |
| `parent_id` | BIGINT UNSIGNED | YES | FK, INDEX | Parent utama |
| `nis` | VARCHAR(100) | NO | UNIQUE | Nomor induk santri |
| `full_name` | VARCHAR(255) | NO | INDEX | Nama santri |
| `gender` | VARCHAR/ENUM | NO | | Jenis kelamin |
| `class_level` | TINYINT UNSIGNED | NO | INDEX | Kelas 1–9 |
| `rombel` | VARCHAR(10) | NO | INDEX | A/B |
| `entry_year` | YEAR | YES | | Tahun masuk |
| `status` | VARCHAR/ENUM | NO | INDEX | Status santri |
| `email` | VARCHAR(255) | YES | | Email jika diperlukan |
| `phone` | VARCHAR(30) | YES | | Nomor kontak jika diperlukan |
| `address` | TEXT | YES | | Alamat |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |
| `deleted_at` | TIMESTAMP | YES | INDEX | Soft delete jika digunakan |

---

# 12. Student Status

Minimal:

```text
active
graduated
withdrawn
inactive
```

Hanya:

```text
active
```

yang secara default menjadi target billing otomatis.

---

# 13. Parent–Student Relationship

Requirement:

> Satu orang tua dapat memiliki 2–3 anak atau lebih yang semuanya menjadi santri.

Untuk prototype, relationship minimal:

```text
Parent 1 ───── N Students
```

Contoh:

```text
Parent
  │
  ├── Student A
  ├── Student B
  └── Student C
```

---

# 14. Parent–Student Workflow

System harus memungkinkan:

### Parent-first

```text
Create Parent
↓
Create Student
↓
Attach Student
```

### Student-first

```text
Create Student
↓
Create/Select Parent
↓
Attach Student
```

Student dapat sementara belum memiliki parent.

---

# 15. Parent Relationship Future Consideration

## 🔶 OPEN REQUIREMENT

Apakah satu santri dapat memiliki lebih dari satu wali/orang tua masih belum ditentukan.

Prototype dapat menggunakan:

```text
students.parent_id
```

untuk menyederhanakan implementasi.

Jika client kemudian membutuhkan:

- ayah;
- ibu;
- wali;
- beberapa parent account;

schema dapat dikembangkan menjadi pivot:

```text
parent_student
```

Jangan menambahkan kompleksitas tersebut tanpa requirement nyata.

---

# 16. `academic_years`

## Purpose

Menyimpan periode tahun ajaran.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `name` | VARCHAR(20) | NO | UNIQUE | Contoh 2026/2027 |
| `start_date` | DATE | NO | | Awal tahun ajaran |
| `end_date` | DATE | NO | | Akhir tahun ajaran |
| `is_active` | BOOLEAN | NO | INDEX | Tahun ajaran aktif |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |

---

# 17. Academic Year Status

Prototype menggunakan:

```text
is_active = true
```

untuk menandai tahun ajaran aktif.

Hanya boleh ada satu academic year aktif pada satu waktu.

Database/application harus memastikan invariant tersebut.

---

# 18. Academic Year Transition

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Super Admin dapat menjalankan:

> Mulai Tahun Ajaran Baru.

Action:

```text
Create Academic Year
↓
Set Active
↓
Close Previous Year
↓
Promote Students
```

Data lama tidak dihapus.

---

# 19. Student Academic Placement

Untuk prototype, class dan rombel disimpan pada student.

Contoh:

```text
class_level = 5
rombel = A
```

Merepresentasikan:

> 5-A

---

# 20. Academic History

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Karena sistem menggunakan tahun ajaran, architecture idealnya harus dapat mempertahankan histori perpindahan kelas.

Untuk prototype sederhana, histori dapat diwakili oleh:

```text
academic_year_id
class_level
rombel
```

pada data enrollment/placement.

Jika implementasi menggunakan tabel terpisah:

```text
student_academic_years
```

maka relationship menjadi:

```text
Student
   │
   ├── 2025/2026 → 4-A
   ├── 2026/2027 → 5-A
   └── 2027/2028 → 6-A
```

Pendekatan ini lebih disarankan jika histori tahun ajaran menjadi penting.

---

# 21. Recommended `student_academic_years`

Untuk menjaga histori, target schema **disarankan** menggunakan table:

`student_academic_years`

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `student_id` | BIGINT UNSIGNED | NO | FK | Santri |
| `academic_year_id` | BIGINT UNSIGNED | NO | FK | Tahun ajaran |
| `class_level` | TINYINT UNSIGNED | NO | | Kelas |
| `rombel` | VARCHAR(10) | NO | | A/B |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |

Unique:

```text
(student_id, academic_year_id)
```

---

# 22. Why Academic Placement Is Separate

Memisahkan placement dari student master memungkinkan:

```text
Student
```

tetap memiliki identity yang sama sepanjang masa menjadi santri.

Sementara:

```text
StudentAcademicYear
```

menyimpan posisi santri pada periode tertentu.

Ini penting untuk historical reporting.

---

# 23. `payment_types`

## Purpose

Master jenis pembayaran.

Contoh:

```text
SPP
Uang Gedung
Uang Makan
Kitab
Seragam
```

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `code` | VARCHAR(100) | NO | UNIQUE | Identifier |
| `name` | VARCHAR(255) | NO | INDEX | Nama |
| `description` | TEXT | YES | | Deskripsi |
| `billing_type` | VARCHAR/ENUM | NO | INDEX | Jenis billing |
| `is_active` | BOOLEAN | NO | INDEX | Aktif/nonaktif |
| `allows_installment` | BOOLEAN | NO | | Dukungan cicilan |
| `created_by` | BIGINT UNSIGNED | YES | FK | Creator |
| `updated_by` | BIGINT UNSIGNED | YES | FK | Last updater |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |
| `deleted_at` | TIMESTAMP | YES | | Soft delete |

---

# 24. Payment Type Billing Type

Contoh:

```text
monthly
one_time
custom
```

Untuk prototype:

### SPP

```text
monthly
```

### Pembayaran lain

Dapat menggunakan:

```text
one_time
custom
```

sesuai kebutuhan.

---

# 25. Payment Type Ownership

`created_by` dan `updated_by` mengarah ke:

```text
users.id
```

Namun authorization menentukan bahwa hanya Super Admin yang boleh membuat/mengubah konfigurasi tertentu.

---

# 26. `payment_type_prices`

## Purpose

Menyimpan nominal pembayaran berdasarkan kelompok santri.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `payment_type_id` | BIGINT UNSIGNED | NO | FK | Jenis pembayaran |
| `academic_year_id` | BIGINT UNSIGNED | NO | FK | Tahun ajaran |
| `class_level` | TINYINT UNSIGNED | NO | INDEX | Kelas |
| `rombel` | VARCHAR(10) | YES | INDEX | A/B |
| `amount` | BIGINT UNSIGNED | NO | | Nominal |
| `effective_from` | DATE | YES | | Awal berlaku |
| `effective_until` | DATE | YES | | Akhir berlaku |
| `created_by` | BIGINT UNSIGNED | YES | FK | Creator |
| `created_at` | TIMESTAMP | YES | | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | | Waktu diubah |

---

# 27. Group Pricing

Contoh:

```text
Payment Type: SPP
Academic Year: 2026/2027

5-A → 500000
5-B → 550000
6-A → 600000
6-B → 600000
```

Nominal disimpan sebagai integer rupiah tanpa floating point.

---

# 28. Price History

Harga yang sudah digunakan oleh financial transaction tidak boleh berubah secara retroaktif.

Contoh:

```text
SPP August
amount = 500000
```

Jika September harga berubah:

```text
September
amount = 550000
```

August tetap:

```text
500000
```

Bill menyimpan snapshot nominalnya.

---

# 29. `student_payment_overrides`

## Purpose

Menyimpan harga khusus seorang santri.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `student_id` | BIGINT UNSIGNED | NO | FK | Santri |
| `payment_type_id` | BIGINT UNSIGNED | NO | FK | Jenis pembayaran |
| `academic_year_id` | BIGINT UNSIGNED | NO | FK | Tahun ajaran |
| `amount` | BIGINT UNSIGNED | NO | | Nominal khusus |
| `effective_from` | DATE | YES | | Mulai berlaku |
| `effective_until` | DATE | YES | | Akhir berlaku |
| `reason` | TEXT | YES | | Alasan |
| `created_by` | BIGINT UNSIGNED | YES | FK | Super Admin |
| `updated_by` | BIGINT UNSIGNED | YES | FK | Super Admin |
| `created_at` | TIMESTAMP | YES | | |
| `updated_at` | TIMESTAMP | YES | | |

---

# 30. Pricing Resolution

Urutan:

```text
Student Override
       ↓
Class + Rombel Price
       ↓
Payment Type Default / configured price
```

Jika tidak ada nominal yang dapat digunakan:

> Bill tidak boleh dibuat secara silent.

---

# 31. `payment_due_date_overrides`

## Purpose

Menyimpan override jatuh tempo.

Karena Super Admin dapat mengatur tanggal:

- per jenis pembayaran;
- per santri.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `payment_type_id` | BIGINT UNSIGNED | NO | FK | Jenis pembayaran |
| `student_id` | BIGINT UNSIGNED | YES | FK | Jika khusus santri |
| `academic_year_id` | BIGINT UNSIGNED | YES | FK | Tahun ajaran |
| `due_day` | TINYINT UNSIGNED | YES | | Hari dalam bulan |
| `due_date` | DATE | YES | | Untuk custom one-time |
| `effective_from` | DATE | YES | | Mulai |
| `effective_until` | DATE | YES | | Berakhir |
| `reason` | TEXT | YES | | Alasan |
| `created_by` | BIGINT UNSIGNED | YES | FK | Creator |
| `updated_by` | BIGINT UNSIGNED | YES | FK | Updater |
| `created_at` | TIMESTAMP | YES | | |
| `updated_at` | TIMESTAMP | YES | | |

---

# 32. Due Date Resolution

Priority:

```text
Student Override
↓
Payment Type Configuration
↓
System Default
```

---

# 33. SPP Due Date

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Prototype:

```text
SPP
↓
Due day = 1
```

Requirement tetap wajib diimplementasikan.

Namun hardcoded value sebaiknya dihindari.

---

# 34. `bills`

## Purpose

Menyimpan kewajiban pembayaran santri.

Bill merupakan **tagihan**, bukan transaksi pembayaran.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `bill_number` | VARCHAR(100) | NO | UNIQUE | Nomor tagihan |
| `student_id` | BIGINT UNSIGNED | NO | FK, INDEX | Santri |
| `parent_id` | BIGINT UNSIGNED | NO | FK, INDEX | Parent saat bill dibuat |
| `payment_type_id` | BIGINT UNSIGNED | NO | FK, INDEX | Jenis pembayaran |
| `academic_year_id` | BIGINT UNSIGNED | NO | FK, INDEX | Tahun ajaran |
| `billing_period` | VARCHAR(20) | YES | INDEX | Periode |
| `billing_date` | DATE | NO | | Tanggal pembuatan |
| `due_date` | DATE | NO | INDEX | Jatuh tempo |
| `amount` | BIGINT UNSIGNED | NO | | Total tagihan |
| `paid_amount` | BIGINT UNSIGNED | NO | | Total dibayar |
| `outstanding_amount` | BIGINT UNSIGNED | NO | | Sisa |
| `status` | VARCHAR/ENUM | NO | INDEX | Status |
| `created_at` | TIMESTAMP | YES | | |
| `updated_at` | TIMESTAMP | YES | | |

---

# 35. Bill Status

Status utama:

```text
unpaid
paid
overdue
```

UI mapping:

| Database Status | UI |
|---|---|
| `unpaid` | 🟡 Belum Bayar |
| `paid` | 🟢 Lunas |
| `overdue` | 🔴 Terlambat |

---

# 36. Bill Status Calculation

Konsep:

```text
paid_amount = SUM(successful payments)
```

Kemudian:

```text
outstanding_amount = amount - paid_amount
```

Status:

```text
outstanding <= 0
→ paid

outstanding > 0
AND today > due_date
→ overdue

outstanding > 0
AND today <= due_date
→ unpaid
```

---

# 37. Bill Amount Snapshot

`bills.amount` merupakan snapshot nominal pada saat tagihan dibuat.

Jika harga master berubah:

> Bill lama tidak ikut berubah.

---

# 38. Billing Period

Untuk SPP:

```text
2026-08
```

atau representation setara.

Untuk one-time payment:

```text
NULL
```

atau period label sesuai kebutuhan.

---

# 39. Bill Uniqueness

Untuk monthly recurring payment:

Business identity minimal:

```text
student_id
payment_type_id
academic_year_id
billing_period
```

kombinasi tersebut harus mencegah duplicate bill untuk periode yang sama.

Unique constraint harus disesuaikan dengan billing type.

---

# 40. `bill_items`

## Purpose

Disiapkan untuk mendukung kemungkinan satu invoice/tagihan memiliki beberapa komponen.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `bill_id` | BIGINT UNSIGNED | NO | FK | Bill |
| `description` | VARCHAR(255) | NO | | Item |
| `quantity` | INT UNSIGNED | NO | | Quantity |
| `unit_price` | BIGINT UNSIGNED | NO | | Harga |
| `subtotal` | BIGINT UNSIGNED | NO | | Subtotal |
| `created_at` | TIMESTAMP | YES | | |
| `updated_at` | TIMESTAMP | YES | | |

---

# 41. Bill vs Bill Item

Untuk SPP sederhana:

```text
Bill
Rp500.000

Bill Item
SPP Agustus
1 × Rp500.000
```

Jika satu bill nanti memiliki beberapa item:

```text
Bill
Rp1.000.000

├── SPP
│   Rp500.000
│
└── Uang Makan
    Rp500.000
```

---

# 42. `payments`

## Purpose

Menyimpan setiap transaksi pembayaran.

Satu bill dapat memiliki banyak payment.

---

## Columns

| Column | Type | Null | Key | Description |
|---|---|---:|---|---|
| `id` | BIGINT UNSIGNED | NO | PK | ID |
| `payment_number` | VARCHAR(100) | NO | UNIQUE | Reference internal |
| `bill_id` | BIGINT UNSIGNED | NO | FK, INDEX | Tagihan |
| `student_id` | BIGINT UNSIGNED | NO | FK, INDEX | Snapshot/context santri |
| `parent_id` | BIGINT UNSIGNED | NO | FK, INDEX | Parent |
| `amount` | BIGINT UNSIGNED | NO | | Nominal |
| `method` | VARCHAR/ENUM | NO | INDEX | Metode |
| `source` | VARCHAR/ENUM | NO | INDEX | MIDTRANS/MANUAL |
| `status` | VARCHAR/ENUM | NO | INDEX | Status |
| `transaction_reference` | VARCHAR(255) | YES | UNIQUE | External/internal reference |
| `paid_at` | TIMESTAMP | YES | INDEX | Waktu pembayaran |
| `recorded_by` | BIGINT UNSIGNED | YES | FK | Operator |
| `notes` | TEXT | YES | | Catatan |
| `created_at` | TIMESTAMP | YES | | |
| `updated_at` | TIMESTAMP | YES | | |

---

# 43. Payment Source

Minimal:

```text
midtrans
manual
```

---

# 44. Payment Method

Contoh:

```text
midtrans
cash
bank_transfer
other_manual
```

Exact payment method dapat diperluas.

---

# 45. Payment Status

Minimal:

```text
pending
paid
failed
```

Status payment berbeda dengan status bill.

---

# 46. Payment Relationship

```text
Bill 1 ───── N Payments
```

Contoh:

```text
Bill Rp1.000.000
│
├── Payment #1 Rp400.000
├── Payment #2 Rp300.000
└── Payment #3 Rp300.000
```

---

# 47. Payment Immutability

Payment successful tidak boleh diubah nominalnya secara sembarangan.

Jika ada correction requirement di masa depan:

> gunakan adjustment/reversal flow.

Prototype belum memerlukan reversal module.

---

# 48. Midtrans Reference

Payment Midtrans harus menyimpan reference yang diperlukan untuk:

- webhook matching;
- idempotency;
- troubleshooting;
- reconciliation.

Contoh field tambahan yang dapat diperlukan:

```text
midtrans_order_id
transaction_id
transaction_status
fraud_status
settlement_time
```

Detail final mengikuti data yang benar-benar dibutuhkan oleh integration implementation.

---

# 49. `payment_gateway_transactions`

## Recommended

Untuk memisahkan metadata Midtrans dari core payment record, dapat digunakan table:

`payment_gateway_transactions`

---

## Columns

| Column | Type | Null | Key |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | NO | PK |
| `payment_id` | BIGINT UNSIGNED | NO | FK |
| `provider` | VARCHAR(50) | NO | INDEX |
| `order_id` | VARCHAR(255) | NO | UNIQUE |
| `transaction_id` | VARCHAR(255) | YES | UNIQUE |
| `transaction_status` | VARCHAR(100) | YES | INDEX |
| `fraud_status` | VARCHAR(100) | YES | |
| `raw_status` | JSON | YES | |
| `response_payload` | JSON | YES | |
| `created_at` | TIMESTAMP | YES | |
| `updated_at` | TIMESTAMP | YES | |

---

# 50. Why Separate Gateway Data

Core `payments` sebaiknya tidak dipenuhi field khusus Midtrans.

Dengan separation:

```text
Payment
   ↓
Gateway Transaction
   ↓
Midtrans
```

Future gateway dapat ditambahkan tanpa merombak payment core.

---

# 51. Webhook Idempotency

Unique identifier dari Midtrans harus digunakan untuk mencegah duplicate payment.

Minimal candidate:

```text
provider
+
order_id
+
transaction_id
```

Tidak boleh membuat payment baru jika webhook yang sama diterima ulang.

---

# 52. `payment_evidences`

## Purpose

Menyimpan bukti pembayaran manual.

---

## Columns

| Column | Type | Null | Key |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | NO | PK |
| `payment_id` | BIGINT UNSIGNED | NO | FK |
| `file_name` | VARCHAR(255) | NO | |
| `file_path` | VARCHAR(500) | NO | |
| `mime_type` | VARCHAR(100) | NO | |
| `file_size` | BIGINT UNSIGNED | YES | |
| `uploaded_by` | BIGINT UNSIGNED | NO | FK |
| `created_at` | TIMESTAMP | YES | |
| `updated_at` | TIMESTAMP | YES | |

---

# 53. Evidence Rules

Evidence hanya dapat diakses oleh:

- Admin;
- Super Admin;
- Parent yang memiliki payment tersebut.

File tidak boleh menjadi public unrestricted file.

---

# 54. `invoices`

## Purpose

Menyimpan metadata invoice.

---

## Columns

| Column | Type | Null | Key |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | NO | PK |
| `bill_id` | BIGINT UNSIGNED | NO | FK, UNIQUE |
| `invoice_number` | VARCHAR(100) | NO | UNIQUE |
| `file_path` | VARCHAR(500) | NO | |
| `generated_at` | TIMESTAMP | NO | |
| `created_at` | TIMESTAMP | YES | |
| `updated_at` | TIMESTAMP | YES | |

---

# 55. Invoice Relationship

Prototype:

```text
Bill 1 ───── 1 Invoice
```

Invoice merupakan dokumen dari bill.

Jika email dikirim ulang:

> gunakan invoice yang sama.

---

# 56. Invoice Generation

Invoice dibuat:

- saat bill tersedia; atau
- sebelum email reminder H-2 jika belum ada.

Namun idealnya:

```text
Bill Created
↓
Invoice Generated
```

agar dokumen sudah tersedia sejak awal.

---

# 57. `receipts`

## Purpose

Menyimpan metadata bukti pembayaran.

---

## Columns

| Column | Type | Null | Key |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | NO | PK |
| `payment_id` | BIGINT UNSIGNED | NO | FK, UNIQUE |
| `receipt_number` | VARCHAR(100) | NO | UNIQUE |
| `file_path` | VARCHAR(500) | NO | |
| `generated_at` | TIMESTAMP | NO | |
| `created_at` | TIMESTAMP | YES | |
| `updated_at` | TIMESTAMP | YES | |

---

# 58. Receipt Relationship

```text
Payment 1 ───── 1 Receipt
```

Jika terdapat tiga cicilan:

```text
Payment #1 → Receipt #1
Payment #2 → Receipt #2
Payment #3 → Receipt #3
```

---

# 59. Document Access

Parent dapat mengakses:

```text
Parent
 ↓
Student
 ↓
Bill
 ↓
Invoice
```

dan:

```text
Parent
 ↓
Student
 ↓
Bill
 ↓
Payment
 ↓
Receipt
```

Authorization harus dilakukan sebelum file disediakan.

---

# 60. `email_logs`

## Purpose

Melacak pengiriman email otomatis.

---

## Columns

| Column | Type | Null | Key |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | NO | PK |
| `recipient_user_id` | BIGINT UNSIGNED | YES | FK |
| `recipient_email` | VARCHAR(255) | NO | INDEX |
| `type` | VARCHAR(100) | NO | INDEX |
| `related_type` | VARCHAR(100) | YES | INDEX |
| `related_id` | BIGINT UNSIGNED | YES | INDEX |
| `document_type` | VARCHAR(100) | YES | |
| `document_id` | BIGINT UNSIGNED | YES | |
| `status` | VARCHAR(50) | NO | INDEX |
| `attempts` | UNSIGNED INT | NO | |
| `sent_at` | TIMESTAMP | YES | |
| `failed_at` | TIMESTAMP | YES | |
| `failure_reason` | TEXT | YES | |
| `created_at` | TIMESTAMP | YES | |
| `updated_at` | TIMESTAMP | YES | |

---

# 61. Email Types

Minimal:

```text
bill_available
due_reminder
payment_receipt
bill_overdue
```

Tidak ada:

```text
payment_failed
```

untuk email parent pada prototype.

---

# 62. Email Status

```text
pending
sent
failed
```

---

# 63. Email Retry

Jika:

```text
status = failed
```

Admin/Super Admin dapat melakukan:

> Resend

Resend harus menggunakan related document/entity yang sama.

---

# 64. Email Failure Notification

Jika email gagal:

```text
Email Log
↓
status = failed
↓
Internal Notification
```

Tidak:

```text
Email failed
↓
Send failure email to parent
```

---

# 65. `notifications`

Jika menggunakan Laravel database notifications, schema notification dapat mengikuti Laravel notification infrastructure.

Tujuan:

- notifikasi internal;
- unread/read state;
- recipient;
- payload.

Target:

```text
Admin
Super Admin
```

---

# 66. Internal Notification Example

```json
{
  "type": "email_failed",
  "title": "Email gagal dikirim",
  "message": "Invoice INV-001 gagal dikirim.",
  "related_type": "invoice",
  "related_id": 1
}
```

Payload final mengikuti implementation.

---

# 67. `audit_logs`

## Purpose

Menyimpan aktivitas administratif yang perlu diaudit.

---

## Columns

| Column | Type | Null | Key |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | NO | PK |
| `user_id` | BIGINT UNSIGNED | YES | FK, INDEX |
| `action` | VARCHAR(100) | NO | INDEX |
| `auditable_type` | VARCHAR(100) | YES | INDEX |
| `auditable_id` | BIGINT UNSIGNED | YES | INDEX |
| `old_values` | JSON | YES | |
| `new_values` | JSON | YES | |
| `ip_address` | VARCHAR(45) | YES | |
| `user_agent` | TEXT | YES | |
| `created_at` | TIMESTAMP | YES | INDEX |

---

# 68. Audit Examples

```text
user = Admin Joko
action = create
auditable_type = Payment
auditable_id = 100
```

atau:

```text
user = Super Admin
action = update
auditable_type = PaymentTypePrice
auditable_id = 15

old:
500000

new:
550000
```

---

# 69. Audit Access

Database record dapat menyimpan aktivitas seluruh internal user.

UI access:

```text
Super Admin → allowed
Admin → denied
Parent → denied
```

---

# 70. Audit Immutable Principle

Audit log tidak boleh diedit oleh user biasa.

Audit log sebaiknya append-only.

---

# 71. Financial Data Relationships

Core:

```text
Parent
   ↓
Student
   ↓
Bill
   ↓
Payment
```

Master:

```text
Academic Year
Payment Type
Pricing
```

Documents:

```text
Bill
 ↓
Invoice

Payment
 ↓
Receipt
```

---

# 72. Complete ERD Concept

```text
┌──────────────────┐
│      users       │
├──────────────────┤
│ id               │
│ name             │
│ email            │
│ password         │
│ role             │
└───────┬──────────┘
        │
        │ 1:1
        ↓
┌──────────────────┐
│     parents      │
├──────────────────┤
│ id               │
│ user_id          │
│ full_name        │
│ phone            │
│ email            │
└───────┬──────────┘
        │
        │ 1:N
        ↓
┌──────────────────┐
│     students     │
├──────────────────┤
│ id               │
│ parent_id        │
│ nis              │
│ full_name        │
│ class_level      │
│ rombel           │
│ status           │
└───────┬──────────┘
        │
        ├───────────────────────┐
        │                       │
        │                       ↓
        │             ┌────────────────────┐
        │             │ student_academic   │
        │             │      _years        │
        │             └─────────┬──────────┘
        │                       │
        ↓                       ↓
┌──────────────────┐   ┌──────────────────┐
│      bills       │   │ academic_years   │
├──────────────────┤   ├──────────────────┤
│ id               │   │ id               │
│ student_id       │   │ name             │
│ parent_id        │   │ start_date       │
│ payment_type_id  │   │ end_date         │
│ academic_year_id │   │ is_active        │
│ amount           │   └──────────────────┘
│ paid_amount      │
│ outstanding      │
│ due_date         │
│ status           │
└───────┬──────────┘
        │
        │ 1:N
        ↓
┌──────────────────┐
│     payments     │
├──────────────────┤
│ id               │
│ bill_id          │
│ amount           │
│ source           │
│ method           │
│ status           │
│ paid_at          │
└───────┬──────────┘
        │
        ├───────────────┐
        ↓               ↓
┌───────────────┐ ┌───────────────────┐
│   receipts    │ │ payment_evidences │
└───────────────┘ └───────────────────┘


┌──────────────────────┐
│    payment_types     │
├──────────────────────┤
│ id                   │
│ code                 │
│ name                 │
│ billing_type         │
│ allows_installment   │
└──────────┬───────────┘
           │
           ├───────────────┐
           ↓               ↓
┌────────────────────┐ ┌────────────────────────┐
│ payment_type_prices│ │ student_payment_       │
│                    │ │ overrides              │
└────────────────────┘ └────────────────────────┘
```

---

# 73. Relationship Cardinality

| Parent | Child | Cardinality |
|---|---|---|
| User | Parent | 1:1 |
| Parent | Student | 1:N |
| Student | Bill | 1:N |
| Payment Type | Bill | 1:N |
| Academic Year | Bill | 1:N |
| Bill | Payment | 1:N |
| Bill | Invoice | 1:1 |
| Payment | Receipt | 1:1 |
| Payment | Evidence | 1:N |
| Payment Type | Price | 1:N |
| Student | Price Override | 1:N |
| Student | Academic Placement | 1:N |
| User | Audit Log | 1:N |
| User | Email Log | 1:N |

---

# 74. Foreign Key Strategy

Foreign keys wajib digunakan pada relationship utama.

Contoh:

```text
parents.user_id → users.id
students.parent_id → parents.id
bills.student_id → students.id
bills.payment_type_id → payment_types.id
bills.academic_year_id → academic_years.id
payments.bill_id → bills.id
```

---

# 75. Delete Strategy

## User

Jangan cascade delete financial history secara sembarangan.

## Parent

Jangan cascade delete students dan financial records secara otomatis.

## Student

Jangan cascade delete bills/payments.

## Payment Type

Jangan hard delete jika sudah digunakan dalam historical bill.

Gunakan:

```text
is_active = false
```

atau soft delete.

## Bill

Financial record tidak boleh dihapus sembarangan.

## Payment

Successful payment tidak boleh cascade delete.

---

# 76. Soft Delete

Soft delete dapat digunakan pada master entity:

```text
students
parents
payment_types
```

Financial transactions sebaiknya tidak mengandalkan soft delete sebagai cara normal untuk "menghapus" transaksi.

---

# 77. Money Storage

Semua nominal uang disimpan sebagai:

```text
BIGINT UNSIGNED
```

Contoh:

```text
Rp500.000
```

disimpan:

```text
500000
```

Jangan menggunakan FLOAT untuk nominal uang.

---

# 78. Date & Time

Tanggal bisnis menggunakan:

```text
DATE
```

Contoh:

- due date;
- billing period.

Timestamp menggunakan:

```text
TIMESTAMP
```

untuk:

- created_at;
- updated_at;
- paid_at;
- generated_at;
- sent_at.

Timezone application harus konsisten.

---

# 79. Index Strategy

Minimal index pada:

### Users

```text
email UNIQUE
role
```

### Students

```text
parent_id
nis UNIQUE
full_name
class_level
rombel
status
```

### Bills

```text
student_id
parent_id
payment_type_id
academic_year_id
due_date
status
billing_period
```

### Payments

```text
bill_id
student_id
parent_id
status
source
method
paid_at
```

### Audit

```text
user_id
action
auditable_type
auditable_id
created_at
```

---

# 80. Composite Index Candidates

Untuk query reporting:

```text
bills(
    academic_year_id,
    payment_type_id,
    status
)
```

```text
bills(
    student_id,
    due_date,
    status
)
```

```text
payments(
    bill_id,
    status
)
```

```text
payments(
    paid_at,
    status
)
```

Index final harus divalidasi berdasarkan query nyata.

Jangan menambahkan puluhan index tanpa kebutuhan.

---

# 81. Unique Constraints

Minimal:

```text
users.email
students.nis
payment_types.code
academic_years.name
bills.bill_number
payments.payment_number
invoices.invoice_number
receipts.receipt_number
```

Gateway reference yang digunakan untuk idempotency juga harus memiliki uniqueness yang sesuai.

---

# 82. Business Unique Constraints

Untuk recurring bill:

```text
student_id
payment_type_id
academic_year_id
billing_period
```

harus unik jika billing type monthly.

Untuk student pricing:

```text
student_id
payment_type_id
academic_year_id
```

harus mencegah duplicate active override untuk periode yang sama.

---

# 83. Data Snapshot Principle

Financial transaction harus menyimpan informasi yang diperlukan pada saat transaksi.

Contoh:

Bill dibuat saat:

```text
SPP = Rp500.000
```

Kemudian master price berubah:

```text
SPP = Rp600.000
```

Bill lama:

```text
Rp500.000
```

tetap Rp500.000.

---

# 84. Parent Snapshot

Bill menyimpan `parent_id` pada saat bill dibuat.

Tujuan:

- histori;
- reporting;
- traceability.

Namun source of truth hubungan parent/student tetap berada pada student relationship.

---

# 85. Payment Snapshot

Payment menyimpan reference ke:

- bill;
- student;
- parent.

Ini membantu historical reporting dan mengurangi ambiguity ketika data master berubah.

---

# 86. Payment Calculation

Tidak menyimpan:

```text
remaining_balance
```

sebagai satu-satunya source of truth.

Recommended:

```text
Bill Amount
+
Successful Payments
=
Calculated Financial State
```

`paid_amount` dan `outstanding_amount` boleh disimpan sebagai cached/derived fields jika diperlukan untuk performance, tetapi harus selalu konsisten dengan payment records.

---

# 87. Bill State Integrity

Invariant:

```text
amount >= 0
paid_amount >= 0
outstanding_amount >= 0
```

Untuk prototype:

```text
paid_amount <= amount
```

karena overpayment belum menjadi requirement.

---

# 88. Installment Data Integrity

Contoh:

```text
Bill = 1.000.000

Payment 1 = 400.000
Payment 2 = 300.000
Payment 3 = 300.000
```

Database harus menghasilkan:

```text
SUM(payment.amount) = 1.000.000
outstanding = 0
status = paid
```

---

# 89. Manual Payment Audit

Manual payment wajib menyimpan:

```text
recorded_by
created_at
amount
method
notes
evidence
```

`recorded_by` mengarah ke:

```text users.id
```

dan hanya user internal yang authorized.

---

# 90. Invoice Document Integrity

Invoice harus mengacu ke satu bill.

Unique:

```text
bill_id
```

Artinya:

```text
1 Bill = 1 canonical Invoice
```

Resend tidak membuat invoice baru.

---

# 91. Receipt Document Integrity

Receipt harus mengacu ke satu payment.

Unique:

```text
payment_id
```

Artinya:

```text
1 successful Payment = 1 canonical Receipt
```

---

# 92. Email Document Relationship

Email log dapat mengacu pada:

```text
Invoice
Receipt
Bill
```

contoh:

```text
type = due_reminder
document_type = invoice
document_id = 123
```

---

# 93. Notification Relationship

Internal notification dapat mengacu pada:

```text
EmailLog
Invoice
Payment
Bill
```

agar Admin dapat membuka entity yang bermasalah.

---

# 94. Audit Relationship

Audit log menggunakan polymorphic reference:

```text
auditable_type
auditable_id
```

sehingga dapat mencatat:

```text
Student
Payment
Bill
PaymentType
Price
AcademicYear
```

tanpa membuat tabel audit terpisah untuk setiap entity.

---

# 95. Reporting Data Sources

### Payment Report

Source:

```text
payments
+
bills
+
students
+
parents
+
payment_types
```

### Revenue Report

Source utama:

```text
successful payments
```

### Outstanding Report

Source utama:

```text
bills
```

dengan:

```text
outstanding_amount > 0
```

---

# 96. Revenue Calculation

Revenue hanya menghitung pembayaran yang benar-benar berhasil.

Tidak menghitung:

```text
pending
failed
```

Contoh:

```text
Payment A = 500K paid
Payment B = 300K pending
Payment C = 200K failed
```

Revenue:

```text
500K
```

---

# 97. Outstanding Calculation

Outstanding:

```text
SUM(bill.amount)
-
SUM(successful payment)
```

atau equivalent menggunakan `outstanding_amount` yang terjaga konsistensinya.

---

# 98. Overdue Calculation

Bill overdue jika:

```text
outstanding_amount > 0
AND current_date > due_date
```

Status tidak hanya ditentukan dari:

```text status = overdue
```

tanpa mempertimbangkan tanggal dan outstanding.

---

# 99. Academic Year Reporting

Report dapat difilter berdasarkan:

```text
academic_year_id
```

Historical report harus tetap dapat membaca tahun ajaran lama.

---

# 100. Data Retention

Financial data harus dipertahankan selama sistem masih membutuhkan histori.

Tidak ada automatic deletion hanya karena:

- tahun ajaran baru;
- student graduated;
- student withdrawn.

---

# 101. Migration Strategy

Migration dari existing schema ke target schema harus dilakukan secara bertahap.

Tidak disarankan:

```text
migrate:fresh
```

terhadap production data.

Untuk development prototype, `migrate:fresh` dapat digunakan jika database memang disposable.

---

# 102. Existing → Target Mapping

Konseptual:

| Existing | Target |
|---|---|
| `users` | `users` |
| `products` | `payment_types` / pricing concept |
| `orders` | `bills` |
| `order_details` | `bill_items` |
| `payments` | `payments` + gateway metadata |

Mapping final harus diverifikasi terhadap migration/model actual sebelum migration dibuat.

README hanya membuktikan keberadaan model dan alur existing, bukan seluruh detail implementasi migration.

---

# 103. Existing `products`

Existing project menggunakan `products` sebagai:

> Layanan / Tagihan

dan memiliki field seperti konsep:

- name;
- price;
- stock;
- description.

Untuk domain pembayaran pondok, konsep `stock` tidak sesuai dengan kebutuhan pembayaran.

Karena itu `products` kemungkinan besar perlu direfactor menjadi konsep:

> `payment_types`

Namun:

> Jangan menghapus `products` sebelum memastikan tidak ada dependency existing yang masih digunakan.

---

# 104. Existing `orders`

Existing `orders` merupakan konsep tagihan utama dengan:

- user;
- invoice number;
- gross amount;
- status.

Target domain membutuhkan:

```text
bill
student
parent
payment_type
academic_year
billing_period
due_date
```

Maka `orders` dapat direfactor menjadi `bills` apabila analysis codebase membuktikan mapping tersebut aman.

---

# 105. Existing `order_details`

Existing `order_details` merupakan rincian item order.

Konsep tersebut masih dapat berguna dan dapat dipetakan ke:

> `bill_items`

Namun tidak boleh langsung dihapus sebelum dependency diperiksa.

---

# 106. Existing `payments`

Existing `payments` sudah berhubungan dengan Midtrans dan menyimpan metadata seperti:

- order;
- amount;
- status;
- snap token;
- payment date.

README menjelaskan payment flow menggunakan Midtrans Snap dan webhook `/payment/callback`.

Target schema tetap mempertahankan konsep payment, tetapi membutuhkan:

- bill relationship;
- payment source;
- manual payment;
- installment;
- receipt;
- audit;
- gateway transaction metadata.

---

# 107. Schema Migration Rule

AI/developer wajib melakukan:

```text
Inspect existing migration
↓
Inspect existing model
↓
Inspect controller
↓
Inspect Filament Resource/Page
↓
Inspect seeders
↓
Map dependencies
↓
Only then modify schema
```

Jangan membuat schema target berdasarkan README saja jika source code actual tersedia.

---

# 108. Temporary Schema Decisions

| Area | Decision | Status |
|---|---|---|
| Class | 1–9 | ⚠️ Temporary |
| Rombel | A/B | ⚠️ Temporary |
| Academic Year | Digunakan | ⚠️ Temporary |
| Promotion | Automatic | ⚠️ Temporary |
| SPP billing | Monthly/date 1 | ⚠️ Temporary |
| SPP due date | Date 1 | ⚠️ Temporary |
| Other due dates | Configurable | ⚠️ Temporary |
| Installment | Technically supported | ⚠️ Temporary |
| All roles Filament | Yes | ⚠️ Temporary |
| Manual payment | Required | ✅ Confirmed |
| Invoice | Required | ✅ Confirmed |
| Receipt | Required | ✅ Confirmed |
| Audit | Required | ✅ Confirmed |

---

# 109. Schema Rules for Temporary Requirements

Temporary requirement **harus tetap memiliki schema support**.

Contoh:

SPP sementara jatuh tempo tanggal 1.

Jangan membuat:

```text
bills.due_date
hardcoded = 1
```

Sebagai gantinya:

```text
payment configuration
↓
effective due date
↓
bill.due_date
```

Dengan demikian ketika client mengatakan:

> "Ternyata SPP jatuh tempo tanggal 10."

yang berubah adalah configuration/business rule, bukan seluruh database.

---

# 110. Open Schema Decisions

Beberapa hal belum final:

1. Apakah parent relationship akan tetap one-to-many sederhana atau menjadi many-to-many.
2. Detail final class 9 setelah tahun ajaran baru.
3. Apakah academic placement perlu table penuh sejak prototype atau cukup snapshot.
4. Detail cicilan masing-masing payment type.
5. Apakah one-time payment membutuhkan billing period.
6. Apakah payment gateway transaction perlu tabel terpisah atau metadata JSON.
7. Struktur final email log.
8. Detail storage provider.
9. Detail correction/reversal payment di masa depan.

Jika belum dibutuhkan, jangan menambahkan kompleksitas tanpa alasan.

---

# 111. Recommended Minimum Target Tables

Untuk prototype, target minimal yang direkomendasikan:

```text
users
parents
students
academic_years
student_academic_years

payment_types
payment_type_prices
student_payment_overrides
payment_due_date_overrides

bills
bill_items
payments
payment_gateway_transactions
payment_evidences

invoices
receipts

email_logs
notifications
audit_logs
```

Total:

> **17 logical tables/concepts**

Beberapa dapat menggunakan infrastructure Laravel existing.

---

# 112. Optional Simplification

Jika saat implementasi ditemukan bahwa beberapa table tidak memberikan manfaat nyata untuk prototype, table tersebut dapat disederhanakan.

Namun simplification tidak boleh menghilangkan requirement.

Contoh:

Jika `payment_gateway_transactions` tidak diperlukan sebagai table terpisah, metadata Midtrans dapat ditempatkan secara aman pada payment dengan alasan yang terdokumentasi.

Sebaliknya, jangan menghapus:

```text
payment
bill
invoice
receipt
audit
```

karena semuanya memiliki fungsi domain yang berbeda.

---

# 113. Core Invariants

Database/application harus menjaga:

### User

```text
email unique
```

### Student

```text
nis unique
```

### Payment Type

```text
code unique
```

### Academic Year

```text
name unique
one active year
```

### Bill

```text
one recurring bill per student/payment_type/period
```

### Payment

```text
payment reference unique
```

### Invoice

```text
one canonical invoice per bill
```

### Receipt

```text
one canonical receipt per payment
```

---

# 114. Final ERD Relationship Summary

```text
USER
│
├── 1:1 PARENT
│
└── 1:N AUDIT_LOG


PARENT
│
└── 1:N STUDENT
          │
          ├── 1:N STUDENT_ACADEMIC_YEAR
          │
          ├── 1:N STUDENT_PAYMENT_OVERRIDE
          │
          ├── 1:N PAYMENT_DUE_DATE_OVERRIDE
          │
          └── 1:N BILL
                    │
                    ├── 1:N BILL_ITEM
                    │
                    ├── 1:1 INVOICE
                    │
                    └── 1:N PAYMENT
                              │
                              ├── 1:1 RECEIPT
                              ├── 1:N PAYMENT_EVIDENCE
                              └── 1:1 PAYMENT_GATEWAY_TRANSACTION


ACADEMIC_YEAR
│
├── 1:N STUDENT_ACADEMIC_YEAR
├── 1:N BILL
├── 1:N PAYMENT_TYPE_PRICE
└── 1:N STUDENT_PAYMENT_OVERRIDE


PAYMENT_TYPE
│
├── 1:N PAYMENT_TYPE_PRICE
├── 1:N STUDENT_PAYMENT_OVERRIDE
├── 1:N PAYMENT_DUE_DATE_OVERRIDE
└── 1:N BILL
```

---

# 115. Final Schema Principle

Database ini harus mengikuti prinsip:

> **Master data dapat berubah. Financial history tidak boleh ikut berubah secara retroaktif.**

Contoh:

```text
Payment Type
SPP = Rp500.000
        ↓
Bill August
Rp500.000
        ↓
Payment
Rp500.000
```

Kemudian Super Admin mengubah:

```text
SPP = Rp600.000
```

Maka:

```text
August Bill = Rp500.000
September Bill = Rp600.000
```

Bukan:

```text
August Bill = Rp600.000
```

---

# 116. Final Schema Objective

Schema harus memungkinkan sistem menjawab pertanyaan:

> Siapa santrinya?

```text
students
```

> Siapa orang tuanya?

```text
parents
```

> Apa yang harus dibayar?

```text
payment_types
bills
```

> Berapa nominalnya?

```text
payment_type_prices
student_payment_overrides
bills.amount
```

> Kapan jatuh tempo?

```text
payment_due_date_overrides
bills.due_date
```

> Sudah bayar berapa?

```text
payments
```

> Masih kurang berapa?

```text
bills.outstanding_amount
```

> Bayarnya lewat mana?

```text
payments.source
payments.method
```

> Bukti pembayarannya mana?

```text
receipts
payment_evidences
```

> Invoice-nya mana?

```text
invoices
```

> Email sudah terkirim?

```text
email_logs
```

> Kenapa email gagal?

```text
email_logs.failure_reason
```

> Siapa yang melakukan perubahan?

```text
audit_logs
```

> Tahun ajarannya apa?

```text
academic_years
```

> Kelas santri pada tahun tersebut apa?

```text
student_academic_years
```

Dengan struktur tersebut, database tidak hanya menjadi tempat menyimpan data, tetapi menjadi fondasi untuk:

> **administrasi pembayaran, monitoring, histori, reporting, dan audit pondok.**