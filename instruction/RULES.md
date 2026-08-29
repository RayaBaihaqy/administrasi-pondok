# RULES.md — Sistem Administrasi Pembayaran Pondok

> **Business Rules, Authorization Rules, Financial Rules, Automation Rules & System Constraints**
>
> Dokumen ini merupakan sumber aturan utama yang harus dipatuhi oleh aplikasi dan developer/AI agent ketika mengimplementasikan Sistem Administrasi Pembayaran Pondok.

---

# 1. Document Information

| Item | Detail |
|---|---|
| Nama Sistem | Sistem Administrasi Pembayaran Pondok |
| Dokumen | RULES |
| Versi | 1.0 — Prototype |
| Status | Active Specification |
| Bahasa Business Rule | Bahasa Indonesia |
| Primary Users | Super Admin, Admin, Orang Tua |

---

# 2. Purpose

`RULES.md` mendefinisikan:

- siapa yang boleh melakukan sesuatu;
- siapa yang tidak boleh melakukan sesuatu;
- bagaimana tagihan dibuat;
- bagaimana pembayaran dihitung;
- bagaimana cicilan bekerja;
- bagaimana jatuh tempo ditentukan;
- bagaimana tahun ajaran bekerja;
- bagaimana invoice dan receipt bekerja;
- bagaimana email automation bekerja;
- bagaimana audit bekerja;
- bagaimana data finansial dilindungi;
- bagaimana sistem menangani error;
- bagaimana AI/developer harus mengambil keputusan ketika requirement belum final.

---

# 3. Rule Priority

Jika terdapat konflik antara aturan, gunakan prioritas berikut:

```text
1. Security & Data Integrity
2. Financial Integrity
3. Authorization
4. Confirmed Business Rules
5. Temporary Business Rules
6. UX Preference
7. Implementation Convenience
```

Developer/AI **tidak boleh mengorbankan financial integrity hanya demi implementation yang lebih mudah.**

---

# 4. Requirement Status

Setiap rule memiliki salah satu status:

### ✅ CONFIRMED

Sudah disepakati dan dianggap sebagai requirement.

### ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Digunakan untuk prototype karena keputusan final belum dikonfirmasi kepada client.

Rule tetap **WAJIB diimplementasikan**.

Temporary berarti:

> "Implementasikan sekarang, tetapi jangan anggap keputusan ini final."

### 🔶 OPEN

Belum memiliki keputusan yang cukup untuk implementasi final.

Jika diperlukan untuk prototype, gunakan implementation paling sederhana yang tidak mengunci architecture secara permanen.

### ❌ OUT OF SCOPE

Tidak diperlukan pada prototype.

---

# 5. Fundamental System Rule

Sistem ini merupakan:

> **Sistem administrasi pembayaran pondok.**

Tujuan utama:

- memudahkan administrasi pembayaran;
- memudahkan pengecekan siapa yang sudah membayar;
- memudahkan pengecekan siapa yang belum membayar;
- memudahkan pengecekan tunggakan;
- memudahkan monitoring pemasukan;
- mengurangi ketergantungan terhadap Excel;
- menyediakan histori pembayaran;
- menyediakan audit trail.

---

# 6. Roles

Sistem memiliki tiga role:

```text
super_admin
admin
parent
```

Dalam UI:

```text
Super Admin
Admin
Orang Tua
```

---

# 7. Super Admin

Super Admin merepresentasikan:

> Kepala Bendahara Pondok.

Super Admin memiliki akses paling tinggi terhadap fungsi administrasi pembayaran.

---

# 8. Admin

Admin merepresentasikan:

> Karyawan/operator administrasi pondok.

Role detail pekerjaan karyawan masih belum dikonfirmasi secara spesifik oleh client.

Namun untuk prototype:

> Admin dianggap sebagai operator administrasi pembayaran.

---

# 9. Parent

Parent merepresentasikan:

> Orang tua/wali santri.

Parent **bukan santri**.

Parent memiliki akun untuk:

- melihat tagihan;
- melakukan pembayaran;
- melihat histori;
- mengakses invoice;
- mengakses bukti pembayaran.

---

# 10. Role Inheritance

Aturan penting:

> **Semua hal yang dapat dilakukan Admin juga dapat dilakukan Super Admin.**

Secara konseptual:

```text
Super Admin
    │
    ├── seluruh capability Admin
    │
    └── capability khusus Super Admin
```

Jangan membuat capability Admin yang tidak dapat dilakukan Super Admin.

---

# 11. Super Admin Exclusive Permissions

Hanya Super Admin yang boleh:

- menentukan nominal pembayaran;
- membuat jenis pembayaran;
- mengubah jenis pembayaran;
- menentukan nominal berdasarkan kelas;
- menentukan nominal berdasarkan rombel;
- menentukan nominal khusus per santri;
- mengatur tanggal jatuh tempo;
- mengubah tanggal jatuh tempo;
- mengubah seluruh tanggal jatuh tempo;
- mengubah tanggal jatuh tempo per jenis pembayaran;
- mengubah tanggal jatuh tempo per santri;
- memulai tahun ajaran baru;
- menjalankan proses kenaikan kelas;
- mengakses konfigurasi administrasi tingkat tinggi.

---

# 12. Admin Permissions

Admin dapat:

- melihat data santri;
- menambah data santri;
- mengubah data santri;
- melihat data orang tua;
- menambah data orang tua;
- mengubah data orang tua;
- melihat tagihan;
- melihat pembayaran;
- melakukan operasional pembayaran;
- mencatat pembayaran manual/offline;
- melihat histori pembayaran;
- melihat laporan yang diberikan kepada Admin;
- mengirim ulang invoice/dokumen jika diizinkan oleh workflow;
- menjalankan tugas administrasi sehari-hari.

Admin **tidak boleh menentukan nominal master pembayaran.**

---

# 13. Parent Permissions

Parent hanya dapat mengakses data yang berkaitan dengan dirinya dan anaknya.

Parent dapat:

- login;
- melihat profil;
- melihat anak;
- melihat tagihan;
- melihat status pembayaran;
- melakukan pembayaran online melalui Midtrans;
- melihat invoice;
- melihat bukti pembayaran;
- melihat histori pembayaran;
- melakukan reset password.

Parent tidak dapat:

- membuat tagihan;
- mengubah nominal;
- mengubah due date;
- mengubah data santri;
- mengubah data parent lain;
- melihat data santri lain;
- melihat data keuangan pondok secara global;
- melihat audit log;
- mengakses administrative configuration.

---

# 14. Parent Account Rule

Satu orang tua menggunakan:

> **satu akun parent.**

Satu parent dapat memiliki:

```text
1 akun
    │
    ├── Anak 1
    ├── Anak 2
    └── Anak 3
```

Tidak perlu membuat satu akun untuk setiap anak.

---

# 15. Parent–Student Rule

Satu parent dapat memiliki banyak student.

Minimal prototype:

```text
Parent 1 → N Students
```

---

# 16. Student–Parent Creation Rule

Sistem harus memungkinkan dua workflow:

### Workflow A

```text
Create Parent
↓
Create Student
↓
Attach Student
```

### Workflow B

```text
Create Student
↓
Create/Select Parent
↓
Attach Student
```

Tidak boleh memaksa user harus selalu membuat parent terlebih dahulu.

---

# 17. Student Data Rule

Student harus memiliki minimal:

- NIS;
- nama;
- kelas;
- rombel;
- status.

Kelas prototype:

```text
1
2
3
4
5
6
7
8
9
```

---

# 18. Rombel Rule

Untuk prototype:

```text
A
B
```

sehingga contoh:

```text
5-A
5-B
6-A
6-B
```

---

# 19. Temporary Academic Structure

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Kelas:

> 1–9

Rombel:

> A/B

tetap wajib digunakan untuk prototype.

Jangan menghapus fitur hanya karena struktur akademik final belum dikonfirmasi.

---

# 20. Academic Year

Sistem menggunakan konsep:

> Tahun Ajaran.

Contoh:

```text
2026/2027
2027/2028
```

---

# 21. Active Academic Year

Hanya satu tahun ajaran yang boleh berstatus:

```text
ACTIVE
```

pada satu waktu.

---

# 22. Start New Academic Year

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Tahun ajaran baru dimulai melalui action Super Admin:

> **Mulai Tahun Ajaran Baru**

Bukan sekadar perubahan otomatis berdasarkan tanggal.

---

# 23. Academic Year Transition

Ketika Super Admin memulai tahun ajaran baru:

```text
Create New Academic Year
↓
Set New Year Active
↓
Close Previous Year
↓
Promote Students
↓
Create New Academic Placement
```

Histori tahun ajaran lama harus dipertahankan.

---

# 24. Automatic Student Promotion

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Pada prototype:

> semua santri aktif otomatis naik satu tingkat kelas ketika tahun ajaran baru dimulai.

Contoh:

```text
2025/2026
5-A
     ↓
2026/2027
6-A
```

---

# 25. Final Grade Behavior

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Jika santri berada di kelas 9, behavior setelah tahun ajaran baru belum dikonfirmasi.

Sistem tidak boleh mengarang behavior seperti:

- otomatis lulus;
- otomatis nonaktif;
- tetap kelas 9.

Untuk prototype, behavior tersebut harus dibuat configurable/guarded dan ditandai sebagai temporary.

---

# 26. Academic History

Tahun ajaran lama tidak boleh dihapus ketika tahun ajaran baru dimulai.

Contoh:

```text
Ahmad

2024/2025 → 4-A
2025/2026 → 5-A
2026/2027 → 6-A
```

Histori harus dapat digunakan untuk reporting.

---

# 27. Payment Type

Jenis pembayaran merupakan master configuration.

Contoh:

```text
SPP
Uang Gedung
Uang Makan
Kitab
Seragam
```

---

# 28. Payment Type Creation

Hanya Super Admin yang boleh membuat jenis pembayaran baru.

Admin tidak boleh membuat jenis pembayaran baru.

Parent tidak boleh membuat jenis pembayaran.

---

# 29. Payment Type Modification

Hanya Super Admin yang boleh:

- mengubah nama;
- mengubah deskripsi;
- mengubah billing behavior;
- mengatur apakah payment aktif;
- mengatur dukungan cicilan;
- mengubah konfigurasi pembayaran.

---

# 30. Payment Type Status

Payment type dapat:

```text
active
inactive
```

Payment type inactive:

> tidak digunakan untuk membuat tagihan baru.

Histori tagihan lama tetap tersedia.

---

# 31. No Destructive Payment Type Delete

Payment type yang sudah pernah digunakan dalam transaksi finansial tidak boleh dihapus secara destructive.

Gunakan:

> Nonaktifkan.

---

# 32. Payment Amount Authority

**Hanya Super Admin yang boleh menentukan nominal pembayaran.**

Ini termasuk:

- nominal default;
- nominal berdasarkan kelas;
- nominal berdasarkan rombel;
- nominal khusus santri.

---

# 33. Admin Cannot Change Price

Admin tidak boleh:

```text
Edit Payment Price
```

walaupun Admin dapat:

```text
View Payment Price
```

---

# 34. Group Pricing

Super Admin menentukan nominal berdasarkan kelompok.

Contoh:

```text
Kelas 5-A → Rp500.000
Kelas 5-B → Rp550.000
Kelas 6-A → Rp600.000
Kelas 6-B → Rp600.000
```

---

# 35. Pricing Hierarchy

Jika sistem mencari nominal untuk seorang student:

```text
Student Override
        ↓
Class + Rombel Price
        ↓
Payment Type Configuration
```

Nominal paling spesifik memiliki prioritas paling tinggi.

---

# 36. Student Price Override

Super Admin dapat menentukan nominal khusus seorang santri.

Workflow:

```text
Search Student
↓
Open Student Detail
↓
Payment Configuration
↓
Set Special Price
```

---

# 37. Student Override Authority

Hanya Super Admin yang boleh membuat atau mengubah student-specific pricing.

Admin hanya dapat melihat informasi tersebut jika permission memungkinkan.

---

# 38. Existing Bill Price Immutability

Jika nominal master berubah:

> tagihan yang sudah dibuat tidak ikut berubah.

Contoh:

```text
SPP August = Rp500.000
```

kemudian:

```text
SPP = Rp600.000
```

maka:

```text
August Bill = Rp500.000
September Bill = Rp600.000
```

---

# 39. Payment Due Date

Setiap bill harus memiliki:

> `due_date`.

---

# 40. SPP Due Date

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Untuk prototype:

> SPP jatuh tempo setiap tanggal 1.

---

# 41. SPP Recurrence

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

SPP dibuat sebagai:

> recurring monthly bill.

Bill SPP dibuat untuk setiap periode bulanan.

---

# 42. Other Payment Due Date

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Untuk jenis pembayaran selain SPP:

> due date dapat ditentukan secara manual/configurable.

---

# 43. Due Date Configuration

Super Admin dapat menentukan:

### Global Payment Type

```text
SPP
Due Day = 1
```

### Per Student

```text
Student A
Due Date = custom
```

---

# 44. Due Date Priority

Jika terdapat beberapa konfigurasi:

```text
Student-specific override
        ↓
Payment-type configuration
        ↓
System/default configuration
```

---

# 45. Due Date Snapshot

Setelah bill dibuat:

> `bill.due_date` menjadi snapshot.

Mengubah master due date tidak boleh mengubah due date bill lama.

---

# 46. Bill Creation

Bill dapat dibuat:

### Automatically

Untuk recurring payment seperti SPP.

### Manually

Untuk jenis pembayaran yang membutuhkan action administratif.

---

# 47. Automatic SPP Billing

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Prototype:

```text
Tanggal 1
↓
Generate SPP bills
↓
Active students
↓
Resolve price
↓
Resolve due date
↓
Create bill
↓
Generate invoice
```

---

# 48. Duplicate Bill Prevention

System tidak boleh membuat dua bill untuk:

```text
Student
+
Payment Type
+
Academic Year
+
Billing Period
```

yang sama.

---

# 49. Bill Status

Status utama hanya:

```text
unpaid
paid
overdue
```

UI:

```text
🟡 Belum Bayar
🟢 Lunas
🔴 Terlambat
```

---

# 50. Unpaid Rule

Bill berstatus:

> Belum Bayar

jika:

```text
outstanding_amount > 0
AND current_date <= due_date
```

---

# 51. Paid Rule

Bill berstatus:

> Lunas

jika:

```text
outstanding_amount <= 0
```

atau seluruh nominal telah dibayar melalui successful payments.

---

# 52. Overdue Rule

Bill berstatus:

> Terlambat

jika:

```text
outstanding_amount > 0
AND current_date > due_date
```

---

# 53. Status Priority

Jika bill:

```text
due_date passed
```

tetapi sudah lunas:

> status tetap **Lunas**.

Jangan menampilkan:

> Terlambat

untuk bill yang sudah dibayar penuh.

---

# 54. Payment Status

Payment memiliki status berbeda dari bill:

```text
pending
paid
failed
```

Bill status:

```text
unpaid
paid
overdue
```

Jangan mencampurkan kedua konsep tersebut.

---

# 55. Successful Payment

Hanya payment dengan status:

```text
paid
```

yang dihitung sebagai pembayaran berhasil.

---

# 56. Failed Payment

Payment failed:

- tidak mengurangi outstanding;
- tidak dianggap revenue;
- tidak membuat bill menjadi paid;
- tidak membuat receipt successful;
- tidak mengubah bill menjadi lunas.

---

# 57. Pending Payment

Payment pending:

- belum dianggap pembayaran berhasil;
- belum dihitung revenue;
- tidak mengurangi outstanding secara final.

---

# 58. Revenue Rule

Revenue hanya dihitung dari:

```text
successful payments
```

Bukan:

```text
pending
failed
```

---

# 59. Installment

Sistem **wajib mendukung cicilan secara teknis.**

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Detail payment type mana yang benar-benar memperbolehkan cicilan masih perlu dikonfirmasi client.

Untuk prototype:

> semua payment type harus mampu mengakomodasi cicilan.

---

# 60. Installment Example

Bill:

```text
Rp1.000.000
```

Payment:

```text
Rp400.000
Rp300.000
Rp300.000
```

Total:

```text
Rp1.000.000
```

Status:

> Lunas.

---

# 61. Partial Payment

Jika:

```text
Bill = Rp1.000.000
Paid = Rp400.000
```

maka:

```text
Outstanding = Rp600.000
```

Jika belum melewati due date:

> Belum Bayar.

Jika melewati due date:

> Terlambat.

---

# 62. Overpayment

⚠️ **OPEN**

Behavior overpayment belum ditentukan.

Prototype harus mencegah payment amount menyebabkan:

```text
paid_amount > bill.amount
```

kecuali business rule kemudian menentukan mekanisme credit/refund.

---

# 63. Payment Source

Minimal:

```text
Midtrans
Manual
```

---

# 64. Online Payment

Parent dapat melakukan pembayaran sendiri melalui:

> Midtrans.

Flow:

```text
Parent
↓
Bill
↓
Bayar
↓
Midtrans
↓
Payment Gateway
↓
Webhook
↓
System
↓
Payment recorded
```

---

# 65. Midtrans as Payment Confirmation Source

System tidak boleh hanya mengandalkan:

> frontend redirect.

Status final payment harus dikonfirmasi melalui:

> Midtrans webhook/server notification.

---

# 66. Webhook Idempotency

Webhook yang sama dapat dikirim lebih dari sekali.

System harus memastikan:

> webhook duplicate tidak membuat duplicate payment.

---

# 67. Manual Payment

Admin dan Super Admin dapat mencatat pembayaran manual/offline.

Fitur ini:

> minor feature.

Namun tetap wajib tersedia.

---

# 68. Manual Payment Operator

Manual payment hanya dapat dicatat oleh:

```text
Admin
Super Admin
```

Parent tidak dapat membuat manual payment.

---

# 69. Manual Payment Audit

Manual payment wajib mencatat:

- siapa yang mencatat;
- nominal;
- waktu;
- metode;
- bill;
- catatan;
- evidence jika tersedia.

---

# 70. Payment Evidence

Manual payment tetap harus dapat memiliki bukti.

Bukti dapat berupa:

- upload file;
- attachment;
- document record.

---

# 71. Payment Receipt

Setiap successful payment harus menghasilkan:

> bukti pembayaran.

Ini berlaku untuk:

- Midtrans;
- manual payment.

---

# 72. Receipt History

Bukti pembayaran yang sudah lampau harus tetap dapat dilihat.

Parent dapat melihat receipt historical dari:

> Riwayat Pembayaran.

---

# 73. Invoice

Setiap bill harus memiliki:

> invoice.

Invoice harus dapat dilihat kembali.

---

# 74. Invoice Generation

Invoice dibuat secara otomatis oleh sistem.

User tidak perlu membuat PDF invoice secara manual setiap kali.

---

# 75. Payment Receipt Generation

Receipt dibuat otomatis setelah:

> payment berhasil tercatat.

---

# 76. Invoice vs Receipt

Invoice:

> dokumen kewajiban/tagihan.

Receipt:

> dokumen bahwa pembayaran telah diterima.

Jangan menukar kedua fungsi tersebut.

---

# 77. Parent Documents

Parent dapat melihat:

```text
Invoice
Receipt
```

melalui web.

---

# 78. Email Documents

Dokumen tertentu juga dikirim melalui email.

Dokumen yang dikirim melalui email:

```text
Invoice
Receipt
Invoice pada overdue reminder
```

---

# 79. Bill Available Email

⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

Requirement yang telah disepakati:

> ketika tagihan baru tersedia, parent mendapatkan email.

Untuk prototype:

```text
New Bill
↓
Email Parent
```

Namun dokumen invoice tidak wajib dilampirkan pada email "tagihan tersedia".

---

# 80. H-2 Reminder

Sistem harus mengirim reminder:

> H-2 sebelum due date.

Contoh:

```text
Due Date = 10 August
Reminder = 8 August
```

---

# 81. H-2 Reminder Document

Email reminder H-2 harus:

> menyertakan invoice.

---

# 82. Overdue Email

Jika bill menjadi overdue:

> parent menerima email.

Email harus menjelaskan:

> pembayaran telah terlambat.

Email juga harus menyertakan:

> invoice sebelumnya.

---

# 83. Successful Payment Email

Ketika payment berhasil:

> parent menerima email.

Email menyertakan:

> receipt/bukti pembayaran.

---

# 84. Failed Payment Email

Untuk prototype:

> **tidak mengirim email payment failed kepada parent.**

---

# 85. Internal Email Failure Notification

Jika sistem gagal mengirim email:

> notifikasi diberikan kepada Admin/Super Admin melalui web.

Tidak perlu mengirim notifikasi failure kepada parent.

---

# 86. Email Resend

Admin dan Super Admin dapat:

> mengirim ulang email.

Contoh:

```text
Invoice
Receipt
Overdue Reminder
```

sesuai jenis email yang gagal atau perlu dikirim ulang.

---

# 87. Resend Safety

Resend tidak boleh:

- membuat bill baru;
- membuat payment baru;
- membuat invoice baru;
- membuat receipt baru.

Resend hanya mengirim ulang dokumen/entity existing.

---

# 88. Email History

System harus menyimpan:

- recipient;
- email type;
- related entity;
- status;
- sent time;
- failure reason;
- attempts.

---

# 89. Email Status

Minimal:

```text
pending
sent
failed
```

---

# 90. Email Failure

Jika email gagal:

```text
EmailLog.status = failed
```

dan:

```text
Internal Notification
```

dibuat untuk Admin/Super Admin.

---

# 91. Password Reset

Prototype menggunakan:

> email password reset.

User memasukkan email:

```text
Email
↓
Reset Password
↓
Email Reset Link
↓
Set New Password
```

---

# 92. Password Reset Scope

Password reset tersedia untuk:

- Parent;
- Admin;
- Super Admin;

melalui authentication system.

---

# 93. Authentication Rule

Password tidak boleh disimpan plaintext.

Password harus menggunakan Laravel authentication/password hashing.

---

# 94. Parent Data Isolation

Parent A tidak boleh melihat:

> Student Parent B.

Parent A tidak boleh melihat:

- bill Parent B;
- payment Parent B;
- invoice Parent B;
- receipt Parent B.

---

# 95. Admin Data Access

Admin dapat mengakses operational data yang diperlukan untuk pekerjaannya.

Namun Admin tidak dapat:

> mengubah configuration yang hanya dimiliki Super Admin.

---

# 96. Super Admin Data Access

Super Admin dapat mengakses seluruh operational data yang tersedia untuk Admin.

Super Admin juga dapat:

- configuration;
- pricing;
- due date;
- academic year;
- audit.

---

# 97. Authorization Must Be Server-Side

UI hiding bukan authorization.

Contoh:

```text
Hide "Edit Price"
```

tidak cukup.

Backend harus tetap memeriksa:

```text
user.role === super_admin
```

---

# 98. Never Trust Client-Side Role

Jangan mengandalkan:

```text
hidden button
disabled field
frontend condition
```

sebagai security boundary.

Authorization harus divalidasi di backend.

---

# 99. Financial Action Authorization

Setiap action berikut harus server-authorized:

- create payment;
- update payment;
- create bill;
- update bill;
- change price;
- change due date;
- start academic year;
- generate report;
- resend email.

---

# 100. Audit Rule

Action penting harus dapat diaudit.

Minimal:

- siapa;
- kapan;
- apa yang dilakukan;
- entity yang berubah;
- old value;
- new value jika applicable.

---

# 101. Audit Scope

Minimal audit:

```text
Student create/update
Parent create/update
Payment Type create/update
Price change
Student Price Override
Due Date change
Bill manual creation
Manual Payment
Academic Year transition
Important configuration changes
```

---

# 102. Audit Immutability

Audit record:

> tidak boleh diedit oleh Admin/Super Admin melalui UI.

Jika ada koreksi, buat audit entry baru.

---

# 103. Financial Record Deletion

Successful payment:

> tidak boleh dihapus secara normal.

Bill yang memiliki payment:

> tidak boleh dihapus secara destructive.

Gunakan correction/void workflow jika diperlukan di masa depan.

---

# 104. No Retroactive Financial Mutation

Master data tidak boleh mengubah historical financial transaction.

Contoh:

```text
Change SPP price
```

tidak boleh mengubah:

```text
old bill amount
old payment amount
old receipt
old invoice
```

---

# 105. Academic Transition Safety

Sebelum memulai tahun ajaran baru, sistem harus:

- meminta confirmation;
- menjelaskan impact;
- memastikan hanya Super Admin;
- mencegah duplicate transition;
- menyimpan histori.

---

# 106. Academic Transition Idempotency

Jika Super Admin menekan:

> Mulai Tahun Ajaran Baru

dua kali:

> tidak boleh membuat dua academic year aktif atau menaikkan kelas dua kali.

---

# 107. Billing Job Idempotency

Automatic billing juga harus idempotent.

Jika scheduler berjalan dua kali:

> jangan membuat duplicate bill.

---

# 108. Scheduler Rule

Automation harus dapat dijalankan berulang tanpa menyebabkan duplicate data.

Contoh:

```text
Generate SPP
```

dapat dipanggil berkali-kali tetapi hanya menghasilkan:

> satu bill untuk student/payment type/period yang sama.

---

# 109. Due Reminder Idempotency

H-2 reminder tidak boleh dikirim berkali-kali hanya karena scheduler dijalankan ulang.

System harus mengecek:

> apakah reminder untuk bill tersebut sudah dikirim.

---

# 110. Overdue Email Idempotency

Overdue notification juga tidak boleh dikirim berkali-kali tanpa alasan.

System harus menyimpan:

> email log / event marker.

---

# 111. Receipt Idempotency

Payment success tidak boleh membuat:

```text
Receipt #1
Receipt #2
Receipt #3
```

hanya karena webhook duplicate.

Harus ada satu canonical receipt per payment.

---

# 112. Invoice Idempotency

Bill tidak boleh menghasilkan invoice duplicate tanpa alasan.

Resend menggunakan invoice yang sama.

---

# 113. Report Integrity

Report harus menggunakan data actual dari database.

Jangan membuat:

> total pemasukan

berdasarkan bill amount.

Revenue report harus menggunakan:

> successful payment amount.

---

# 114. Payment Report

Payment report menampilkan transaksi pembayaran.

Filter dapat mencakup:

- tanggal;
- bulan;
- tahun;
- tahun ajaran;
- jenis pembayaran;
- status;
- payment method.

---

# 115. Revenue Report

Revenue report menghitung:

> pembayaran yang berhasil diterima.

Tidak menghitung:

- unpaid bill;
- pending payment;
- failed payment.

---

# 116. Outstanding Report

Outstanding report menampilkan:

> bill yang masih memiliki outstanding amount.

Dapat dibedakan:

```text
Belum Bayar
Terlambat
```

---

# 117. Report Period

Super Admin dapat menentukan periode.

Minimal:

```text
Tanggal
Bulan
Tahun
Tahun Ajaran
```

---

# 118. Report Format

Setiap report harus dapat di-generate dalam:

```text
Excel
PDF
```

---

# 119. Report Types

Minimal:

```text
Laporan Pembayaran
Laporan Pemasukan
Laporan Tunggakan
```

---

# 120. Report Authorization

Report administrative hanya dapat diakses oleh:

- Super Admin;
- Admin jika capability diberikan.

Parent tidak dapat mengakses laporan global pondok.

---

# 121. Parent Financial Summary

Parent hanya dapat melihat:

> summary financial anaknya.

Tidak dapat melihat:

> total pemasukan pondok.

---

# 122. Data Consistency

Data berikut harus konsisten:

```text
Bill Amount
=
Total expected payment

Paid Amount
=
Total successful payments

Outstanding
=
Bill Amount - Paid Amount
```

---

# 123. Payment Reconciliation

Untuk Midtrans:

```text
Midtrans Transaction
↓
Gateway Reference
↓
Internal Payment
↓
Bill
```

Harus dapat ditelusuri dua arah.

---

# 124. Manual Payment Reconciliation

Manual payment:

```text
Manual Payment
↓
Recorded By
↓
Bill
↓
Student
↓
Parent
```

harus dapat ditelusuri.

---

# 125. Money Unit

Semua nominal menggunakan:

> Rupiah.

Database menyimpan integer.

UI menggunakan:

> format Rupiah.

---

# 126. No Floating Point Money

Jangan menggunakan:

```text
float
double
```

untuk financial amount.

---

# 127. Date Rule

Business date harus menggunakan timezone aplikasi yang konsisten.

Due date tidak boleh berubah hanya karena timezone browser user.

---

# 128. Current Date Rule

Status overdue harus berdasarkan:

> server/application date.

Bukan tanggal yang dikirim client.

---

# 129. Parent Email Requirement

Parent harus memiliki email yang valid untuk:

- notification;
- invoice;
- receipt;
- password reset.

Jika email kosong/tidak valid:

> sistem harus memberi warning kepada Admin/Super Admin.

---

# 130. Missing Parent Email

Bill tetap dapat dibuat jika business process mengizinkan.

Namun email automation harus menghasilkan:

```text
failed / skipped
```

dan internal notification jika perlu.

Jangan membuat payment gagal hanya karena email gagal.

---

# 131. Email Failure Does Not Equal Payment Failure

Ini merupakan rule penting.

Jika:

```text
Payment = SUCCESS
Email = FAILED
```

maka:

```text
Payment remains SUCCESS.
```

Bukan:

```text
Payment = FAILED
```

---

# 132. Document Failure Does Not Equal Payment Failure

Jika receipt PDF gagal dibuat:

> payment tidak boleh diam-diam diubah menjadi failed.

System harus membuat internal error state dan memungkinkan retry generation.

---

# 133. Parent Payment Completion

Setelah Midtrans confirmed successful:

```text
Payment
↓
Paid
↓
Bill recalculated
↓
Receipt generated
↓
Email receipt queued
```

---

# 134. Bill Completion

Bill menjadi:

```text
paid
```

ketika:

```text total successful payments >= bill amount
```

dengan asumsi overpayment tidak diizinkan.

---

# 135. Partial Bill Completion

Jika:

```text
0 < paid < amount
```

maka bill belum lunas.

Status:

```text
unpaid
```

atau:

```text
overdue
```

berdasarkan due date.

---

# 136. Manual Payment and Receipt

Manual payment yang berhasil dicatat harus:

1. membuat payment;
2. update bill state;
3. generate receipt;
4. log activity;
5. queue email receipt jika email tersedia.

---

# 137. Manual Payment Confirmation

Sebelum final submit:

system harus menampilkan:

- student;
- bill;
- amount;
- payment method;
- date.

Operator harus dapat membatalkan sebelum final submit.

---

# 138. No Duplicate Manual Payment

UI harus mencegah accidental double submission.

Backend juga harus memiliki protection yang sesuai.

---

# 139. Unsaved Form Rule

Jika user memiliki perubahan yang belum disimpan:

```text
Close
Escape
Click outside
Navigate away
```

harus diperlakukan sebagai:

> potential data loss.

---

# 140. Unsaved Confirmation

Pesan harus menjelaskan:

> perubahan belum disimpan dan akan hilang.

Action:

```text
Tetap Mengedit
Tutup
```

---

# 141. Loading Rule

Setiap operation asynchronous harus memiliki state:

```text
idle
loading
success
error
```

---

# 142. Double Click Protection

Financial action harus:

> disable ketika processing.

Contoh:

```text
[ Catat Pembayaran ]
```

menjadi:

```text
[ Menyimpan... ]
```

---

# 143. Error Rule

Error user-facing harus:

- Bahasa Indonesia;
- jelas;
- actionable;
- tidak menampilkan stack trace.

---

# 144. Technical Error Logging

Technical error seperti:

```text
SQLSTATE
Stack trace
Exception
API payload
```

disimpan di server/logging.

Tidak ditampilkan mentah kepada user.

---

# 145. Destructive Action Confirmation

Confirmation wajib untuk:

- delete;
- deactivate;
- price change;
- due date change jika impact besar;
- academic year transition;
- financial correction.

---

# 146. Configuration Change Warning

Saat Super Admin mengubah harga:

UI harus menjelaskan:

> perubahan berlaku untuk tagihan baru, bukan tagihan lama.

---

# 147. Due Date Change Warning

Saat due date diubah:

UI harus menjelaskan scope:

```text
Semua tagihan baru
```

atau:

```text
Santri tertentu
```

Jangan membuat user tidak tahu apakah tagihan lama ikut berubah.

---

# 148. Existing Bill Protection

Perubahan configuration:

- price;
- due date;
- payment type;

tidak boleh mengubah historical bill kecuali explicit correction workflow dibuat.

---

# 149. Parent Data Editing

Admin/Super Admin dapat memperbarui:

- nama;
- email;
- phone;
- address.

Perubahan contact data tidak boleh mengubah historical invoice/receipt recipient information yang sudah generated jika document sudah final.

---

# 150. Student Data Editing

Admin/Super Admin dapat memperbarui:

- nama;
- NIS jika business rule mengizinkan;
- class;
- rombel;
- parent;
- status.

Namun perubahan akademik tidak boleh mengubah histori tahun ajaran lama.

---

# 151. Student Transfer

Jika student pindah kelas:

> histori placement lama harus tetap dapat direkonstruksi.

---

# 152. Parent Transfer

Jika student dipindahkan ke parent lain:

> historical bill/payment harus tetap dapat ditelusuri.

Perubahan relationship tidak boleh menghapus financial history.

---

# 153. Financial Ownership History

Bill menyimpan context parent/student ketika bill dibuat.

Ini penting jika relationship berubah di masa depan.

---

# 154. Master Data vs Transaction Data

### Master Data

- Parent;
- Student;
- Payment Type;
- Pricing;
- Academic Year.

### Transaction Data

- Bill;
- Payment;
- Invoice;
- Receipt.

Transaction data harus lebih ketat daripada master data.

---

# 155. Transaction Immutability

Setelah transaction successful:

> jangan melakukan destructive mutation.

Jika correction diperlukan:

> buat mechanism baru.

---

# 156. Prototype Scope Rule

Fitur minor boleh memiliki UI sederhana.

Contoh:

> manual payment.

Namun:

> jangan menghilangkan feature tersebut hanya karena bukan priority utama.

---

# 157. "Untuk Sekarang" Rule

Jika requirement ditandai:

> **UNTUK SEKARANG**

maka:

1. feature tetap dibuat;
2. implementation dibuat cukup fleksibel;
3. requirement diberi status Temporary;
4. client confirmation dicatat;
5. jangan hardcode architecture yang menyulitkan perubahan;
6. jangan menganggap temporary sebagai final business policy.

---

# 158. AI Implementation Rule

AI coding agent **tidak boleh mengubah Temporary Rule menjadi Final Rule secara diam-diam.**

Contoh:

```text
SPP due date = tanggal 1
```

Tidak boleh ditulis dalam architecture:

> "SPP memang selalu jatuh tempo tanggal 1."

Harus:

> "Prototype menggunakan tanggal 1; perlu konfirmasi client."

---

# 159. AI Must Not Invent Business Rules

Jika requirement tidak tersedia:

> jangan mengarang.

Contoh yang tidak boleh diinvent:

- denda keterlambatan;
- diskon;
- beasiswa;
- refund;
- pajak;
- late fee;
- overpayment credit;
- multiple guardian;
- approval workflow tambahan.

---

# 160. Ask Before Major Business Change

Jika AI menemukan bahwa implementasi existing bertentangan dengan business rule:

> jangan langsung merombak.

AI harus:

1. identifikasi conflict;
2. jelaskan dampaknya;
3. usulkan solusi;
4. gunakan solusi minimal;
5. minta confirmation jika perubahan major diperlukan.

---

# 161. Existing Foundation Preservation

Project existing berasal dari pondasi yang sudah dibuat sebelumnya.

Rule:

> Jangan melakukan major rewrite tanpa alasan yang jelas dan positive impact yang terukur.

---

# 162. Refactor Rule

Refactor diperbolehkan jika:

- required oleh business requirement;
- meningkatkan data integrity;
- menghilangkan duplicate logic;
- memperbaiki security;
- memperbaiki maintainability;
- diperlukan untuk feature baru.

---

# 163. No Rewrite for Preference

Jangan rewrite hanya karena:

> "Saya lebih suka framework/library lain."

Existing:

> Filament.

Tetap gunakan Filament kecuali ada alasan teknis yang kuat.

---

# 164. Filament Rule

Filament digunakan sebagai admin application foundation.

Jika ada component default yang tidak sesuai:

> customize.

Jangan mengganti Filament hanya karena default visual tidak disukai.

---

# 165. Filament Customization Priority

Prioritas:

```text
Filament configuration
↓
Theme
↓
Tailwind/CSS
↓
Custom component
↓
Blade customization
↓
Deep framework modification
```

Gunakan level paling sederhana yang menyelesaikan masalah.

---

# 166. No Fragile UI Hacks

Hindari customization yang bergantung pada:

- selector DOM internal yang tidak stabil;
- JavaScript injection berlebihan;
- overriding internal framework behavior tanpa alasan.

---

# 167. Database Rule

Schema harus mengikuti:

> `SCHEMA.md`.

Namun developer harus memeriksa actual existing migrations sebelum melakukan destructive migration.

---

# 168. Architecture Rule

Implementation harus mengikuti:

> `ARCHITECTURE.md`.

Jika terdapat conflict:

> business rules dalam `RULES.md` menentukan behavior.

---

# 169. Design Rule

UI harus mengikuti:

> `DESIGN.md`.

Business rule tidak boleh diubah hanya untuk membuat UI lebih mudah.

---

# 170. PRD Rule

Product requirement utama berasal dari:

> `PRD.md`.

Jika PRD dan implementation existing berbeda:

> existing code tidak otomatis dianggap lebih benar.

---

# 171. Cross-Document Consistency

AI agent harus menjaga consistency:

```text
PRD
 ↓
ARCHITECTURE
 ↓
SCHEMA
 ↓
DESIGN
 ↓
RULES
```

Tidak boleh:

```text
PRD says A
SCHEMA implements B
RULES says C
```

tanpa alasan terdokumentasi.

---

# 172. Financial Source of Truth

Untuk financial transaction:

> Database transaction records adalah source of truth.

UI bukan source of truth.

PDF bukan source of truth.

Email bukan source of truth.

Excel export bukan source of truth.

---

# 173. Midtrans Source Rule

Midtrans merupakan external payment gateway.

Internal database tetap menyimpan:

> canonical payment record.

Webhook digunakan untuk sinkronisasi status.

---

# 174. Excel Rule

Excel report adalah:

> export.

Bukan database.

Admin tidak boleh mengubah Excel lalu menganggap database otomatis berubah.

---

# 175. PDF Rule

PDF:

- invoice;
- receipt;
- report;

merupakan generated document.

Bukan source of truth.

---

# 176. Email Rule

Email merupakan:

> notification channel.

Bukan source of truth.

---

# 177. Audit Rule for External Events

Webhook penting dari Midtrans harus dapat dilacak.

Minimal:

- external reference;
- event/status;
- received timestamp;
- related payment.

---

# 178. External API Failure

Jika Midtrans API gagal:

> jangan membuat payment successful secara manual tanpa explicit manual payment workflow.

---

# 179. Midtrans Timeout

Jika status belum diketahui:

> jangan menganggap failed.

Gunakan:

```text
pending
```

sampai status final diketahui.

---

# 180. Webhook Validation

Webhook harus divalidasi sesuai mekanisme Midtrans yang digunakan.

Jangan menerima arbitrary callback sebagai payment success.

---

# 181. Payment Success Sequence

Recommended:

```text
Midtrans webhook
↓
Validate
↓
Find bill/payment
↓
Check idempotency
↓
Update payment
↓
Recalculate bill
↓
Generate receipt
↓
Create notification/email job
↓
Audit
```

---

# 182. Payment Failure Sequence

```text
Midtrans webhook
↓
Validate
↓
Update payment failed
↓
Do NOT reduce bill outstanding
↓
Do NOT create successful receipt
↓
Do NOT email parent
```

---

# 183. Payment Pending Sequence

```text
Midtrans webhook/status
↓
Update payment pending
↓
Do not finalize financial state
```

---

# 184. Email Queue Rule

Email automation sebaiknya tidak menghambat primary transaction.

Contoh:

```text
Payment success
↓
Payment saved
↓
Response success
↓
Email queued
```

Bukan:

```text
Payment success
↓
Wait for SMTP
↓
If SMTP fails
↓
Payment fails
```

---

# 185. PDF Generation Rule

PDF generation sebaiknya dipisahkan dari critical financial transaction jika memungkinkan.

Payment harus tetap tersimpan walaupun PDF generation mengalami temporary failure.

---

# 186. Retry Rule

Retry diperbolehkan untuk:

- email;
- PDF generation;
- external service request yang aman diulang.

Retry tidak boleh membuat duplicate financial transaction.

---

# 187. Notification Rule

Notification harus membantu user mengetahui:

> apa yang terjadi dan apa yang harus dilakukan.

Contoh:

```text
Email gagal dikirim.
[ Kirim Ulang ]
```

---

# 188. No Notification Spam

Automation tidak boleh membuat notification berulang tanpa perubahan state.

---

# 189. Parent Email Timing Summary

| Event | Parent Email | Document |
|---|---:|---|
| New Bill Available | ✅ | Tidak wajib |
| H-2 Due Reminder | ✅ | Invoice |
| Payment Successful | ✅ | Receipt |
| Bill Overdue | ✅ | Invoice |
| Payment Failed | ❌ | — |

---

# 190. Internal Notification Summary

| Event | Admin | Super Admin |
|---|---:|---:|
| Email Failed | ✅ | ✅ |
| Important system issue | ✅ | ✅ |
| Payment issue | sesuai workflow | sesuai workflow |
| Audit event | tersedia di audit | tersedia di audit |

---

# 191. Report Period Summary

Super Admin dapat memilih:

```text
Per tanggal
Per bulan
Per tahun
Per tahun ajaran
```

---

# 192. Report Export Summary

Setiap report utama:

```text
Laporan Pembayaran
Laporan Pemasukan
Laporan Tunggakan
```

dapat di-export:

```text
Excel
PDF
```

---

# 193. Parent History Rule

Parent dapat melihat histori pembayaran lama.

History tidak dibatasi hanya pada current month.

---

# 194. Invoice History Rule

Invoice historical tetap dapat dilihat selama financial record tersedia.

---

# 195. Receipt History Rule

Receipt historical tetap dapat dilihat selama payment record tersedia.

---

# 196. Data Visibility Rule

Parent:

```text
Own Account
↓
Own Children
↓
Their Bills
↓
Their Payments
↓
Their Documents
```

Tidak boleh keluar dari scope tersebut.

---

# 197. Admin Visibility Rule

Admin:

```text
Operational Pondok Data
```

dengan pengecualian:

> configuration yang hanya Super Admin.

---

# 198. Super Admin Visibility Rule

Super Admin:

> seluruh operational data + administrative configuration.

---

# 199. No Role Escalation

Parent tidak boleh mengubah role menjadi:

```text
admin
super_admin
```

Admin tidak boleh mengubah dirinya menjadi:

```text
super_admin
```

Role management harus dilindungi.

---

# 200. User Creation

Parent account dibuat oleh:

```text
Admin
Super Admin
```

sesuai requirement prototype.

---

# 201. Parent Password

Password parent dapat:

- dibuat melalui account creation workflow;
- di-reset melalui email.

Detail invitation flow belum menjadi requirement utama.

---

# 202. Admin Account

Admin account dikelola secara internal.

Parent tidak dapat membuat Admin.

---

# 203. Super Admin Account

Super Admin merupakan role paling privileged.

Jumlah Super Admin belum ditentukan.

Jangan membuat hardcoded single-super-admin assumption kecuali implementation membutuhkannya.

---

# 204. System Configuration Security

Configuration seperti:

- price;
- due date;
- academic year;

harus protected oleh authorization.

---

# 205. Bulk Operation Safety

Jika nantinya ada bulk operation:

> harus ada preview/confirmation jika impact besar.

Contoh:

```text
Naikkan kelas seluruh santri
```

harus menampilkan impact.

---

# 206. Academic Bulk Operation

Promote students merupakan bulk operation.

Sebelum execution:

```text
Jumlah santri yang akan diproses
```

harus dapat diketahui.

---

# 207. Bulk Operation Failure

Jika sebagian data gagal:

> system tidak boleh diam-diam menyatakan seluruh operation sukses.

Harus ada:

- transaction strategy;
- error report;
- retry/recovery.

---

# 208. Database Transaction Rule

Operation yang mempengaruhi beberapa financial records harus menggunakan database transaction jika diperlukan.

Contoh:

```text
Create Payment
↓
Update Bill
↓
Create Receipt Record
↓
Audit
```

Core state harus konsisten.

---

# 209. Transaction Boundary

External services seperti:

> email/Midtrans/PDF

tidak boleh membuat database transaction menggantung terlalu lama.

---

# 210. Concurrency

Jika dua payment masuk hampir bersamaan:

> system harus menjaga konsistensi bill.

Contoh:

```text
Bill = 500K

Payment A = 500K
Payment B = 500K
```

System harus mencegah double settlement/overpayment berdasarkan business rule.

---

# 211. Race Condition Protection

Financial calculation harus mempertimbangkan:

- database locking;
- transaction;
- unique constraint;
- idempotency.

Approach final mengikuti implementation.

---

# 212. No Client-Side Financial Calculation Authority

Frontend boleh menghitung preview.

Namun:

> backend harus menghitung ulang nominal final.

Jangan percaya nominal yang dikirim browser sebagai authoritative.

---

# 213. Price Resolution Backend

Ketika membuat bill:

```text
Frontend amount
```

tidak boleh menjadi source of truth.

Backend harus:

```text
Resolve price
↓
Create bill
```

---

# 214. Due Date Resolution Backend

Frontend due date juga tidak menjadi source of truth.

Backend harus menentukan:

```text
Resolve due date
↓
Create bill
```

---

# 215. Payment Amount Validation

Backend harus memastikan:

```text
amount > 0
```

dan sesuai outstanding.

---

# 216. Parent Payment Authorization

Ketika parent melakukan payment:

backend harus memastikan:

```text
Bill belongs to Student
AND
Student belongs to Parent
```

sebelum membuat payment session.

---

# 217. Invoice Authorization

Ketika parent meminta invoice:

backend harus memastikan:

```text
Invoice → Bill → Student → Parent
```

benar-benar milik user tersebut.

---

# 218. Receipt Authorization

Ketika parent meminta receipt:

backend harus memastikan:

```text
Receipt → Payment → Bill → Student → Parent
```

sesuai ownership.

---

# 219. Document URL Security

PDF invoice/receipt tidak boleh diberikan melalui URL public yang dapat ditebak.

Jika storage public digunakan:

> access policy harus tetap diperhatikan.

---

# 220. File Upload Rule

Manual payment evidence harus:

- memiliki allowed MIME type;
- memiliki size limit;
- disimpan aman;
- tidak dapat menjalankan executable.

---

# 221. File Name Rule

Nama file user tidak boleh dipercaya sebagai safe filesystem path.

Gunakan generated storage filename.

---

# 222. Report File Security

Generated report hanya dapat diakses oleh user yang memiliki permission.

Parent tidak boleh mengakses global report.

---

# 223. Sensitive Data Logging

Jangan memasukkan:

- password;
- reset token;
- secret;
- sensitive payment credentials;

ke audit log.

---

# 224. Secret Management

Midtrans credentials harus berada di environment configuration.

Jangan hardcode:

```text
server key
client key
password
secret
```

ke source code.

---

# 225. Environment Separation

Minimal:

```text
local
development
production
```

jika deployment membutuhkan.

---

# 226. Prototype Rule

Prototype boleh memiliki simplification.

Tetapi simplification tidak boleh merusak:

- security;
- authorization;
- financial integrity;
- data history.

---

# 227. What "Minor Feature" Means

Feature minor berarti:

> priority implementasinya lebih rendah.

Bukan berarti:

> feature boleh tidak ada.

Contoh:

> Manual payment = minor.

Tetap wajib ada.

---

# 228. Priority Order

Secara product:

### P0

- Authentication;
- Parent;
- Student;
- Payment Type;
- Pricing;
- Bill;
- Payment;
- Midtrans;
- Status;
- Invoice;
- Receipt.

### P1

- Email automation;
- Installment;
- Reporting;
- Academic Year.

### P2

- Manual payment;
- advanced audit;
- advanced UX enhancements.

Namun seluruh requirement prototype tetap harus memiliki implementation path.

---

# 229. No Feature Deletion Without Reason

Jika AI agent merasa feature terlalu kompleks:

> jangan menghapusnya.

AI harus:

1. mempertahankan requirement;
2. menyederhanakan implementation;
3. mencatat limitation;
4. meminta confirmation jika benar-benar impossible.

---

# 230. Existing Code Preservation

Sebelum mengubah existing implementation:

```text
Inspect
↓
Understand
↓
Map
↓
Change minimally
```

Jangan:

```text
Delete
↓
Rewrite
```

tanpa justification.

---

# 231. Existing Midtrans Flow

Existing project sudah memiliki Midtrans integration dan webhook flow.

Perubahan terhadap integration harus dilakukan dengan hati-hati agar:

> existing payment functionality tidak rusak tanpa alasan.

---

# 232. Existing Filament Foundation

Existing project menggunakan Filament.

Filament tetap menjadi foundation prototype.

Jika customization diperlukan:

> lakukan secara incremental.

---

# 233. No Premature Architecture

Jangan menambahkan:

- microservices;
- message broker;
- Kubernetes;
- event sourcing penuh;
- CQRS penuh;

jika belum diperlukan oleh prototype.

---

# 234. Simple First

Implementation harus:

> sesederhana mungkin tetapi cukup untuk requirement.

---

# 235. Maintainability

Code harus mudah dipahami oleh developer berikutnya.

Business logic penting jangan disembunyikan dalam:

- giant controller;
- duplicated closures;
- magic constants;
- hardcoded query.

---

# 236. Business Rule Centralization

Rule seperti:

```text
Bill Status
Price Resolution
Due Date Resolution
Academic Promotion
```

sebaiknya berada di service/domain layer yang jelas.

---

# 237. No Business Logic Duplication

Jangan membuat:

```text
Dashboard calculates overdue one way
Report calculates overdue another way
Parent page calculates overdue another way
```

Gunakan satu business rule.

---

# 238. Status Calculation Consistency

Semua UI harus menggunakan status calculation yang sama.

Jika:

```text
Dashboard = overdue
```

maka:

```text
Bill detail
Report
Parent dashboard
```

harus menggunakan definisi overdue yang sama.

---

# 239. Reporting Consistency

Total revenue dashboard harus konsisten dengan:

> laporan pemasukan pada periode yang sama.

Jika berbeda:

> harus ada documented reason.

---

# 240. Final Rule

Jika ada dua cara melakukan sesuatu:

> pilih cara yang menjaga data paling aman dan paling mudah diaudit.

---

# 241. Final Business Principle

Sistem harus selalu dapat menjawab:

> **Siapa yang membayar?**

> **Untuk siapa pembayaran tersebut?**

> **Membayar apa?**

> **Berapa nominalnya?**

> **Kapan dibayar?**

> **Kapan jatuh tempo?**

> **Sudah lunas atau belum?**

> **Siapa yang mencatat/mengubahnya?**

> **Dokumennya mana?**

> **Apakah email sudah dikirim?**

Jika sistem tidak dapat menjawab pertanyaan tersebut dari data yang tersedia:

> schema, business logic, atau audit trail perlu diperiksa kembali.

---

# 242. Final Temporary Requirement Principle

Semua requirement yang ditandai:

> ⚠️ **TEMPORARY — CLIENT CONFIRMATION REQUIRED**

harus diperlakukan sebagai:

```text
IMPLEMENT NOW
+
DOCUMENT
+
KEEP FLEXIBLE
+
CONFIRM WITH CLIENT LATER
```

**Bukan:**

```text
SKIP
```

---

# 243. Final Rule for AI Coding Agent

AI coding agent harus:

1. membaca `PRD.md`;
2. membaca `ARCHITECTURE.md`;
3. membaca `SCHEMA.md`;
4. membaca `DESIGN.md`;
5. membaca `RULES.md`;
6. membaca `AGENTS.md` jika tersedia;
7. memeriksa existing code;
8. tidak mengarang requirement;
9. tidak melakukan major rewrite tanpa alasan;
10. menjaga existing functionality;
11. menjaga authorization;
12. menjaga financial integrity;
13. menjaga historical data;
14. menandai temporary requirement;
15. bertanya jika menemukan conflict besar.

---

# 244. Absolute Rules

Berikut adalah aturan yang **tidak boleh dilanggar**:

### RULE 001

> Super Admin memiliki seluruh capability Admin.

### RULE 002

> Hanya Super Admin yang menentukan nominal pembayaran.

### RULE 003

> Admin tidak boleh mengubah nominal master pembayaran.

### RULE 004

> Parent hanya dapat melihat data dirinya dan anaknya.

### RULE 005

> Financial history tidak boleh berubah secara retroaktif.

### RULE 006

> Successful payment tidak boleh dihapus secara normal.

### RULE 007

> Duplicate Midtrans webhook tidak boleh menghasilkan duplicate payment.

### RULE 008

> Duplicate billing job tidak boleh menghasilkan duplicate bill.

### RULE 009

> Payment success ditentukan dari verified payment flow, bukan sekadar frontend redirect.

### RULE 010

> Invoice dan receipt adalah dokumen berbeda.

### RULE 011

> Payment receipt dibuat setelah payment berhasil tercatat.

### RULE 012

> H-2 reminder menyertakan invoice.

### RULE 013

> Overdue email menyertakan invoice.

### RULE 014

> Successful payment email menyertakan receipt.

### RULE 015

> Payment failed tidak mengirim email ke parent pada prototype.

### RULE 016

> Email failure dilaporkan kepada Admin/Super Admin melalui web.

### RULE 017

> Resend email tidak membuat transaksi/dokumen baru.

### RULE 018

> Manual payment tetap diaudit.

### RULE 019

> Tahun ajaran lama tidak boleh dihapus ketika tahun ajaran baru dimulai.

### RULE 020

> Temporary requirement tetap wajib diimplementasikan.

---

# 245. Final Status

Dokumen ini menjadi:

> **Business Rule Source of Truth**

untuk implementation prototype.

Jika implementation existing bertentangan dengan rule:

> jangan langsung rewrite.

Lakukan:

```text
Identify Conflict
↓
Assess Impact
↓
Preserve Existing Where Possible
↓
Apply Minimal Change
↓
Document Decision
```

Jika perubahan membutuhkan keputusan client:

> tandai sebagai `CLIENT CONFIRMATION REQUIRED`.

---

# 246. End State

Tujuan akhirnya bukan hanya:

> "sistem bisa mencatat pembayaran."

Tetapi:

> **setiap kewajiban pembayaran memiliki identitas, nominal, periode, jatuh tempo, status, histori pembayaran, dokumen, dan audit trail yang dapat ditelusuri.**

Dengan demikian kepala bendahara dapat menggunakan sistem sebagai alat untuk:

```text
Monitoring
+
Administration
+
Reporting
+
Audit
+
Payment Tracking
```

tanpa kembali bergantung sepenuhnya pada Excel.