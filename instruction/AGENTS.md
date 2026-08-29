# AGENTS.md --- AI Coding Agent Instructions

> **Development Guidelines, Agent Behavior, Workflow, Constraints &
> Implementation Rules**
>
> Dokumen ini merupakan panduan kerja untuk AI coding agent yang
> mengembangkan Sistem Administrasi Pembayaran Pondok.
>
> Agent wajib membaca dan mengikuti dokumen ini sebelum melakukan
> perubahan pada codebase.

------------------------------------------------------------------------

# 1. Document Information

  Item                Detail
  ------------------- ---------------------------------------------
  Project             Sistem Administrasi Pembayaran Pondok
  Document            AGENTS.md
  Version             1.0 --- Prototype
  Primary Language    Bahasa Indonesia
  Primary Framework   Laravel + Filament
  Primary Database    PostgreSQL/MySQL sesuai existing project
  Payment Gateway     Midtrans
  Target              Prototype yang siap didemokan kepada client

------------------------------------------------------------------------

# 2. Purpose of This File

`AGENTS.md` mendefinisikan bagaimana AI coding agent harus bekerja di
project ini.

Dokumen ini bukan business requirement utama.

Business requirement berada di:

``` text
PRD.md
```

Architecture berada di:

``` text
ARCHITECTURE.md
```

Database berada di:

``` text
SCHEMA.md
```

UI/UX berada di:

``` text
DESIGN.md
```

Business/system rules berada di:

``` text
RULES.md
```

Sedangkan:

``` text
AGENTS.md
```

menentukan:

> **bagaimana AI harus mengimplementasikan seluruh dokumen tersebut ke
> dalam codebase.**

------------------------------------------------------------------------

# 3. Core Agent Philosophy

Agent harus bertindak seperti:

> **Senior software engineer yang menjaga existing project sambil
> membangun requirement baru.**

Bukan seperti:

> AI yang bebas membuat project dari nol.

Prioritas:

``` text
Understand
↓
Inspect
↓
Plan
↓
Implement
↓
Verify
↓
Document
```

Bukan:

``` text
Generate
↓
Hope it works
```

------------------------------------------------------------------------

# 4. Most Important Rule

> **JANGAN LANGSUNG NGODING SEBELUM MEMAHAMI EXISTING PROJECT.**

Sebelum melakukan perubahan besar, agent wajib:

1.  membaca struktur project;
2.  membaca existing README;
3.  membaca existing migrations;
4.  membaca models;
5.  membaca services;
6.  membaca routes;
7.  membaca Filament resources/pages;
8.  membaca authentication;
9.  membaca Midtrans integration;
10. membaca test yang tersedia;
11. membaca keenam specification files.

------------------------------------------------------------------------

# 5. Specification Reading Order

Sebelum implementation, agent membaca:

``` text
1. PRD.md
2. ARCHITECTURE.md
3. SCHEMA.md
4. DESIGN.md
5. RULES.md
6. AGENTS.md
```

Setelah itu:

``` text
7. Existing project files
```

------------------------------------------------------------------------

# 6. Requirement Hierarchy

Jika agent menemukan konflik:

``` text
Security / Data Integrity
        ↓
RULES.md
        ↓
PRD.md
        ↓
ARCHITECTURE.md
        ↓
SCHEMA.md
        ↓
DESIGN.md
        ↓
Implementation Convenience
```

Namun jika konflik tersebut menunjukkan specification yang tidak
konsisten:

> agent **tidak boleh diam-diam memilih salah satu.**

Agent harus mendokumentasikan conflict.

------------------------------------------------------------------------

# 7. Do Not Invent Requirements

Agent tidak boleh membuat business rule baru tanpa dasar.

Contoh yang tidak boleh diarang:

-   denda;
-   diskon;
-   beasiswa;
-   refund;
-   cicilan khusus jenis tertentu;
-   aturan kelulusan;
-   aturan siswa pindahan;
-   multi guardian;
-   late fee;
-   approval tambahan;
-   financial correction workflow baru;
-   payment policy baru.

Jika belum ditentukan:

> gunakan behavior paling sederhana yang aman atau tandai sebagai
> unresolved.

------------------------------------------------------------------------

# 8. Temporary Requirement Rule

Jika dokumen menyatakan:

> **TEMPORARY --- CLIENT CONFIRMATION REQUIRED**

maka agent harus memahami:

``` text
Temporary ≠ Optional
Temporary ≠ Skip
Temporary ≠ Ignore
```

Artinya:

``` text
Implement
+
Keep Flexible
+
Document
+
Mark For Client Confirmation
```

------------------------------------------------------------------------

# 9. Example of Temporary Rule

Contoh:

> SPP jatuh tempo tanggal 1.

Jika statusnya temporary:

Agent tetap membuat:

``` text
SPP due day = 1
```

Tetapi implementation **tidak boleh mengunci architecture** sehingga
perubahan ke:

``` text
tanggal 5
tanggal 10
tanggal custom
```

menjadi sulit.

------------------------------------------------------------------------

# 10. Existing Project Preservation

Project sudah memiliki pondasi dari developer sebelumnya.

Rule:

> **Jangan melakukan major rewrite tanpa alasan teknis yang jelas.**

Agent wajib berusaha:

``` text
Reuse
↓
Extend
↓
Refactor minimally
↓
Rewrite only if justified
```

------------------------------------------------------------------------

# 11. Major Rewrite Definition

Major rewrite termasuk:

-   mengganti framework;
-   mengganti Filament;
-   mengganti database;
-   menghapus architecture existing;
-   menghapus banyak migration;
-   mengganti authentication;
-   membuang existing Midtrans integration;
-   menghapus resource existing;
-   membuat ulang project dari awal.

Tidak boleh dilakukan hanya karena:

> "Saya punya cara yang lebih bagus."

------------------------------------------------------------------------

# 12. When Rewrite Is Allowed

Major change hanya boleh dilakukan jika:

1.  existing architecture benar-benar tidak kompatibel;
2.  terdapat security issue;
3.  terdapat data integrity issue;
4.  requirement tidak dapat diimplementasikan dengan architecture
    existing;
5.  maintenance cost existing architecture sangat tinggi;
6.  perubahan memberikan manfaat yang jelas.

Agent harus menjelaskan:

``` text
Problem
↓
Current Limitation
↓
Proposed Change
↓
Positive Impact
↓
Risk
```

------------------------------------------------------------------------

# 13. Filament Rule

Project menggunakan:

> **Filament**

Agent wajib mempertahankan Filament sebagai foundation.

Jika UI default Filament tidak sesuai:

> customize.

Jangan langsung mengganti Filament.

------------------------------------------------------------------------

# 14. Filament Customization Order

Gunakan pendekatan paling sederhana:

``` text
Filament Configuration
↓
Theme
↓
Tailwind
↓
CSS
↓
Custom Component
↓
Blade
↓
Deep Override
```

Hindari deep override jika tidak diperlukan.

------------------------------------------------------------------------

# 15. Avoid Fragile Filament Hacks

Jangan membuat customization yang bergantung pada:

-   internal DOM structure;
-   selector yang tidak stabil;
-   JavaScript hack;
-   internal private API;
-   monkey patch;
-   undocumented behavior.

Jika terpaksa:

> dokumentasikan alasannya.

------------------------------------------------------------------------

# 16. UI Customization Principle

Target UI:

> **Modern SaaS + Corporate**

Bukan:

> Filament default.

Namun:

> Jangan mengorbankan maintainability demi aesthetic.

------------------------------------------------------------------------

# 17. Responsive Requirement

Semua halaman harus diuji untuk:

``` text
Mobile
Tablet
Laptop
Desktop
```

Minimal target:

``` text
320px+
768px+
1024px+
1366px+
```

------------------------------------------------------------------------

# 18. Responsive Rule

Agent tidak boleh menganggap:

> "Kalau desktop bagus berarti responsive."

Responsive harus diverifikasi secara visual.

Periksa:

-   table;
-   modal;
-   form;
-   sidebar;
-   navigation;
-   button;
-   dropdown;
-   card;
-   pagination;
-   filter.

------------------------------------------------------------------------

# 19. Mobile First Thinking

Jika membuat component baru:

> pikirkan mobile behavior sejak awal.

Bukan:

``` text
Desktop
↓
Fix mobile later
```

------------------------------------------------------------------------

# 20. No Horizontal Overflow

Agent harus menghindari accidental horizontal overflow.

Terutama:

-   data table;
-   modal;
-   filter;
-   forms;
-   dashboard cards.

------------------------------------------------------------------------

# 21. Smooth Interaction Requirement

UI harus terasa smooth.

Perhatikan:

-   modal opening;
-   modal closing;
-   sidebar;
-   dropdown;
-   toast;
-   loading;
-   page transition;
-   confirmation;
-   table refresh.

Animation harus:

> subtle dan cepat.

------------------------------------------------------------------------

# 22. Unsaved Changes

Jika form memiliki perubahan yang belum disimpan:

``` text
X
Escape
Click outside
Navigate away
```

tidak boleh langsung membuang perubahan.

Agent harus menggunakan:

> unsaved changes confirmation.

------------------------------------------------------------------------

# 23. Unsaved Confirmation

Copy harus menjelaskan:

> perubahan belum disimpan dan akan hilang.

Contoh:

``` text
Tutup tanpa menyimpan?

Perubahan yang Anda buat belum disimpan
dan akan hilang.

[ Tetap Mengedit ] [ Tutup ]
```

------------------------------------------------------------------------

# 24. Destructive Actions

Action destructive harus memiliki confirmation.

Contoh:

-   delete;
-   deactivate;
-   price change;
-   academic year transition;
-   financial correction.

------------------------------------------------------------------------

# 25. Financial Actions

Financial action membutuhkan perhatian ekstra.

Sebelum final action, user harus dapat melihat:

``` text
Student
Bill
Amount
Date
Payment Method
```

------------------------------------------------------------------------

# 26. Financial Integrity Is Priority

Agent harus mengutamakan:

> financial correctness

dibanding:

> UI convenience.

------------------------------------------------------------------------

# 27. Never Trust Frontend Amount

Frontend dapat mengirim:

``` text
amount = 100000
```

Tetapi backend harus melakukan validation/resolution ulang.

Frontend bukan source of truth untuk:

-   amount;
-   price;
-   due date;
-   payment status.

------------------------------------------------------------------------

# 28. Price Resolution

Backend harus menentukan harga berdasarkan hierarchy:

``` text
Student Override
↓
Class + Rombel
↓
Payment Type Configuration
```

------------------------------------------------------------------------

# 29. Existing Bill Price

Setelah bill dibuat:

> nominal bill menjadi snapshot.

Perubahan master price tidak boleh mengubah bill lama.

------------------------------------------------------------------------

# 30. Due Date Snapshot

Setelah bill dibuat:

> due date bill menjadi snapshot.

Perubahan konfigurasi due date tidak boleh mengubah bill lama.

------------------------------------------------------------------------

# 31. Payment Status

Jangan mencampurkan:

``` text
Payment Status
```

dengan:

``` text
Bill Status
```

Payment:

``` text
pending
paid
failed
```

Bill:

``` text
unpaid
paid
overdue
```

------------------------------------------------------------------------

# 32. Bill Status Calculation

Gunakan satu business logic terpusat.

Rule:

``` text
paid:
outstanding <= 0

overdue:
outstanding > 0
AND current_date > due_date

unpaid:
outstanding > 0
AND current_date <= due_date
```

Jangan membuat logic berbeda pada:

-   dashboard;
-   report;
-   parent page;
-   admin page.

------------------------------------------------------------------------

# 33. Money Handling

Semua uang:

> integer Rupiah.

Jangan menggunakan:

``` text
float
double
```

untuk financial amount.

------------------------------------------------------------------------

# 34. Money Formatting

UI:

``` text
Rp500.000
Rp1.000.000
```

Database:

``` text
500000
1000000
```

------------------------------------------------------------------------

# 35. Midtrans Rule

Midtrans adalah payment gateway.

Internal database tetap menjadi:

> canonical record untuk payment.

------------------------------------------------------------------------

# 36. Midtrans Webhook

Payment successful harus diverifikasi melalui:

> server-side notification/webhook.

Jangan menganggap frontend redirect sebagai payment confirmation final.

------------------------------------------------------------------------

# 37. Webhook Idempotency

Agent wajib memastikan duplicate webhook:

> tidak membuat duplicate payment.

------------------------------------------------------------------------

# 38. Midtrans Failure

Jika Midtrans API gagal:

> jangan menganggap payment successful.

Jika status belum diketahui:

> gunakan pending.

------------------------------------------------------------------------

# 39. Payment Flow

Expected flow:

``` text
Parent
↓
Bill
↓
Create Payment
↓
Midtrans
↓
Webhook
↓
Validate
↓
Update Payment
↓
Recalculate Bill
↓
Generate Receipt
↓
Queue Email
```

------------------------------------------------------------------------

# 40. Webhook Processing

Webhook processing harus:

1.  validate authenticity;
2.  identify transaction;
3.  check idempotency;
4.  update payment;
5.  update bill;
6.  create necessary records;
7.  dispatch notification/email;
8.  record audit.

------------------------------------------------------------------------

# 41. Duplicate Prevention

Agent wajib mencegah duplicate:

-   bill;
-   payment;
-   receipt;
-   invoice;
-   academic year;
-   email reminder.

------------------------------------------------------------------------

# 42. Billing Idempotency

SPP automatic billing dapat dieksekusi ulang.

Tetapi:

> tidak boleh membuat duplicate bill.

Unique business key harus dipertimbangkan.

------------------------------------------------------------------------

# 43. Reminder Idempotency

H-2 reminder:

> satu reminder per bill/event.

Scheduler tidak boleh membuat spam.

------------------------------------------------------------------------

# 44. Overdue Notification Idempotency

Overdue notification harus memiliki mechanism untuk mencegah duplicate
notification.

------------------------------------------------------------------------

# 45. Receipt Idempotency

Satu successful payment:

> satu canonical receipt.

------------------------------------------------------------------------

# 46. Invoice Idempotency

Satu bill:

> satu canonical invoice.

Resend tidak membuat invoice baru.

------------------------------------------------------------------------

# 47. Email Resend

Resend hanya:

> mengirim ulang existing document/event.

Tidak boleh:

-   create bill;
-   create payment;
-   create receipt;
-   create invoice baru.

------------------------------------------------------------------------

# 48. Email Failure

Jika email gagal:

> payment/bill state tetap berdasarkan transaction sebenarnya.

Contoh:

``` text
Payment = paid
Email = failed
```

Tetap:

``` text
Payment = paid
```

------------------------------------------------------------------------

# 49. Internal Email Notification

Email failure harus dapat dilihat oleh:

-   Admin;
-   Super Admin.

Melalui:

> web notification.

------------------------------------------------------------------------

# 50. Parent Email Rules

Agent harus mengimplementasikan:

``` text
New Bill Available
↓
Email Parent

H-2
↓
Email Parent + Invoice

Payment Success
↓
Email Parent + Receipt

Overdue
↓
Email Parent + Invoice

Payment Failed
↓
No Parent Email
```

------------------------------------------------------------------------

# 51. Email Should Not Block Payment

Email sending sebaiknya menggunakan:

> queue/job.

Payment success tidak boleh bergantung pada keberhasilan SMTP.

------------------------------------------------------------------------

# 52. PDF Should Not Block Payment

Jika receipt PDF generation gagal:

> payment tetap successful.

Gunakan retry/error workflow.

------------------------------------------------------------------------

# 53. Queue Principle

Long-running operations sebaiknya asynchronous:

-   email;
-   PDF generation jika berat;
-   report generation jika berat.

------------------------------------------------------------------------

# 54. Scheduler Principle

Scheduled jobs harus:

-   idempotent;
-   safe to rerun;
-   logged;
-   failure-aware.

------------------------------------------------------------------------

# 55. SPP Scheduler

⚠️ **TEMPORARY --- CLIENT CONFIRMATION REQUIRED**

Prototype:

> SPP dibuat setiap tanggal 1.

Agent harus membuat mechanism yang nantinya dapat diubah jika client
menentukan billing policy lain.

------------------------------------------------------------------------

# 56. H-2 Scheduler

Scheduler harus mendeteksi bill:

``` text
due_date - 2 days
```

dan mengirim reminder jika belum lunas.

------------------------------------------------------------------------

# 57. Overdue Scheduler

Scheduler harus mendeteksi:

``` text
current_date > due_date
AND outstanding > 0
```

kemudian:

> membuat overdue notification/email sesuai rules.

------------------------------------------------------------------------

# 58. Academic Year Scheduler

Jangan membuat academic year transition otomatis berdasarkan tanggal.

Prototype menggunakan:

> manual action oleh Super Admin.

------------------------------------------------------------------------

# 59. Academic Year Transition

Action:

``` text
Mulai Tahun Ajaran Baru
```

harus:

-   protected;
-   confirmation;
-   idempotent;
-   auditable.

------------------------------------------------------------------------

# 60. Student Promotion

⚠️ **TEMPORARY --- CLIENT CONFIRMATION REQUIRED**

Prototype:

> semua santri aktif naik satu tingkat ketika tahun ajaran baru dimulai.

Agent harus memastikan:

> tidak terjadi promotion dua kali.

------------------------------------------------------------------------

# 61. Parent Data

Parent account:

> satu akun dapat memiliki banyak anak.

Jangan membuat:

``` text
one parent = one student
```

------------------------------------------------------------------------

# 62. Student--Parent Relationship

Relationship harus mendukung:

``` text
Parent
 ├── Student A
 ├── Student B
 └── Student C
```

------------------------------------------------------------------------

# 63. Parent Authorization

Setiap query parent harus memastikan ownership.

Contoh:

``` text
Bill
→ Student
→ Parent
→ Authenticated User
```

------------------------------------------------------------------------

# 64. Prevent IDOR

Jangan membuat endpoint:

``` text
/bills/123
```

yang hanya memeriksa:

> user sudah login.

Harus memeriksa:

> user memang memiliki akses terhadap bill tersebut.

------------------------------------------------------------------------

# 65. Admin Authorization

Admin harus dapat melakukan operational action.

Namun tidak boleh:

``` text
change master price
change restricted configuration
start academic year
```

kecuali permission explicitly diberikan.

------------------------------------------------------------------------

# 66. Super Admin Authorization

Super Admin dapat:

> semua capability Admin.

Dan:

> seluruh restricted administrative capability.

------------------------------------------------------------------------

# 67. Server-Side Authorization

Authorization harus berada di:

-   backend;
-   policy;
-   gate;
-   service;
-   resource authorization.

Hiding button bukan security.

------------------------------------------------------------------------

# 68. Role-Based UI

UI boleh menyembunyikan action berdasarkan role.

Tetapi:

> backend tetap wajib melakukan authorization.

------------------------------------------------------------------------

# 69. Audit Logging

Audit harus digunakan untuk perubahan penting.

Minimal:

``` text
who
what
when
entity
before
after
```

------------------------------------------------------------------------

# 70. Audit Never Editable

Audit log tidak boleh diedit dari UI.

------------------------------------------------------------------------

# 71. Financial History

Historical:

-   invoice;
-   bill;
-   payment;
-   receipt;

harus dipertahankan.

------------------------------------------------------------------------

# 72. No Destructive Financial Delete

Agent tidak boleh membuat:

``` text
DELETE payment
```

sebagai normal workflow untuk successful payment.

------------------------------------------------------------------------

# 73. Correction Workflow

Jika financial correction belum memiliki business rule:

> jangan mengarang correction mechanism.

Untuk prototype:

> protect the record.

------------------------------------------------------------------------

# 74. Manual Payment

Manual payment tetap wajib tersedia walaupun minor.

Admin dan Super Admin dapat:

> mencatat pembayaran offline.

------------------------------------------------------------------------

# 75. Manual Payment Audit

Manual payment harus mencatat:

-   operator;
-   bill;
-   amount;
-   payment method;
-   date;
-   notes;
-   evidence jika ada.

------------------------------------------------------------------------

# 76. Installment

⚠️ **TEMPORARY --- CLIENT CONFIRMATION REQUIRED**

Prototype harus mendukung cicilan untuk semua payment type.

Namun:

> payment type yang benar-benar wajib mendukung cicilan masih perlu
> dikonfirmasi client.

------------------------------------------------------------------------

# 77. Partial Payment

Jika:

``` text
paid < bill amount
```

bill belum lunas.

------------------------------------------------------------------------

# 78. Overpayment

Karena belum ada business rule final:

> backend sebaiknya mencegah overpayment.

Jangan membuat refund/credit mechanism tanpa requirement.

------------------------------------------------------------------------

# 79. Payment Reconciliation

Setiap payment harus dapat ditelusuri:

``` text
Payment
↓
Bill
↓
Student
↓
Parent
```

Untuk Midtrans:

``` text
Payment
↓
Midtrans Reference
```

------------------------------------------------------------------------

# 80. Report Rules

Report utama:

``` text
Laporan Pembayaran
Laporan Pemasukan
Laporan Tunggakan
```

------------------------------------------------------------------------

# 81. Report Format

Semua report utama:

``` text
Excel
PDF
```

------------------------------------------------------------------------

# 82. Report Period

Super Admin dapat menentukan:

-   tanggal;
-   bulan;
-   tahun;
-   tahun ajaran.

------------------------------------------------------------------------

# 83. Revenue Calculation

Revenue:

> hanya successful payment.

Jangan menggunakan:

> total bill.

------------------------------------------------------------------------

# 84. Report Consistency

Dashboard revenue dan report revenue harus menggunakan:

> business calculation yang sama.

------------------------------------------------------------------------

# 85. Database Change Rule

Sebelum mengubah database:

1.  inspect migrations;
2.  inspect models;
3.  inspect relationships;
4.  inspect existing data assumptions;
5.  check impact.

------------------------------------------------------------------------

# 86. Migration Rule

Migration harus:

-   reversible jika memungkinkan;
-   jelas;
-   atomic;
-   memiliki foreign key;
-   memiliki index yang diperlukan;
-   tidak merusak existing data tanpa explicit reason.

------------------------------------------------------------------------

# 87. Destructive Migration

Jangan membuat migration destructive terhadap existing data tanpa:

-   explicit requirement;
-   migration safety plan;
-   documented reason.

------------------------------------------------------------------------

# 88. Existing Migration Rule

Jika existing migration sudah digunakan:

> jangan mengedit migration lama sembarangan.

Buat migration baru jika perubahan schema diperlukan.

------------------------------------------------------------------------

# 89. Model Rule

Model harus mencerminkan domain.

Hindari model yang memiliki:

> terlalu banyak business logic yang seharusnya berada pada
> service/domain layer.

------------------------------------------------------------------------

# 90. Service Rule

Business logic kompleks seperti:

-   billing;
-   payment;
-   price resolution;
-   academic promotion;
-   notification;

sebaiknya berada di service/domain layer.

------------------------------------------------------------------------

# 91. Controller Rule

Controller tidak boleh menjadi tempat seluruh business logic.

Controller sebaiknya:

``` text
Validate
↓
Authorize
↓
Call Service
↓
Return Response
```

------------------------------------------------------------------------

# 92. Filament Resource Rule

Filament Resource digunakan untuk:

-   CRUD;
-   table;
-   form;
-   action.

Business logic tetap harus berada pada layer yang sesuai.

Jangan menaruh seluruh business rule dalam anonymous closure Filament.

------------------------------------------------------------------------

# 93. Validation Rule

Validation harus berada di backend.

Frontend validation hanya:

> UX enhancement.

------------------------------------------------------------------------

# 94. Request Validation

Gunakan validation yang jelas.

Contoh:

``` text
email
required
email
```

atau:

``` text
amount
required
integer
min:1
```

------------------------------------------------------------------------

# 95. Error Handling

User-facing error:

> Bahasa Indonesia.

Developer-facing log:

> technical detail.

------------------------------------------------------------------------

# 96. Logging

Log digunakan untuk:

-   exception;
-   external API failure;
-   webhook;
-   queue failure;
-   email failure;
-   critical system events.

------------------------------------------------------------------------

# 97. Do Not Log Secrets

Jangan log:

-   password;
-   API key;
-   Midtrans secret;
-   reset token;
-   sensitive credentials.

------------------------------------------------------------------------

# 98. Environment Variables

Secrets harus berada di environment configuration.

Contoh:

``` text
MIDTRANS_SERVER_KEY
MIDTRANS_CLIENT_KEY
```

bukan hardcoded dalam PHP.

------------------------------------------------------------------------

# 99. External Service Rule

Jika external service gagal:

> jangan merusak transaction state internal yang sudah berhasil.

------------------------------------------------------------------------

# 100. Email Provider

⚠️ **TEMPORARY / IMPLEMENTATION DECISION**

Provider email dipilih berdasarkan:

-   gratis;
-   reliable;
-   mudah diintegrasikan;
-   tidak membutuhkan biaya untuk prototype;
-   memiliki SMTP/API yang cocok.

Agent harus memprioritaskan:

> free/open-source/self-hostable atau free-tier solution.

Jangan mengunci architecture terlalu dalam ke provider tertentu.

------------------------------------------------------------------------

# 101. Email Architecture

Gunakan abstraction jika memungkinkan:

``` text
Application
↓
Mail Service
↓
Provider
```

bukan business logic langsung bergantung pada provider.

------------------------------------------------------------------------

# 102. Free Service Principle

Karena prototype tidak memiliki budget:

> jangan menggunakan paid service sebagai requirement mandatory.

Jika free tier digunakan:

> dokumentasikan limitasinya.

------------------------------------------------------------------------

# 103. AI Agent Must Research Current Tooling When Needed

Jika pemilihan library/service membutuhkan informasi terbaru:

> agent boleh melakukan research.

Namun external research tidak boleh menggantikan project specification.

------------------------------------------------------------------------

# 104. Dependency Rule

Jangan menambahkan dependency hanya untuk:

> hal yang bisa dilakukan dengan Laravel/Filament existing.

Setiap dependency baru harus memiliki alasan.

------------------------------------------------------------------------

# 105. Dependency Evaluation

Sebelum menambahkan package:

cek:

-   maintenance;
-   compatibility;
-   license;
-   security;
-   Laravel version compatibility;
-   Filament compatibility;
-   package size;
-   apakah benar-benar diperlukan.

------------------------------------------------------------------------

# 106. No Dependency Bloat

Jangan menambahkan banyak package untuk menyelesaikan masalah yang bisa
dilakukan dengan existing stack.

------------------------------------------------------------------------

# 107. Testing Requirement

Setiap feature penting harus memiliki testing yang sesuai.

Prioritas:

### High

-   authentication;
-   authorization;
-   payment;
-   billing;
-   pricing;
-   status;
-   Midtrans webhook;
-   duplicate prevention.

### Medium

-   email automation;
-   reports;
-   academic year.

### Lower

-   purely visual behavior.

------------------------------------------------------------------------

# 108. Business Rule Tests

Test harus memverifikasi:

``` text
unpaid
paid
overdue
```

------------------------------------------------------------------------

# 109. Billing Tests

Test:

``` text
SPP generated once
duplicate billing prevented
correct price resolved
correct due date resolved
```

------------------------------------------------------------------------

# 110. Payment Tests

Test:

``` text
successful payment
partial payment
failed payment
pending payment
duplicate webhook
```

------------------------------------------------------------------------

# 111. Authorization Tests

Minimal:

``` text
Parent cannot access other parent bill
Admin cannot change price
Admin cannot start academic year
Parent cannot access reports
Super Admin can perform Admin action
```

------------------------------------------------------------------------

# 112. Academic Year Tests

Test:

``` text
new year created
old year preserved
students promoted once
duplicate transition prevented
```

------------------------------------------------------------------------

# 113. Email Tests

Test:

``` text
new bill email
H-2 email
payment success email
overdue email
failed payment no parent email
email failure notification
```

------------------------------------------------------------------------

# 114. PDF Tests

Test:

``` text
invoice generated
receipt generated
report generated
```

------------------------------------------------------------------------

# 115. Report Tests

Test:

``` text
payment report
revenue report
outstanding report
period filters
Excel
PDF
```

------------------------------------------------------------------------

# 116. Testing Principle

Test:

> business behavior.

Jangan hanya test:

> apakah halaman berhasil dibuka.

------------------------------------------------------------------------

# 117. Before Coding

Agent harus membuat implementation plan.

Contoh:

``` text
## Plan

1. Inspect existing billing models.
2. Map current schema.
3. Identify reusable services.
4. Add missing bill fields.
5. Implement status calculation.
6. Add tests.
7. Update Filament UI.
8. Verify responsive behavior.
```

------------------------------------------------------------------------

# 118. Plan Before Major Changes

Untuk perubahan yang menyentuh banyak file:

> agent harus menjelaskan plan sebelum execution jika interaction mode
> memungkinkan.

------------------------------------------------------------------------

# 119. Small Changes

Untuk perubahan kecil seperti:

-   text;
-   spacing;
-   label;
-   simple validation;

tidak perlu membuat plan panjang.

------------------------------------------------------------------------

# 120. Large Changes

Untuk:

-   database;
-   payment;
-   authentication;
-   academic transition;
-   billing automation;

wajib memiliki plan.

------------------------------------------------------------------------

# 121. Inspect Before Modify

Sebelum modify file:

> baca file tersebut.

Jangan menebak isi file.

------------------------------------------------------------------------

# 122. Search Before Create

Sebelum membuat:

``` text
Service
Model
Component
Migration
Enum
```

search dulu apakah sudah ada.

------------------------------------------------------------------------

# 123. Avoid Duplicate Architecture

Jangan membuat banyak class yang memiliki tanggung jawab sama tanpa
memahami existing structure.

------------------------------------------------------------------------

# 124. Naming Consistency

Gunakan naming existing project jika reasonable.

Jangan mengganti naming hanya karena preference.

------------------------------------------------------------------------

# 125. Enum Rule

Status yang finite sebaiknya menggunakan enum/centralized constants jika
architecture project sudah menggunakan pattern tersebut.

------------------------------------------------------------------------

# 126. Hardcoded Business Values

Hindari hardcode seperti:

``` php
$dueDay = 1;
```

jika business rule memungkinkan configuration.

Untuk temporary prototype:

> default boleh `1`, tetapi design harus memungkinkan perubahan.

------------------------------------------------------------------------

# 127. Hardcoded Class

Kelas:

``` text
1–9
```

dan rombel:

``` text
A/B
```

merupakan temporary requirement.

Jangan membuat database architecture yang tidak memungkinkan perubahan
struktur akademik.

------------------------------------------------------------------------

# 128. Hardcoded Role

Gunakan role/permission system.

Jangan menyebar conditional berdasarkan nama user di seluruh codebase.

------------------------------------------------------------------------

# 129. Role Capability

Capability harus berdasarkan:

> role/permission.

Bukan nama user.

------------------------------------------------------------------------

# 130. Super Admin Inheritance

Jika menggunakan permission system:

> Super Admin harus mendapatkan seluruh permission Admin.

Jangan duplicate business implementation hanya untuk Super Admin.

------------------------------------------------------------------------

# 131. Parent Ownership Scope

Gunakan relationship/policy scope.

Jangan melakukan filtering parent hanya di frontend.

------------------------------------------------------------------------

# 132. Query Safety

Query parent harus selalu memiliki ownership constraints.

------------------------------------------------------------------------

# 133. N+1 Prevention

Untuk table/list:

> eager load relationship yang diperlukan.

Terutama:

``` text
bill
student
parent
payment
payment type
```

------------------------------------------------------------------------

# 134. Pagination

Jangan mengambil seluruh dataset untuk table besar.

Gunakan:

> pagination.

------------------------------------------------------------------------

# 135. Report Query

Report harus dirancang untuk dataset besar.

Hindari:

> load seluruh transaksi ke memory jika tidak diperlukan.

------------------------------------------------------------------------

# 136. PDF Query

PDF generation harus menggunakan query yang efisien.

------------------------------------------------------------------------

# 137. Export Query

Excel export juga harus memperhatikan:

-   memory;
-   pagination/chunking;
-   query efficiency.

------------------------------------------------------------------------

# 138. UI Table Performance

Jika data besar:

> gunakan server-side pagination/filtering.

------------------------------------------------------------------------

# 139. Search Performance

Search harus menggunakan:

-   indexed fields;
-   appropriate query;
-   debouncing jika necessary.

------------------------------------------------------------------------

# 140. Database Index

Kolom yang sering digunakan untuk:

-   lookup;
-   filtering;
-   relationship;
-   unique business constraint;

harus dipertimbangkan untuk index.

------------------------------------------------------------------------

# 141. Financial Unique Constraints

Database sebaiknya memiliki protection terhadap duplicate business
records.

Contoh:

``` text
student
payment_type
billing_period
academic_year
```

------------------------------------------------------------------------

# 142. Transaction Boundary

Financial operation harus menggunakan database transaction ketika
beberapa records harus konsisten.

------------------------------------------------------------------------

# 143. Concurrency

Agent harus mempertimbangkan race condition pada:

-   payment;
-   billing;
-   academic promotion;
-   duplicate webhook.

------------------------------------------------------------------------

# 144. Locking

Jika diperlukan:

> gunakan database locking/transaction untuk financial state.

Jangan mengandalkan JavaScript.

------------------------------------------------------------------------

# 145. Background Jobs

Jobs harus idempotent jika bisa dijalankan ulang.

------------------------------------------------------------------------

# 146. Failed Jobs

Failed jobs harus dapat:

-   dilihat;
-   retried;
-   dilacak.

------------------------------------------------------------------------

# 147. Queue Monitoring

Untuk prototype minimal:

> logging dan failed jobs cukup.

Tidak perlu menambahkan infrastructure monitoring kompleks tanpa
kebutuhan.

------------------------------------------------------------------------

# 148. Email Queue

Email job harus menyimpan context yang cukup untuk retry.

------------------------------------------------------------------------

# 149. Notification Queue

Internal notification tidak boleh menyebabkan payment transaction gagal.

------------------------------------------------------------------------

# 150. Document Generation

Invoice/receipt harus menggunakan canonical financial data.

Jangan mengambil amount dari frontend state.

------------------------------------------------------------------------

# 151. PDF Branding

Logo/warnanya saat ini masih temporary.

PDF harus menggunakan centralized branding configuration.

------------------------------------------------------------------------

# 152. Branding

Logo dan warna pondok:

> **TEMPORARY --- CLIENT DATA NOT AVAILABLE**

Agent menggunakan placeholder.

Jangan mengunci design ke placeholder.

------------------------------------------------------------------------

# 153. Design Token Rule

Brand colors harus centralized.

Contoh:

``` text
primary
success
warning
danger
background
surface
border
text
muted
```

------------------------------------------------------------------------

# 154. No Random Colors

Jangan membuat setiap halaman memiliki warna berbeda.

------------------------------------------------------------------------

# 155. Status Color Rule

Status:

``` text
Belum Bayar → Yellow
Lunas → Green
Terlambat → Red
```

harus konsisten di seluruh system.

------------------------------------------------------------------------

# 156. Status Accessibility

Status tidak boleh hanya dibedakan berdasarkan warna.

Harus ada:

> text label.

------------------------------------------------------------------------

# 157. User-Facing Language

UI utama:

> Bahasa Indonesia.

------------------------------------------------------------------------

# 158. Technical Terms

Istilah umum seperti:

-   Dashboard;
-   Invoice;
-   PDF;
-   Excel;
-   Midtrans;

boleh digunakan.

------------------------------------------------------------------------

# 159. Error Message

Error user-facing harus:

> human readable.

Jangan menampilkan raw SQL/database exception kepada user.

------------------------------------------------------------------------

# 160. Confirmation Copy

Confirmation harus menjelaskan impact.

Jangan hanya:

> "Are you sure?"

------------------------------------------------------------------------

# 161. Loading State

Setiap asynchronous action harus memiliki feedback.

Contoh:

``` text
Menyimpan...
Memproses...
Mengirim...
Membuat laporan...
```

------------------------------------------------------------------------

# 162. Double Submit Protection

Button financial harus disabled saat request berjalan.

------------------------------------------------------------------------

# 163. Accessibility

Agent harus menjaga:

-   keyboard;
-   focus;
-   labels;
-   contrast;
-   aria attributes;
-   reduced motion.

------------------------------------------------------------------------

# 164. No Accessibility Regression

Customization Filament tidak boleh menghilangkan:

-   focus;
-   keyboard navigation;
-   semantic labels.

------------------------------------------------------------------------

# 165. Security First

Agent harus selalu mempertimbangkan:

-   authentication;
-   authorization;
-   IDOR;
-   CSRF;
-   file upload;
-   secret exposure;
-   mass assignment;
-   SQL injection;
-   XSS.

Gunakan mechanism Laravel/Filament yang sudah tersedia jika
memungkinkan.

------------------------------------------------------------------------

# 166. Mass Assignment

Model harus aman dari mass assignment vulnerability.

------------------------------------------------------------------------

# 167. User Input

Jangan percaya:

-   request;
-   query parameter;
-   route parameter;
-   hidden input.

Semua harus divalidasi dan di-authorize.

------------------------------------------------------------------------

# 168. File Upload

Validate:

-   type;
-   size;
-   extension;
-   storage.

Jangan langsung trust filename.

------------------------------------------------------------------------

# 169. Secret Security

Jangan commit:

``` text
.env
Midtrans secrets
SMTP passwords
API keys
```

------------------------------------------------------------------------

# 170. Git Hygiene

Agent tidak boleh:

-   commit secret;
-   commit debug dumps;
-   commit temporary files;
-   commit generated sensitive data.

------------------------------------------------------------------------

# 171. Debug Code

Sebelum menyatakan feature selesai, pastikan tidak ada:

``` text
dd()
dump()
var_dump()
console.log()
debug-only route
```

yang tidak diperlukan.

------------------------------------------------------------------------

# 172. Production Safety

Jangan meninggalkan:

``` text
APP_DEBUG=true
```

sebagai production assumption.

------------------------------------------------------------------------

# 173. Database Seeders

Seeder boleh digunakan untuk:

-   development;
-   demo;
-   testing.

Namun seed data harus jelas sebagai:

> demo/sample data.

------------------------------------------------------------------------

# 174. Demo Data

Jangan menggunakan data client asli sebagai hardcoded seed tanpa
permission.

------------------------------------------------------------------------

# 175. Fake Payment

Development boleh menggunakan:

> Midtrans sandbox.

Jangan menganggap sandbox payment sebagai real payment.

------------------------------------------------------------------------

# 176. Midtrans Environment

Credentials sandbox dan production harus terpisah.

------------------------------------------------------------------------

# 177. Demo Safety

Prototype demo harus dapat menggunakan:

> fake/test data.

------------------------------------------------------------------------

# 178. Migration Safety

Sebelum menjalankan destructive database operation:

> pastikan environment benar.

Agent tidak boleh sembarangan menjalankan:

``` text
migrate:fresh
db:wipe
truncate
```

pada database yang berisi data penting.

------------------------------------------------------------------------

# 179. Existing Data Safety

Jika database existing berisi data:

> anggap data tersebut penting.

Jangan menghapus tanpa explicit instruction.

------------------------------------------------------------------------

# 180. Backup Principle

Sebelum major migration pada data existing:

> sarankan/siapkan backup strategy.

------------------------------------------------------------------------

# 181. Testing Environment

Automated tests harus menggunakan:

> test database/environment.

Jangan test destructive operation pada production DB.

------------------------------------------------------------------------

# 182. Documentation Updates

Jika implementation mengubah architecture:

> update documentation yang relevan.

Contoh:

``` text
Schema change
→ SCHEMA.md

Architecture change
→ ARCHITECTURE.md

UI change
→ DESIGN.md

Business rule change
→ RULES.md
```

------------------------------------------------------------------------

# 183. No Silent Specification Changes

Agent tidak boleh mengubah:

``` text
PRD.md
ARCHITECTURE.md
SCHEMA.md
DESIGN.md
RULES.md
```

secara diam-diam hanya untuk membuat code pass.

------------------------------------------------------------------------

# 184. Specification Change

Jika requirement berubah:

1.  identify;
2.  update affected specification;
3.  explain change;
4.  implement;
5.  test.

------------------------------------------------------------------------

# 185. Decision Log

Jika terdapat keputusan teknis penting:

> dokumentasikan alasan.

Contoh:

``` text
Decision:
Keep existing Midtrans service.

Reason:
Existing webhook already works and replacement
does not provide meaningful benefit.
```

------------------------------------------------------------------------

# 186. Minimal Change Principle

Jika existing code sudah melakukan:

``` text
80% requirement
```

agent harus mengubah:

> 20% yang kurang.

Bukan membuat ulang 100%.

------------------------------------------------------------------------

# 187. Refactor Before Feature

Jika code existing sangat buruk:

> refactor hanya bagian yang diperlukan untuk feature.

Hindari unrelated cleanup besar.

------------------------------------------------------------------------

# 188. No Scope Creep

Jika task:

> membuat billing feature

jangan sekalian:

-   redesign seluruh dashboard;
-   mengganti auth;
-   mengganti database;
-   menambah notification platform baru.

Kecuali diperlukan.

------------------------------------------------------------------------

# 189. One Concern at a Time

Perubahan sebaiknya memiliki scope jelas.

Contoh:

``` text
Feature:
Automatic SPP Billing

Includes:
- bill generation
- price resolution
- due date
- duplicate prevention
- tests

Does not include:
- unrelated UI redesign
```

------------------------------------------------------------------------

# 190. Before Completing a Task

Agent wajib melakukan:

``` text
Code
↓
Run relevant tests
↓
Run lint/static checks if available
↓
Inspect changed files
↓
Check unintended changes
↓
Summarize
```

------------------------------------------------------------------------

# 191. Never Claim Untested

Jika test belum dijalankan:

> jangan mengatakan:

> "sudah pasti berhasil."

Gunakan:

> "Implementasi selesai, tetapi belum diverifikasi."

------------------------------------------------------------------------

# 192. Verification Levels

Agent harus membedakan:

``` text
Implemented
Tested
Verified
Production-ready
```

Itu bukan hal yang sama.

------------------------------------------------------------------------

# 193. Final Response Format

Setelah implementation:

``` text
## Implemented
- ...

## Changed
- ...

## Tested
- ...

## Not Tested
- ...

## Temporary Decisions
- ...

## Remaining Issues
- ...
```

------------------------------------------------------------------------

# 194. Error Reporting

Jika gagal:

``` text
## Error

### What happened
...

### Root cause
...

### What was attempted
...

### Current state
...

### Next step
...
```

Jangan menyembunyikan failure.

------------------------------------------------------------------------

# 195. No Fake Success

Agent dilarang mengatakan:

> "berhasil"

jika command/test sebenarnya gagal.

------------------------------------------------------------------------

# 196. Command Safety

Sebelum menjalankan command destructive:

> pastikan target environment.

------------------------------------------------------------------------

# 197. Package Installation

Jika menambahkan package:

Agent harus menjelaskan:

``` text
Package
Reason
Alternative considered
Impact
```

untuk dependency yang significant.

------------------------------------------------------------------------

# 198. External Service Selection

Jika perlu memilih provider gratis:

prioritas:

``` text
Free/Open Source
↓
Free Tier
↓
Low Cost
```

Tetapi jangan memilih service hanya karena:

> "gratis"

tanpa mempertimbangkan reliability.

------------------------------------------------------------------------

# 199. Architecture Simplicity

Untuk prototype:

> modular monolith lebih disukai.

Jangan membuat microservice tanpa kebutuhan nyata.

------------------------------------------------------------------------

# 200. Avoid Overengineering

Jangan menambahkan:

-   Redis jika belum diperlukan;
-   Kafka;
-   RabbitMQ;
-   Kubernetes;
-   separate frontend/backend deployment;
-   microservices;

hanya demi terlihat "enterprise".

------------------------------------------------------------------------

# 201. But Do Not Underengineer Financial Logic

Simple architecture tidak berarti:

> financial logic boleh asal.

Billing/payment harus tetap:

-   transactional;
-   idempotent;
-   auditable;
-   secure.

------------------------------------------------------------------------

# 202. Domain Separation

Minimal logical domains:

``` text
Users/Auth
Students/Parents
Payment Types
Billing
Payments
Documents
Notifications
Academic Years
Reports
Audit
```

------------------------------------------------------------------------

# 203. Code Organization

Ikuti architecture existing.

Jangan membuat folder architecture baru hanya karena preference.

------------------------------------------------------------------------

# 204. Service Boundaries

Service sebaiknya merepresentasikan business operation.

Contoh:

``` text
BillingService
PaymentService
PricingService
AcademicYearService
NotificationService
ReportService
```

Nama final harus mengikuti existing project.

------------------------------------------------------------------------

# 205. No Giant Service

Jangan membuat satu:

``` text
PondokService
```

yang menangani seluruh system.

------------------------------------------------------------------------

# 206. No Giant Controller

Controller harus tetap tipis.

------------------------------------------------------------------------

# 207. No Giant Filament Resource

Jika resource terlalu kompleks:

> gunakan service/domain layer.

------------------------------------------------------------------------

# 208. Business Rule Reuse

Logic:

``` text
resolvePrice()
resolveDueDate()
calculateOutstanding()
calculateStatus()
```

harus reusable.

------------------------------------------------------------------------

# 209. Status Reuse

Satu sumber logic status.

------------------------------------------------------------------------

# 210. Notification Reuse

Email event harus menggunakan reusable mail/job/service.

------------------------------------------------------------------------

# 211. PDF Reuse

Invoice dan receipt generator harus reusable.

------------------------------------------------------------------------

# 212. Document Naming

Generated document harus memiliki naming yang predictable.

Contoh:

``` text
invoice-{invoice_number}.pdf
receipt-{receipt_number}.pdf
```

------------------------------------------------------------------------

# 213. Storage

Generated documents harus disimpan dengan struktur yang aman.

Contoh konsep:

``` text
documents/
├── invoices/
├── receipts/
└── reports/
```

------------------------------------------------------------------------

# 214. Storage Access

Parent hanya dapat mengakses:

> dokumen yang menjadi haknya.

------------------------------------------------------------------------

# 215. Report Storage

Report generated oleh Super Admin/Admin harus memiliki authorization.

------------------------------------------------------------------------

# 216. Temporary Branding Storage

Logo placeholder tidak boleh dianggap final asset.

------------------------------------------------------------------------

# 217. Client Confirmation Tracking

Agent harus menjaga daftar temporary requirement.

Minimal:

``` text
TEMP-001
SPP due date tanggal 1

TEMP-002
Automatic promotion

TEMP-003
Kelas 1-9

TEMP-004
Rombel A/B

TEMP-005
Semua payment type mendukung installment
```

Daftar final mengikuti seluruh specification.

------------------------------------------------------------------------

# 218. Client Confirmation Rule

Ketika client memberikan keputusan baru:

Agent harus:

``` text
Identify affected docs
↓
Update specification
↓
Update implementation
↓
Update tests
```

------------------------------------------------------------------------

# 219. Do Not Patch Only Code

Jika business rule berubah:

> jangan hanya mengubah code.

Specification harus diperbarui juga.

------------------------------------------------------------------------

# 220. Prototype vs Production

Agent harus membedakan:

### Prototype simplification

boleh:

-   placeholder branding;
-   simplified academic rules;
-   simplified manual payment;
-   free email provider;
-   limited configuration.

### Production safety

tidak boleh disederhanakan:

-   authentication;
-   authorization;
-   financial integrity;
-   webhook validation;
-   duplicate prevention;
-   password security.

------------------------------------------------------------------------

# 221. Prototype Demo Quality

Walaupun prototype:

> UI harus terasa polished.

Target:

``` text
Functional
+
Stable
+
Responsive
+
Professional
```

------------------------------------------------------------------------

# 222. Demo Flow

Agent harus memastikan demo utama dapat berjalan:

``` text
Login
↓
Create Parent
↓
Create Student
↓
Configure Payment
↓
Generate Bill
↓
View Invoice
↓
Pay
↓
Payment Recorded
↓
Receipt
↓
History
↓
Report
```

------------------------------------------------------------------------

# 223. Critical Demo Path

Critical path:

``` text
Parent
↓
Student
↓
Bill
↓
Midtrans
↓
Payment
↓
Receipt
```

Jika critical path rusak:

> feature tidak boleh dianggap complete.

------------------------------------------------------------------------

# 224. Admin Demo Path

``` text
Login Admin
↓
Search Student
↓
View Bill
↓
Record Manual Payment
↓
View Payment
↓
View Receipt
```

------------------------------------------------------------------------

# 225. Super Admin Demo Path

``` text
Login Super Admin
↓
Configure Payment Type
↓
Set Price
↓
Set Due Date
↓
Start Academic Year
↓
View Reports
```

------------------------------------------------------------------------

# 226. Parent Demo Path

``` text
Login
↓
View Children
↓
View Bills
↓
Pay via Midtrans
↓
View Receipt
↓
View History
```

------------------------------------------------------------------------

# 227. Final QA Checklist

Sebelum feature dianggap selesai:

### Functional

-   [ ] Requirement implemented
-   [ ] Business rule implemented
-   [ ] Authorization implemented
-   [ ] Validation implemented
-   [ ] Error handling implemented

### Financial

-   [ ] Amount correct
-   [ ] Outstanding correct
-   [ ] Status correct
-   [ ] Duplicate protected
-   [ ] Payment audited

### UI

-   [ ] Desktop
-   [ ] Tablet
-   [ ] Mobile
-   [ ] Loading
-   [ ] Success
-   [ ] Error
-   [ ] Empty state
-   [ ] Confirmation

### Security

-   [ ] Authentication
-   [ ] Authorization
-   [ ] Ownership check
-   [ ] Input validation
-   [ ] Secret protection

### Testing

-   [ ] Relevant tests run
-   [ ] No known regression
-   [ ] No debug code

------------------------------------------------------------------------

# 228. Final Agent Checklist Before Commit

``` text
[ ] Read relevant specifications
[ ] Inspected existing code
[ ] Did not rewrite unnecessarily
[ ] Implemented business rule
[ ] Added authorization
[ ] Added validation
[ ] Added tests
[ ] Tested changed behavior
[ ] Checked responsive UI
[ ] Checked error state
[ ] Checked loading state
[ ] Checked destructive action
[ ] Checked duplicate operation
[ ] Checked audit requirements
[ ] Checked temporary requirement status
[ ] Updated documentation if necessary
```

------------------------------------------------------------------------

# 229. Definition of Done

Feature dianggap selesai jika:

``` text
Requirement
    ↓
Implemented
    ↓
Authorized
    ↓
Validated
    ↓
Tested
    ↓
UI Verified
    ↓
No Critical Regression
    ↓
Documented
```

Bukan hanya:

``` text
Code compiles
```

------------------------------------------------------------------------

# 230. Final Agent Behavior

AI coding agent harus:

> **berpikir sebelum mengubah.**

> **membaca sebelum membuat.**

> **memeriksa sebelum menghapus.**

> **mengikuti requirement sebelum berimprovisasi.**

> **menjaga data sebelum mengejar aesthetic.**

> **bertanya sebelum mengambil keputusan business yang belum
> ditentukan.**

------------------------------------------------------------------------

# 231. Absolute Agent Rules

Berikut aturan yang tidak boleh dilanggar:

### AGENT-001

> Jangan langsung coding sebelum memahami existing project.

### AGENT-002

> Jangan melakukan major rewrite tanpa alasan teknis yang jelas.

### AGENT-003

> Jangan mengarang business requirement.

### AGENT-004

> Temporary requirement tetap wajib diimplementasikan.

### AGENT-005

> Jangan mengubah temporary requirement menjadi permanent assumption.

### AGENT-006

> Jangan mempercayai frontend sebagai source of truth untuk financial
> data.

### AGENT-007

> Jangan menghapus successful financial transaction secara normal.

### AGENT-008

> Jangan membiarkan duplicate webhook menghasilkan duplicate payment.

### AGENT-009

> Jangan membiarkan duplicate scheduler menghasilkan duplicate
> bill/email.

### AGENT-010

> Semua authorization harus dilakukan server-side.

### AGENT-011

> Parent hanya boleh mengakses data miliknya.

### AGENT-012

> Semua capability Admin juga harus tersedia untuk Super Admin.

### AGENT-013

> Hanya Super Admin yang boleh menentukan nominal pembayaran.

### AGENT-014

> Jangan mengubah historical financial records karena perubahan master
> configuration.

### AGENT-015

> Jangan mengklaim feature berhasil jika belum diverifikasi.

### AGENT-016

> Jangan menyembunyikan error.

### AGENT-017

> Jangan meninggalkan debug code.

### AGENT-018

> Jangan menambahkan dependency tanpa alasan.

### AGENT-019

> Jangan mengganti Filament hanya karena tidak menyukai default UI.

### AGENT-020

> UI harus responsive dan usable di mobile, tablet, dan desktop.

------------------------------------------------------------------------

# 232. Final Development Principle

Project ini harus dibangun dengan prinsip:

``` text
                    CLIENT REQUIREMENT
                           │
                           ↓
                        PRD.md
                           │
                           ↓
                    ARCHITECTURE.md
                           │
                           ↓
                       SCHEMA.md
                           │
                           ↓
                       RULES.md
                           │
                           ↓
                       DESIGN.md
                           │
                           ↓
                       AGENTS.md
                           │
                           ↓
                     EXISTING CODE
                           │
                           ↓
                       IMPLEMENT
                           │
                           ↓
                         TEST
                           │
                           ↓
                        VERIFY
```

`AGENTS.md` tidak boleh digunakan untuk mengubah business requirement.

Fungsinya adalah memastikan AI:

> **mengimplementasikan requirement dengan cara yang aman, konsisten,
> terukur, dan tidak merusak pondasi project yang sudah ada.**

------------------------------------------------------------------------

# 233. Final Objective

Tujuan agent bukan sekadar:

> membuat aplikasi "jalan".

Tujuannya adalah menghasilkan prototype yang ketika didemokan kepada
client:

-   alurnya dapat dipahami;
-   UI terlihat profesional;
-   pembayaran dapat ditelusuri;
-   histori tersedia;
-   laporan masuk akal;
-   role bekerja sesuai kewenangan;
-   automation terlihat nyata;
-   bagian yang masih membutuhkan keputusan client dapat diidentifikasi
    dengan jelas.

> **Build the system that was specified.**
>
> **Do not invent the system that was not specified.**
>
> **Preserve what already works.**
>
> **Change only what needs to change.**
>
> **When a requirement is temporary, implement it --- but keep it
> replaceable.**
