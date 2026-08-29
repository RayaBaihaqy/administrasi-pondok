# ARCHITECTURE.md — Sistem Administrasi Pembayaran Pondok

> **System Architecture Document**
>
> Dokumentasi arsitektur teknis untuk Sistem Administrasi Pembayaran Pondok.

---

# 1. Document Information

| Item | Detail |
|---|---|
| Nama Sistem | Sistem Administrasi Pembayaran Pondok |
| Dokumen | Architecture |
| Versi | 1.0 — Prototype |
| Status | Draft Architecture |
| Backend | Laravel 12 |
| Admin/UI Framework | Filament 5 |
| Database | MySQL |
| Payment Gateway | Midtrans |
| Frontend/UI | Filament |
| Build Tool | Vite |
| Primary Language | PHP |
| Existing Project | Existing Laravel + Filament + Midtrans boilerplate |

---

# 2. Purpose

Dokumen ini mendefinisikan bagaimana sistem administrasi pembayaran pondok akan dibangun secara teknis.

PRD mendefinisikan:

> **Apa yang harus dibangun.**

Dokumen ini mendefinisikan:

> **Bagaimana sistem tersebut dibangun.**

Architecture harus memastikan sistem:

- mudah dikembangkan;
- mudah dipelihara;
- aman;
- dapat diaudit;
- mendukung automation;
- dapat mengakomodasi perubahan requirement;
- tidak bergantung secara berlebihan pada vendor;
- dapat mempertahankan pondasi existing;
- dapat berkembang dari prototype menjadi sistem production.

---

# 3. Architecture Principles

## 3.1 Preserve Existing Foundation

Project existing sudah menggunakan:

- Laravel 12;
- Filament 5;
- MySQL;
- Midtrans;
- Blade;
- Tailwind;
- Vite.

Pondasi tersebut harus dipertahankan selama masih memenuhi kebutuhan sistem.

Perubahan besar tidak boleh dilakukan hanya karena preferensi developer.

Perubahan major hanya boleh dilakukan apabila:

1. Ada alasan teknis yang jelas.
2. Ada requirement yang tidak dapat dipenuhi dengan architecture existing.
3. Ada masalah maintainability/security/performance yang signifikan.
4. Manfaat perubahan lebih besar daripada risiko migrasinya.

---

# 4. High-Level Architecture

Architecture utama menggunakan pendekatan:

> **Laravel Monolithic Web Application + Modular Domain Organization**

Tidak perlu menggunakan microservices untuk prototype.

Struktur konseptual:

```text
┌──────────────────────────────────────────────────────────────┐
│                         CLIENTS                              │
├──────────────────────┬───────────────────────┬───────────────┤
│ Super Admin          │ Admin                 │ Orang Tua     │
│ Kepala Bendahara     │ Operator Pondok       │ User          │
└──────────┬───────────┴──────────┬────────────┴──────┬────────┘
           │                      │                   │
           └──────────────────────┼───────────────────┘
                                  ↓
                    ┌────────────────────────┐
                    │      FILAMENT 5        │
                    │      Presentation      │
                    └────────────┬───────────┘
                                 ↓
                    ┌────────────────────────┐
                    │   Laravel Application  │
                    │                        │
                    │ Authorization          │
                    │ Business Logic         │
                    │ Services                │
                    │ Actions                 │
                    │ Events / Listeners      │
                    │ Notifications           │
                    │ Jobs                    │
                    │ Policies                │
                    └────────────┬───────────┘
                                 ↓
                    ┌────────────────────────┐
                    │       Eloquent         │
                    │        Models           │
                    └────────────┬───────────┘
                                 ↓
                    ┌────────────────────────┐
                    │         MySQL           │
                    └────────────────────────┘

External Services:

Laravel ───────────────→ Midtrans
Laravel ───────────────→ SMTP / Mail Provider
Midtrans ──────────────→ Laravel Webhook
```

---

# 5. Architecture Style

Sistem menggunakan:

> **Modular Monolith**

bukan microservices.

Semua modul berjalan dalam satu Laravel application dan menggunakan satu database utama.

Keuntungan untuk prototype:

- deployment sederhana;
- debugging lebih mudah;
- transaction database lebih sederhana;
- tidak membutuhkan service discovery;
- tidak membutuhkan message broker;
- tidak membutuhkan infrastructure kompleks;
- cocok untuk ukuran sistem pondok;
- tetap dapat dipisahkan secara modular pada level code.

---

# 6. Application Layers

Architecture aplikasi dibagi secara konseptual menjadi beberapa layer.

```text
Presentation
     ↓
Application
     ↓
Domain / Business Logic
     ↓
Infrastructure / Persistence
     ↓
Database
```

Implementasi Laravel tidak harus menggunakan Clean Architecture secara ekstrem.

Tujuan utama adalah **separation of concerns**, bukan menambah abstraksi yang tidak diperlukan.

---

# 7. Presentation Layer

Presentation layer bertanggung jawab terhadap:

- halaman;
- form;
- table;
- dashboard;
- navigation;
- filter;
- action;
- notification;
- user interaction.

Framework utama:

> **Filament 5**

Semua role prototype menggunakan Filament.

```text
Filament
├── Super Admin Panel
├── Admin Panel
└── User Panel
```

---

# 8. Filament Architecture

Filament digunakan sebagai UI/application presentation framework.

Konsep:

```text
Filament Panel
│
├── Resources
│   ├── StudentResource
│   ├── ParentResource
│   ├── PaymentTypeResource
│   ├── BillResource
│   └── PaymentResource
│
├── Pages
│   ├── Dashboard
│   ├── AcademicYearManagement
│   ├── Reports
│   └── AuditLog
│
├── Widgets
│   ├── StudentStats
│   ├── PaymentStats
│   ├── RevenueStats
│   └── OutstandingStats
│
└── Notifications
```

Nama class di atas merupakan rancangan konseptual.

Implementasi final harus disesuaikan dengan struktur project existing.

---

# 9. Filament Customization Strategy

Filament tidak boleh dianggap sebagai UI template yang harus diterima secara mentah.

UI dapat dikustomisasi.

Area customization:

- theme;
- colors;
- typography;
- navigation;
- sidebar;
- dashboard;
- widgets;
- forms;
- tables;
- badges;
- actions;
- modals;
- page layouts;
- login page;
- branding;
- responsive behavior.

Prinsip:

> Gunakan Filament sebagai foundation, customisasi elemen yang tidak sesuai, jangan mengganti seluruh framework tanpa alasan teknis.

---

# 10. User Panel Strategy

Semua role menggunakan Filament pada prototype.

Role:

```text
Super Admin → Filament
Admin       → Filament
User        → Filament
```

Perbedaan akses ditentukan oleh:

- authentication;
- authorization;
- policies;
- permissions;
- role;
- resource visibility;
- action visibility;
- query scoping.

---

# 11. Authentication Architecture

Laravel authentication menjadi foundation.

User account harus memiliki role.

Konseptual:

```text
User
├── Super Admin
├── Admin
└── User
```

Authentication harus menangani:

- login;
- logout;
- session;
- password hashing;
- remember session jika digunakan;
- reset password;
- password validation.

---

# 12. Authorization Architecture

Authentication menjawab:

> "Siapa kamu?"

Authorization menjawab:

> "Apa yang boleh kamu lakukan?"

Authorization harus dilakukan pada lebih dari satu layer.

```text
UI
 ↓
Policy / Authorization
 ↓
Service / Business Logic
 ↓
Database
```

Jangan hanya menyembunyikan tombol UI.

Contoh:

> Jika Admin tidak boleh mengubah nominal, menyembunyikan tombol edit saja tidak cukup.

Backend harus tetap menolak operasi tersebut.

---

# 13. Role Hierarchy

Role hierarchy:

```text
Super Admin
    │
    └── memiliki seluruh kemampuan Admin

Admin
    │
    └── memiliki kemampuan operasional

User
    │
    └── memiliki akses terbatas pada data dirinya dan anak
```

Super Admin tidak perlu memiliki implementasi business logic terpisah untuk setiap kemampuan Admin.

Super Admin harus dapat menggunakan operation yang sama dengan Admin apabila operation tersebut diizinkan.

---

# 14. Data Authorization

Data authorization untuk User sangat penting.

User hanya boleh mengakses:

```text
User
 ↓
Parent Profile
 ↓
Linked Students
 ↓
Their Bills
 ↓
Their Payments
 ↓
Their Documents
```

User tidak boleh mengakses:

```text
Student milik Parent lain
Bill milik Student lain
Payment milik Parent lain
```

Authorization harus dilakukan di query/resource level.

Jangan mengambil seluruh data lalu memfilter hanya di UI.

---

# 15. Domain Modules

Sistem secara konseptual dibagi menjadi domain/module:

```text
Authentication
User Management
Parent Management
Student Management
Academic Year
Payment Type
Billing
Payment
Midtrans Integration
Manual Payment
Invoice
Receipt
Email
Notification
Reporting
Audit
```

Modul tidak harus menjadi package terpisah.

Modularisasi dapat diwujudkan melalui:

- Models;
- Services;
- Actions;
- Policies;
- Jobs;
- Events;
- Listeners;
- Notifications;
- Filament Resources.

---

# 16. User Management Module

Tanggung jawab:

- account creation;
- role;
- authentication;
- password reset;
- parent account management.

Admin dan Super Admin dapat membuat akun User.

User tidak dapat melakukan self-registration pada prototype.

---

# 17. Parent Module

Tanggung jawab:

- data orang tua;
- akun orang tua;
- relasi dengan santri;
- email;
- nomor HP;
- alamat;
- akses anak.

Relationship:

```text
Parent
   │
   ├── Student
   ├── Student
   └── Student
```

Satu Parent dapat memiliki banyak Student.

---

# 18. Student Module

Tanggung jawab:

- data santri;
- NIS;
- nama;
- jenis kelamin;
- kelas;
- rombel;
- tahun masuk;
- status;
- email;
- parent relationship.

Student harus dapat berada dalam kondisi:

- Active;
- Graduated;
- Withdrawn;
- Inactive.

---

# 19. Class & Rombel Architecture

Prototype menggunakan:

```text
Class: 1–9

Rombel:
A
B
```

Conceptual grouping:

```text
Class
  ↓
Rombel
  ↓
Student
```

Kelompok tersebut digunakan antara lain untuk:

- menentukan nominal;
- filtering laporan;
- monitoring pembayaran;
- kenaikan kelas.

---

# 20. Academic Year Module

Academic Year menjadi konteks periodisasi.

Contoh:

```text
2026/2027
2027/2028
2028/2029
```

Satu periode harus dapat ditandai sebagai:

> Active Academic Year

---

# 21. Start New Academic Year

Aksi dilakukan oleh Super Admin.

```text
Current Academic Year
        ↓
Start New Academic Year
        ↓
Create New Academic Year
        ↓
Set New Year as Active
        ↓
Preserve Old Year
```

Tidak boleh:

```text
Start New Academic Year
↓
DELETE OLD DATA
```

---

# 22. Automatic Promotion

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Prototype menggunakan automatic promotion.

Saat tahun ajaran baru dimulai:

```text
Class 1 → Class 2
Class 2 → Class 3
...
Class 8 → Class 9
```

Rombel tetap:

```text
5-A → 6-A
5-B → 6-B
```

Kelas 9 membutuhkan penanganan khusus dan belum final.

Architecture harus memungkinkan rule promotion diubah kemudian.

Jangan menanamkan logic kelas 9 secara irreversible.

---

# 23. Payment Type Module

Payment Type merupakan master configuration.

Contoh:

```text
SPP
Uang Gedung
Uang Makan
Kitab
Seragam
```

Payment Type harus configurable.

Jangan hardcode:

```php
if ($paymentType === 'SPP') { ... }
```

untuk seluruh business logic.

Gunakan model/configuration/payment type attributes.

---

# 24. Payment Type Ownership

Super Admin adalah owner konfigurasi Payment Type.

Super Admin dapat:

- create;
- update;
- configure;
- set nominal;
- set due date;
- configure applicable group.

Admin tidak boleh melakukan operation yang restricted.

---

# 25. Pricing Architecture

Nominal payment tidak boleh hanya disimpan sebagai satu harga global apabila payment type membutuhkan variasi kelompok.

Konseptual:

```text
Payment Type
    │
    ├── Class/Rombel Pricing
    │       ├── 5-A → 500K
    │       ├── 5-B → 550K
    │       └── 6-A → 600K
    │
    └── Student Override
            └── Ahmad → 300K
```

---

# 26. Pricing Resolution

Saat sistem menentukan nominal tagihan, sistem harus menentukan effective price.

Konseptual priority:

```text
Student Override
       ↓
Class/Rombel Pricing
       ↓
Payment Type Default
       ↓
No Applicable Price
```

Jika Student memiliki override:

> gunakan override.

Jika tidak:

> gunakan harga kelompok.

Jika kelompok tidak memiliki harga dan Payment Type memiliki fallback default:

> gunakan default.

Jika tidak ada harga:

> jangan membuat tagihan secara diam-diam.

Sistem harus menghasilkan error/validation yang dapat ditindaklanjuti.

---

# 27. Due Date Architecture

Due date harus dapat ditentukan secara configurable.

Conceptual priority:

```text
Student Due Date Override
          ↓
Payment Type Due Date
          ↓
System Default
```

Effective due date digunakan oleh:

- billing;
- late detection;
- reminder;
- report;
- invoice.

---

# 28. Billing Module

Billing bertanggung jawab atas:

- pembuatan tagihan;
- periode;
- nominal;
- due date;
- status;
- payment progress;
- outstanding amount.

Billing tidak bertanggung jawab atas payment gateway.

Payment dan Billing harus dipisahkan.

---

# 29. Billing vs Payment

Jangan menyamakan:

> Tagihan

dengan:

> Pembayaran.

Contoh:

```text
Bill
Rp1.000.000
```

dapat memiliki:

```text
Payment #1
Rp400.000

Payment #2
Rp300.000

Payment #3
Rp300.000
```

Maka:

```text
Bill total = Rp1.000.000
Paid       = Rp1.000.000
Outstanding= Rp0
```

---

# 30. Installment Architecture

Cicilan harus direpresentasikan sebagai multiple payment transactions terhadap satu bill.

```text
Bill
 │
 ├── Payment #1
 ├── Payment #2
 └── Payment #3
```

Jangan membuat satu payment record yang terus di-overwrite.

Tujuan:

- audit;
- histori;
- rekonsiliasi;
- reporting;
- traceability.

---

# 31. Payment Status Calculation

Status bill tidak boleh hanya bergantung pada satu field payment.

Konsep:

```text
total_bill
-
sum(successful_payments)
=
outstanding_amount
```

Kemudian:

```text
if outstanding_amount <= 0
    status = PAID

else if current_date > due_date
    status = OVERDUE

else
    status = UNPAID
```

Nama enum/status final harus diselaraskan dengan `SCHEMA.md`.

---

# 32. Midtrans Integration

Midtrans merupakan external payment gateway.

Integration boundary:

```text
Laravel
   ↓
Midtrans API
   ↓
Customer Payment
   ↓
Midtrans
   ↓
Webhook
   ↓
Laravel
```

Laravel tidak boleh menganggap pembayaran berhasil hanya berdasarkan response frontend.

Status pembayaran harus diverifikasi menggunakan server-side notification/webhook.

---

# 33. Midtrans Payment Flow

```text
User
 ↓
Create Payment Intent / Transaction
 ↓
Laravel
 ↓
Midtrans
 ↓
Snap
 ↓
User Pays
 ↓
Midtrans Processes
 ↓
Midtrans Notification
 ↓
Laravel Webhook
 ↓
Verify Notification
 ↓
Update Payment
 ↓
Update Bill
 ↓
Generate Receipt
 ↓
Dispatch Receipt Email
```

---

# 34. Webhook Architecture

Endpoint webhook harus:

- menerima notification;
- memvalidasi request;
- memverifikasi signature/hash;
- menemukan transaction;
- memetakan status Midtrans;
- mengupdate payment;
- menjaga idempotency;
- tidak membuat duplicate payment;
- memicu downstream process hanya ketika status benar-benar berubah.

---

# 35. Webhook Idempotency

Webhook dapat dikirim lebih dari satu kali.

Sistem tidak boleh:

```text
Webhook #1
→ Payment Rp500K

Webhook #2
→ Payment Rp500K lagi

Webhook #3
→ Payment Rp500K lagi
```

menjadi tiga pembayaran.

Sistem harus dapat mengenali transaction/reference yang sama.

Konsep:

```text
Midtrans Transaction ID
+
Order/Payment Reference
```

digunakan untuk memastikan uniqueness/idempotency.

---

# 36. Payment State Transition

Contoh:

```text
Pending
   │
   ├── Success → Paid
   │
   ├── Failure → Failed
   │
   └── Expired/other → sesuai mapping
```

Mapping status Midtrans harus dipusatkan dalam integration layer.

Jangan menyebarkan string status Midtrans ke seluruh application.

---

# 37. Manual Payment Architecture

Manual payment melewati service/application layer yang sama dengan payment gateway sejauh memungkinkan.

Perbedaannya hanya pada payment source:

```text
Payment Source
├── MIDTRANS
└── MANUAL
```

Manual payment harus:

- mencatat operator;
- mencatat nominal;
- mencatat tanggal;
- mencatat metode;
- menyimpan note;
- mendukung evidence;
- masuk audit log.

---

# 38. Payment Reconciliation

Setiap payment harus memiliki referensi ke bill.

Conceptual:

```text
Bill
 ↓
Payment
 ↓
Payment Source
```

Payment source:

```text
MIDTRANS
MANUAL
```

Ini memungkinkan laporan:

> "Berapa pemasukan dari Midtrans?"

dan:

> "Berapa pemasukan manual?"

---

# 39. Invoice Architecture

Invoice adalah representation dari kewajiban pembayaran.

Invoice harus dibuat berdasarkan bill.

```text
Bill
 ↓
Invoice
 ↓
PDF
```

Invoice tidak boleh dibuat ulang tanpa alasan hanya karena email dikirim ulang.

Jika email gagal:

```text
Existing Invoice
↓
Resend
```

bukan:

```text
New Invoice
↓
Resend
```

---

# 40. Receipt Architecture

Receipt dibuat berdasarkan successful payment.

```text
Payment
 ↓
Receipt
 ↓
PDF
```

Jika satu bill memiliki tiga cicilan dan setiap cicilan berhasil:

```text
Payment #1 → Receipt #1
Payment #2 → Receipt #2
Payment #3 → Receipt #3
```

Dengan demikian setiap transaksi dapat memiliki bukti pembayaran yang dapat ditelusuri.

---

# 41. Document Storage

Dokumen harus memiliki lifecycle yang jelas.

Minimal:

```text
Invoice
Receipt
Manual Payment Evidence
```

Dokumen harus:

- memiliki reference;
- memiliki owner/context;
- dapat diakses melalui authorization;
- tidak dapat diakses oleh user lain;
- tetap tersedia untuk histori.

File path/storage detail akan ditentukan di implementation architecture.

---

# 42. Email Architecture

Email merupakan asynchronous operation.

Jangan membuat seluruh proses payment bergantung pada keberhasilan email.

Contoh:

```text
Payment Successful
↓
Payment committed
↓
Receipt generated
↓
Email Job dispatched
```

Jika email gagal:

```text
Payment tetap berhasil
Email = Failed
```

Jangan:

```text
Email gagal
↓
Rollback payment
```

---

# 43. Email Provider

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Provider final belum ditentukan.

Constraint:

- tidak menggunakan layanan berbayar untuk prototype;
- efektif;
- efisien;
- mudah dikonfigurasi Laravel;
- dapat diganti;
- tidak membuat application terlalu vendor-locked.

Architecture harus menggunakan Laravel Mail abstraction.

Application code tidak boleh langsung bergantung pada API provider tertentu di business logic.

Konfigurasi harus melalui `.env`.

Contoh konseptual:

```env
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```

Development dapat menggunakan mail testing/logging environment.

Provider production dapat diputuskan kemudian tanpa mengubah business logic email.

---

# 44. Email Event Architecture

Email trigger sebaiknya dipicu berdasarkan domain/application event.

Contoh:

```text
BillCreated
    ↓
SendNewBillNotification

BillApproachingDueDate
    ↓
SendDueReminder

PaymentSucceeded
    ↓
GenerateReceipt
    ↓
SendReceiptEmail

BillOverdue
    ↓
SendOverdueNotification
```

---

# 45. Email Queue

Email sebaiknya dikirim melalui queued jobs.

Tujuan:

- UI tidak menunggu SMTP;
- payment tidak bergantung pada email;
- retry lebih mudah;
- failure dapat dicatat;
- system lebih responsive.

Konseptual:

```text
Application
   ↓
Queue Job
   ↓
Mail Provider
```

---

# 46. Email Delivery Tracking

Sistem perlu menyimpan status email:

```text
PENDING
SENT
FAILED
```

Minimal informasi:

- recipient;
- email type;
- related entity;
- document reference;
- attempt;
- sent time;
- failure reason;
- status.

---

# 47. Email Retry / Resend

Resend harus menggunakan existing business document.

Contoh:

```text
Invoice #INV001
Email Failed

Admin
↓
Resend

Existing Invoice #INV001
↓
Email Job
```

Tidak membuat invoice kedua.

---

# 48. Notification Architecture

Internal notification berbeda dari email notification.

```text
Email Notification
→ Orang Tua

Internal Notification
→ Admin / Super Admin
```

Internal notification digunakan untuk event operasional seperti:

- email gagal;
- tindakan yang membutuhkan perhatian internal;
- event administratif yang nantinya ditentukan.

Notification harus tersedia melalui UI Filament.

---

# 49. Reporting Architecture

Reporting tidak boleh mengambil data secara manual dari UI.

Report harus memiliki service/application layer.

Konseptual:

```text
Report Request
↓
Report Service
↓
Query Builder / Read Model
↓
Filtered Dataset
↓
Exporter
├── Excel
└── PDF
```

---

# 50. Report Filters

Report service harus dapat menerima:

```text
Academic Year
Payment Type
Class
Rombel
Student
Parent
Payment Status
Payment Method
Start Date
End Date
```

Filter harus composable.

Contoh:

```text
Report
+
Academic Year = 2026/2027
+
Class = 5
+
Rombel = A
+
Payment Type = SPP
+
Date = August 2026
```

---

# 51. Report Types

Report layer menyediakan:

```text
PaymentReport
RevenueReport
OutstandingReport
```

Ketiganya dapat menggunakan shared filtering infrastructure.

---

# 52. Export Architecture

Exporters:

```text
Report Dataset
    │
    ├── Excel Exporter
    │
    └── PDF Exporter
```

Jangan membuat business query terpisah untuk setiap format jika dataset-nya sama.

Gunakan:

```text
Query
↓
Normalized Report Data
↓
Excel/PDF
```

---

# 53. Audit Architecture

Audit log merupakan cross-cutting concern.

Event:

```text
User Action
↓
Business Operation
↓
Audit Record
```

Audit harus menangkap minimal:

- actor;
- role;
- action;
- entity;
- entity ID;
- timestamp;
- relevant changes;
- context jika diperlukan.

---

# 54. Audit Scope

Aktivitas yang harus dipertimbangkan untuk audit:

- create student;
- update student;
- create parent;
- update parent;
- create payment type;
- update payment type;
- update price;
- update student-specific price;
- update due date;
- create manual payment;
- update payment-related administrative data;
- academic year transition;
- resend email;
- administrative changes lainnya yang berdampak pada finansial.

---

# 55. Audit Visibility

Audit record disimpan secara internal.

UI Audit Log hanya dapat diakses:

> Super Admin.

Admin tidak dapat membaca audit history.

---

# 56. Automation Architecture

Automation utama:

```text
Scheduled Tasks
│
├── Generate Monthly SPP
├── Detect H-2 Due Dates
├── Detect Overdue Bills
└── Other Future Billing Jobs
```

Laravel Scheduler/Cron digunakan sebagai orchestration mechanism.

---

# 57. Monthly SPP Automation

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Prototype:

```text
Every 1st of Month
↓
Find Active Students
↓
Resolve Payment Configuration
↓
Resolve Price
↓
Resolve Due Date
↓
Create Bill
↓
Dispatch New Bill Email
```

Automation harus idempotent.

Jika job dijalankan dua kali:

```text
Run #1 → Create SPP Bill
Run #2 → DO NOT create duplicate
```

---

# 58. H-2 Reminder Automation

Scheduler mencari bill dengan effective due date:

```text
today + 2 days
```

Kemudian:

```text
Bill
↓
Generate/find Invoice
↓
Dispatch Reminder Email
```

Invoice tidak dibuat duplicate apabila sudah tersedia.

---

# 59. Overdue Detection

Scheduler mengevaluasi:

```text
current date > effective due date
AND outstanding amount > 0
```

Maka:

```text
Bill Status → OVERDUE
```

Kemudian sistem mengirim:

```text
Overdue Email
+
Existing Invoice
```

Automation harus idempotent sehingga tidak mengirim email overdue berulang kali tanpa aturan yang jelas.

---

# 60. Transaction Boundaries

Operasi finansial penting harus menggunakan database transaction.

Contoh manual payment:

```text
BEGIN TRANSACTION

Create Payment
Update/Calculate Bill
Store Evidence Reference
Create Audit Record

COMMIT
```

Jika terjadi failure:

```text
ROLLBACK
```

Email sebaiknya tidak menjadi bagian dari database transaction.

---

# 61. Payment Transaction Boundary

Untuk Midtrans:

```text
Webhook received
↓
Validate
↓
BEGIN TRANSACTION
↓
Update Payment
↓
Recalculate Bill
↓
Create/Update Receipt
↓
Create Audit/Event
↓
COMMIT
↓
Dispatch Email Job
```

Dengan demikian payment state aman walaupun mail provider bermasalah.

---

# 62. Concurrency

Sistem harus mempertimbangkan race condition.

Contoh:

```text
Webhook A
Webhook B
```

atau:

```text
Admin Manual Payment
+
Midtrans Payment
```

secara bersamaan.

Database transaction dan locking/unique constraint harus digunakan apabila diperlukan.

---

# 63. Idempotent Billing

Billing generator harus memiliki unique business identity.

Contoh konsep:

```text
Academic Year
+
Student
+
Payment Type
+
Billing Period
```

digunakan untuk memastikan satu kewajiban yang sama tidak dibuat dua kali.

---

# 64. Idempotent Payment

Payment gateway transaction reference harus unique.

Payment manual juga harus memiliki transaction identity/reference.

Tujuan:

> Satu pembayaran tidak boleh tercatat dua kali karena request ulang atau webhook duplicate.

---

# 65. Error Handling

Application harus membedakan:

### Validation Error

Input pengguna tidak valid.

### Business Rule Error

Operation melanggar business rule.

### Integration Error

Midtrans/email provider gagal.

### System Error

Unexpected application/database failure.

User-facing error harus aman dan tidak membocorkan:

- stack trace;
- secret;
- API key;
- database detail.

---

# 66. Security Architecture

Minimal security requirement:

- password hashing;
- authorization;
- policy enforcement;
- CSRF protection;
- validation;
- mass-assignment protection;
- secure file access;
- secure document authorization;
- webhook verification;
- secret management melalui `.env`;
- HTTPS pada production;
- database constraints.

---

# 67. Secret Management

Secret tidak boleh disimpan di source code.

Contoh:

```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MAIL_USERNAME=
MAIL_PASSWORD=
APP_KEY=
```

Jangan:

```php
$serverKey = "SB-Mid-server-...";
```

---

# 68. File Security

Invoice, receipt, dan manual evidence tidak boleh menjadi public file tanpa authorization jika dokumen mengandung informasi pribadi/keuangan.

User hanya dapat membuka file yang berkaitan dengan:

```text
Authenticated User
↓
Owned Parent
↓
Linked Student
↓
Related Bill/Payment
↓
Document
```

---

# 69. Database Strategy

Database:

> MySQL

ORM:

> Eloquent

Database harus menggunakan:

- foreign keys;
- indexes;
- unique constraints;
- timestamps;
- nullable fields sesuai kebutuhan;
- enum/value object strategy sesuai implementation;
- transactional consistency.

Detail tabel dan kolom akan didefinisikan dalam:

> `SCHEMA.md`

---

# 70. Existing Schema Migration Strategy

Existing schema memiliki konsep:

```text
users
products
orders
order_details
payments
```

Architecture baru kemungkinan memerlukan perubahan domain model karena `products/orders` existing merupakan struktur payment boilerplate dan belum sepenuhnya merepresentasikan:

- Student;
- Parent;
- Bill;
- Payment Type;
- Academic Year;
- Installment;
- Audit;
- Pricing configuration.

Namun:

> **Jangan melakukan rewrite database tanpa analisis migration impact.**

Existing data harus diperiksa terlebih dahulu.

---

# 71. Refactoring Strategy

Refactoring dilakukan bertahap.

Urutan yang disarankan:

```text
Existing Project
↓
Understand Existing Code
↓
Identify Reusable Components
↓
Map Existing Models
↓
Identify Conflicts
↓
Design New Domain Model
↓
Migration Plan
↓
Incremental Refactor
↓
Verification
```

Jangan langsung:

```text
Delete Existing
↓
Build Everything Again
```

---

# 72. Reuse Strategy

Komponen existing harus digunakan kembali jika:

- masih kompatibel;
- tidak melanggar business rule;
- tidak menghambat architecture;
- tidak menyebabkan security issue.

Contoh yang kemungkinan dipertahankan:

- Laravel application;
- Filament installation;
- Midtrans package/configuration;
- existing authentication foundation;
- existing Vite/Tailwind setup;
- reusable UI components.

---

# 73. Replacement Criteria

Komponen existing boleh diganti apabila:

1. Tidak memenuhi requirement.
2. Tidak aman.
3. Tidak maintainable.
4. Tidak kompatibel dengan domain baru.
5. Menyebabkan technical debt signifikan.
6. Replacement memiliki benefit yang dapat dibuktikan.

Setiap replacement major harus didokumentasikan.

---

# 74. Service Layer

Business logic penting sebaiknya tidak ditulis seluruhnya di Filament Resource.

Contoh service:

```text
BillingService
PaymentService
PaymentTypeService
PricingService
DueDateService
AcademicYearService
InvoiceService
ReceiptService
ReportService
AuditService
EmailService
```

Nama dapat berubah selama tanggung jawab tetap jelas.

---

# 75. Action Pattern

Operation yang memiliki satu business intent dapat menggunakan Action class.

Contoh:

```text
CreateManualPayment
StartNewAcademicYear
GenerateMonthlyBills
GenerateInvoice
GenerateReceipt
ProcessMidtransWebhook
ResendEmail
```

Tujuannya agar business operation dapat dipanggil dari:

- Filament;
- scheduled job;
- webhook;
- CLI;
- future API;

tanpa duplikasi logic.

---

# 76. Events

Gunakan events untuk proses downstream yang tidak harus dilakukan secara synchronous.

Contoh:

```text
PaymentSucceeded
↓
Generate Receipt
↓
Send Email
```

atau:

```text
BillCreated
↓
Send New Bill Notification
```

Event harus digunakan secara proporsional.

Jangan membuat event untuk setiap operasi CRUD sederhana jika tidak memberikan manfaat.

---

# 77. Jobs

Jobs digunakan untuk pekerjaan asynchronous/berat.

Candidate jobs:

```text
SendEmailJob
GenerateReportJob
GenerateInvoiceJob
GenerateReceiptJob
ProcessPaymentNotificationJob
```

Job harus idempotent jika memungkinkan.

---

# 78. Scheduled Jobs

Scheduler digunakan untuk:

```text
GenerateMonthlyBills
SendDueReminders
DetectOverdueBills
```

Schedule harus didefinisikan secara eksplisit.

---

# 79. Notifications

Gunakan notification abstraction untuk:

- internal web notification;
- email notification jika cocok.

Namun email document delivery tetap harus memiliki tracking sendiri.

---

# 80. Queue Strategy

Untuk prototype, queue infrastructure harus tetap sederhana.

Tidak perlu menambahkan Redis/Kafka/RabbitMQ hanya untuk kebutuhan email jika belum diperlukan.

Gunakan queue mechanism yang paling sederhana dan kompatibel dengan deployment environment.

Jika menggunakan database queue:

```text
jobs table
```

dapat digunakan.

Architecture harus tetap memungkinkan queue backend diganti di kemudian hari.

---

# 81. Caching

Caching bukan prioritas utama prototype.

Jangan menambahkan caching kompleks sebelum terdapat kebutuhan performa nyata.

Caching dapat dipertimbangkan untuk:

- active academic year;
- static payment type metadata;
- dashboard aggregation.

Namun financial state tidak boleh menjadi stale karena caching.

---

# 82. Reporting Performance

Report query harus dirancang agar tidak:

- N+1 query;
- mengambil seluruh dataset ke memory jika tidak diperlukan;
- melakukan filtering hanya di PHP;
- melakukan query berbeda untuk setiap row.

Gunakan:

- eager loading;
- query builder;
- indexes;
- pagination;
- aggregate queries.

---

# 83. Dashboard Performance

Dashboard harus menggunakan aggregate query.

Contoh:

```text
COUNT(active students)
COUNT(paid bills)
COUNT(unpaid bills)
SUM(successful payments)
SUM(outstanding)
```

Jangan mengambil seluruh Payment record hanya untuk menghitung total.

---

# 84. Logging

Application log digunakan untuk:

- exception;
- integration failure;
- webhook issue;
- email failure;
- scheduler failure;
- unexpected business errors.

Log tidak boleh menyimpan:

- password;
- server key;
- client key;
- full payment credentials;
- sensitive secrets.

---

# 85. Observability

Prototype minimal harus dapat menjawab:

- apakah scheduler berjalan;
- apakah webhook diterima;
- apakah payment diproses;
- apakah email gagal;
- error apa yang terjadi.

Audit log tidak menggantikan application log.

Keduanya memiliki tujuan berbeda.

---

# 86. Application Log vs Audit Log

### Application Log

Untuk developer/system operator.

Contoh:

```text
Midtrans webhook failed validation.
SMTP connection failed.
Database exception.
```

### Audit Log

Untuk audit bisnis.

Contoh:

```text
Admin Joko recorded manual payment.
Super Admin changed SPP price for 5-A.
```

Jangan mencampurkan keduanya.

---

# 87. Testing Architecture

Testing harus mencakup minimal:

## Unit Tests

Untuk business logic:

- price resolution;
- due date resolution;
- payment calculation;
- installment calculation;
- status calculation.

## Feature Tests

Untuk:

- login;
- authorization;
- student management;
- parent access;
- billing;
- manual payment;
- report;
- audit.

## Integration Tests

Untuk:

- Midtrans webhook;
- email;
- document generation.

---

# 88. Financial Calculation Testing

Financial calculations harus diuji dengan kasus:

```text
Bill 500K
Payment 500K
→ Paid
```

```text
Bill 500K
Payment 200K
→ Outstanding 300K
```

```text
Bill 500K
Payment 200K
Payment 300K
→ Paid
```

```text
Bill 500K
Payment 200K
Past Due Date
→ Overdue
```

---

# 89. Authorization Testing

Minimal test:

```text
Super Admin
→ create payment type = allowed

Admin
→ create payment type = denied

User
→ create payment type = denied
```

```text
User A
→ access Student A = allowed

User A
→ access Student B = denied
```

```text
Admin
→ Audit Log = denied

Super Admin
→ Audit Log = allowed
```

---

# 90. Deployment Architecture

Prototype dapat menggunakan single application deployment.

Konseptual:

```text
Internet
   ↓
Web Server
   ↓
Laravel Application
   ├── Filament
   ├── Scheduler
   ├── Queue
   └── Storage
   ↓
MySQL
```

External:

```text
Laravel → Midtrans
Laravel → SMTP
Midtrans → Laravel Webhook
```

---

# 91. Local Development

Development environment minimal:

- PHP >= 8.2.
- Laravel 12.
- Composer.
- Node.js/npm.
- MySQL.
- Filament 5.
- Midtrans configuration.

Development dapat menjalankan:

```bash
php artisan serve
npm run dev
```

atau command existing project yang telah tersedia.

---

# 92. Environment Configuration

Environment-specific values harus berada di `.env`.

Kategori:

```text
Application
Database
Midtrans
Mail
Queue
Storage
```

Jangan commit secrets.

---

# 93. Midtrans Environment

Development menggunakan sandbox.

Production menggunakan production mode.

Konseptual:

```env
MIDTRANS_IS_PRODUCTION=false
```

Development:

```text
Laravel
↓
Midtrans Sandbox
```

Production:

```text
Laravel
↓
Midtrans Production
```

Server key dan client key harus berasal dari environment.

---

# 94. Webhook Security

Webhook URL harus:

- HTTPS pada production;
- memvalidasi notification;
- memverifikasi signature;
- menggunakan unique transaction reference;
- idempotent.

Jangan mempercayai status pembayaran dari client-side JavaScript.

---

# 95. Storage Architecture

Storage digunakan untuk:

- invoice;
- receipt;
- manual payment evidence.

Storage abstraction Laravel harus digunakan.

Jangan hardcode filesystem path di business logic.

Konfigurasi storage harus dapat dipindahkan dari:

```text
local
```

ke:

```text
cloud/object storage
```

jika diperlukan di masa depan.

---

# 96. Document Naming

Dokumen sebaiknya memiliki naming convention yang dapat ditelusuri.

Contoh:

```text
invoice/{academic_year}/{student}/{invoice_number}.pdf

receipt/{academic_year}/{student}/{receipt_number}.pdf
```

Exact path tidak boleh dianggap sebagai public URL.

Authorization tetap wajib.

---

# 97. Data Lifecycle

Data pembayaran merupakan historical financial data.

Jangan menghapus data pembayaran hanya karena:

- tahun ajaran berubah;
- santri lulus;
- santri keluar;
- payment type dinonaktifkan.

Historical data harus tetap dapat digunakan untuk:

- audit;
- laporan;
- histori;
- bukti pembayaran.

---

# 98. Soft Delete Strategy

Entity master seperti:

- Student;
- Parent;
- Payment Type;

dapat mempertimbangkan soft delete/non-active status.

Financial transaction:

- Bill;
- Payment;

tidak boleh dihapus secara sembarangan.

Financial data harus memiliki retention/history yang kuat.

---

# 99. Immutable Financial Records

Payment record yang sudah berhasil harus diperlakukan sebagai historical transaction.

Jangan mengedit nominal successful payment secara sembarangan.

Jika terjadi koreksi, gunakan mekanisme adjustment/reversal yang dapat diaudit apabila kebutuhan tersebut muncul.

Prototype belum membutuhkan modul reversal kompleks kecuali client meminta.

---

# 100. Consistency Rules

Architecture harus menjaga:

```text
Bill total >= Paid total
```

dan:

```text
Outstanding = Bill total - Paid total
```

Payment successful tidak boleh membuat outstanding negatif kecuali sistem secara eksplisit mendukung overpayment.

Untuk prototype, overpayment belum menjadi requirement.

---

# 101. Temporary Decision Handling

Semua keputusan temporary dari PRD harus:

1. Diimplementasikan.
2. Ditandai.
3. Dibuat cukup configurable jika memungkinkan.
4. Tidak dianggap sebagai business rule final.
5. Tidak dihapus hanya karena belum dikonfirmasi client.

Contoh:

```text
SPP due date = 1
```

harus berjalan pada prototype.

Namun code sebaiknya tidak membuat:

```php
const SPP_DUE_DATE = 1;
```

tersebar di banyak tempat.

Gunakan configuration/domain rule agar mudah diubah.

---

# 102. Open Requirement Handling

Jika requirement belum diketahui:

Jangan:

```text
Developer menebak
↓
Hardcode
```

Lebih baik:

```text
Unknown Requirement
↓
Flexible Architecture
↓
Temporary Default jika prototype membutuhkan
↓
Mark Client Confirmation Required
```

---

# 103. Dependency Direction

Business logic tidak boleh bergantung langsung pada UI.

Salah:

```text
Business Logic
↓
Filament Resource
```

Benar:

```text
Filament Resource
↓
Application Service / Action
↓
Domain Logic
```

Dengan demikian business logic dapat digunakan dari:

- Filament;
- Scheduler;
- Webhook;
- CLI;
- future API.

---

# 104. Avoid Fat Filament Resources

Jangan menempatkan seluruh logic seperti:

- billing;
- payment calculation;
- Midtrans;
- email;
- invoice;
- audit;

langsung dalam `Resource` atau `Page`.

Resource bertanggung jawab terutama terhadap presentation/configuration.

Business logic berada di service/action/domain layer.

---

# 105. Avoid Fat Models

Eloquent Model dapat memiliki:

- relationships;
- casts;
- scopes;
- simple accessors/mutators.

Business workflow kompleks sebaiknya berada di service/action.

Contoh:

```text
Bill
```

tidak seharusnya bertanggung jawab sendiri atas:

```text
generate invoice
send email
call Midtrans
create receipt
```

---

# 106. Integration Boundary

External integration harus diisolasi.

Contoh:

```text
Application
    ↓
PaymentGateway Interface
    ↓
Midtrans Implementation
```

Untuk email:

```text
Application
    ↓
Mail Abstraction
    ↓
SMTP Provider
```

Tujuannya agar vendor dapat diganti tanpa mengubah core business logic.

---

# 107. Future Extensibility

Architecture harus memungkinkan:

- payment gateway lain;
- email provider lain;
- additional payment methods;
- additional payment types;
- additional report formats;
- additional roles;
- future API;
- mobile application.

Namun:

> Jangan membangun abstraksi kompleks hanya untuk kemungkinan masa depan yang belum diperlukan.

Gunakan abstraction yang memiliki manfaat nyata.

---

# 108. Prototype Complexity Principle

Prototype boleh cukup kompleks apabila kompleksitas tersebut membantu client memahami arah sistem.

Namun kompleksitas harus:

- memiliki alasan;
- terdokumentasi;
- dapat diuji;
- tidak menghasilkan infrastructure yang tidak perlu.

Contoh:

> Multi-payment installment support

diimplementasikan karena merupakan requirement.

Sedangkan:

> Microservice architecture

tidak diperlukan karena tidak memberikan benefit nyata pada scope prototype.

---

# 109. Architecture Decision Summary

| Decision | Status |
|---|---|
| Laravel 12 | ✅ Confirmed |
| Filament 5 | ✅ Existing / Retain |
| MySQL | ✅ Existing / Retain |
| Midtrans | ✅ Existing / Required |
| Modular Monolith | ✅ Architecture Decision |
| All roles use Filament | ⚠️ Temporary |
| Parent/Student separate domain concepts | ✅ Required |
| Automatic monthly SPP billing | ⚠️ Temporary |
| Academic Year | ⚠️ Temporary |
| Automatic promotion | ⚠️ Temporary |
| Configurable payment type | ✅ Required |
| Installment support | ⚠️ Temporary |
| Manual payment | ✅ Required |
| Audit Log | ✅ Required |
| Async email | Architecture Decision |
| Email provider | ⚠️ Temporary |
| Excel + PDF reporting | ✅ Required |

---

# 110. Architecture Non-Goals

Architecture ini tidak bertujuan:

- membangun microservices;
- membuat distributed system;
- membuat mobile backend terpisah;
- membuat event-driven infrastructure kompleks;
- menggunakan Kubernetes;
- menggunakan message broker kompleks;
- mengganti Laravel;
- mengganti Filament;
- mengganti Midtrans;
- melakukan rewrite total existing project.

Semua hal tersebut hanya boleh dipertimbangkan jika requirement dan skala sistem di masa depan benar-benar membutuhkannya.

---

# 111. Final Architecture

Architecture target prototype:

```text
                           USERS
                             │
            ┌────────────────┼────────────────┐
            │                │                │
            ↓                ↓                ↓
       SUPER ADMIN         ADMIN           PARENT
            │                │                │
            └────────────────┼────────────────┘
                             ↓
                    ┌────────────────┐
                    │   FILAMENT 5   │
                    └───────┬────────┘
                            ↓
                ┌──────────────────────┐
                │ Laravel Application  │
                │                      │
                │ Auth                 │
                │ Authorization        │
                │ Services             │
                │ Actions              │
                │ Policies             │
                │ Events               │
                │ Jobs                 │
                │ Notifications        │
                │ Reporting            │
                │ Audit                │
                └──────────┬───────────┘
                           ↓
                ┌──────────────────────┐
                │      Eloquent        │
                └──────────┬───────────┘
                           ↓
                ┌──────────────────────┐
                │        MySQL         │
                └──────────────────────┘
                           │
          ┌────────────────┼─────────────────┐
          ↓                ↓                 ↓
      MIDTRANS           MAIL              STORAGE
          │                │                 │
          │                │                 ├── Invoice
          │                │                 ├── Receipt
          │                │                 └── Evidence
          │                │
          ↓                ↓
      WEBHOOK         EMAIL DELIVERY
```

Core principle:

> **Satu Laravel application, satu database utama, satu admin framework, dengan business logic yang dipisahkan dari presentation layer dan integrasi eksternal.**

Architecture harus cukup sederhana untuk prototype, tetapi cukup terstruktur agar dapat berkembang menjadi sistem administrasi pembayaran pondok yang production-ready tanpa harus melakukan rewrite total.