# PRD — Sistem Administrasi Pembayaran Pondok

> **Product Requirements Document**
>
> Sistem Administrasi Pembayaran Pondok Pesantren berbasis web.

---

## 1. Document Information

| Item | Detail |
|---|---|
| Nama Produk | Sistem Administrasi Pembayaran Pondok |
| Dokumen | Product Requirements Document |
| Versi | 1.0 — Prototype |
| Status | Draft Requirement / Prototype |
| Fokus Produk | Administrasi pembayaran pondok |
| Target Pengguna | Super Admin, Admin, Orang Tua Santri |
| Backend Existing | Laravel 12 |
| Admin Panel Existing | Filament 5 |
| Payment Gateway Existing | Midtrans |
| Database Existing | MySQL |

---

# 2. Requirement Status Legend

Dokumen ini menggunakan status requirement berikut.

### ✅ CONFIRMED

Requirement yang sudah disepakati dalam proses requirement gathering dan dapat dianggap sebagai requirement produk.

**Wajib diimplementasikan.**

### ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Keputusan sementara yang digunakan untuk membangun prototype karena detail final belum dikonfirmasi kepada client.

**PENTING: Temporary bukan berarti optional.**

Requirement dengan status ini:

- tetap **WAJIB diimplementasikan pada prototype**;
- tidak boleh di-skip hanya karena masih sementara;
- harus ditandai sebagai requirement yang perlu dikonfirmasi kepada client;
- implementasinya sebaiknya dibuat cukup fleksibel agar dapat diubah ketika client memberikan keputusan final.

### 🔶 OPEN REQUIREMENT

Requirement yang belum memiliki keputusan yang cukup untuk ditentukan.

AI/developer **tidak boleh mengarang aturan bisnis** untuk bagian ini.

Jika bagian tersebut belum dibutuhkan untuk prototype, gunakan desain yang fleksibel atau tandai sebagai TODO/confirmation point.

### ❌ OUT OF SCOPE

Fitur yang secara sengaja tidak termasuk dalam prototype.

---

# 3. Executive Summary

Sistem Administrasi Pembayaran Pondok adalah aplikasi web untuk membantu pondok pesantren mengelola administrasi pembayaran santri secara terpusat.

Pondok sebenarnya telah memiliki sistem administrasi sebelumnya, tetapi sistem tersebut saat ini tidak digunakan secara efektif. Operasional administrasi pembayaran masih banyak dilakukan menggunakan Excel.

Kondisi tersebut menyebabkan proses administrasi, monitoring pembayaran, pencatatan pemasukan, dan audit menjadi lebih sulit dilakukan.

Client utama sistem adalah **Kepala Bendahara Pondok**, yang dalam sistem akan memiliki role **Super Admin**.

Sistem ditujukan untuk membantu:

1. Karyawan/admin pondok dalam menjalankan administrasi pembayaran sehari-hari.
2. Kepala Bendahara dalam melakukan monitoring dan audit.
3. Orang tua santri dalam melihat tagihan dan melakukan pembayaran secara mandiri.
4. Pondok dalam mengotomatisasi pembuatan tagihan, invoice, bukti pembayaran, notifikasi, dan laporan.

Fokus prototype adalah **administrasi pembayaran saja**.

Sistem tidak dimaksudkan menjadi sistem manajemen pondok secara keseluruhan pada tahap ini.

---

# 4. Problem Statement

## 4.1 Kondisi Existing

Pondok sudah pernah/memiliki sistem administrasi, tetapi sistem tersebut tidak digunakan secara efektif.

Operasional yang berjalan saat ini masih menggunakan Excel sebagai salah satu media utama pencatatan.

Kondisi ini menimbulkan beberapa kebutuhan:

- Data pembayaran sulit dimonitor secara cepat.
- Bendahara perlu melakukan pengecekan manual.
- Kepala Bendahara membutuhkan cara yang lebih mudah untuk melakukan audit.
- Informasi mengenai siapa yang sudah membayar dan siapa yang belum perlu dapat ditemukan dengan cepat.
- Pemasukan pada periode tertentu perlu dapat dihitung dan direkap dengan mudah.
- Riwayat pembayaran perlu tersedia secara terstruktur.
- Pembayaran manual/offline tetap harus dapat dicatat.
- Proses pembuatan invoice dan bukti pembayaran sebaiknya tidak dilakukan secara manual.
- Orang tua membutuhkan akses langsung untuk melihat tagihan anak dan melakukan pembayaran.

---

# 5. Product Goal

Tujuan utama sistem adalah:

> **Menyediakan sistem administrasi pembayaran pondok yang memudahkan operasional bendahara dan memberikan Kepala Bendahara kemampuan monitoring serta audit pembayaran secara terpusat.**

Secara khusus sistem harus membantu pondok untuk:

- Mengelola data santri.
- Mengelola data orang tua.
- Menghubungkan satu orang tua dengan beberapa santri.
- Mengelola jenis pembayaran.
- Menghasilkan tagihan secara otomatis.
- Mengatur nominal pembayaran.
- Mengatur tanggal jatuh tempo.
- Mendukung pembayaran melalui Midtrans.
- Mendukung pembayaran manual/offline.
- Mendukung pembayaran secara cicilan.
- Menghasilkan invoice.
- Menghasilkan bukti pembayaran.
- Mengirim dokumen melalui email.
- Menampilkan status pembayaran.
- Menyediakan histori pembayaran.
- Menyediakan laporan pembayaran.
- Menyediakan laporan pemasukan.
- Menyediakan laporan tunggakan.
- Menyediakan audit log.
- Menyediakan notifikasi internal.
- Memudahkan proses audit oleh Super Admin.

---

# 6. Product Scope

## 6.1 In Scope

Prototype mencakup:

### A. Authentication & Account Management

- Login.
- Logout.
- Reset password.
- Manajemen akun orang tua.
- Manajemen akun Admin.
- Manajemen akun Super Admin.

### B. Santri Management

- Data santri.
- Status santri.
- Kelas.
- Rombel.
- Hubungan santri dengan orang tua.
- Override nominal pembayaran per santri.

### C. Parent/User Management

- Data orang tua.
- Akun orang tua.
- Hubungan orang tua dengan banyak santri.
- Akses orang tua terhadap data anak-anaknya.

### D. Payment Type Management

- Jenis pembayaran.
- Nominal pembayaran.
- Nominal berdasarkan kelompok santri.
- Override nominal per santri.
- Konfigurasi pembayaran.
- Konfigurasi jatuh tempo.

### E. Billing

- Pembuatan tagihan otomatis.
- Tagihan bulanan SPP.
- Tagihan jenis pembayaran lain.
- Jatuh tempo.
- Status pembayaran.
- Cicilan.
- Sisa pembayaran.

### F. Payment

- Midtrans.
- Pembayaran manual/offline.
- Riwayat transaksi.
- Bukti pembayaran.
- Pencatatan operator pembayaran manual.

### G. Invoice & Document

- Invoice PDF.
- Bukti pembayaran PDF.
- Penyimpanan dokumen.
- Akses dokumen dari web.
- Pengiriman dokumen melalui email.

### H. Notification

- Email tagihan baru.
- Email pengingat H-2.
- Email bukti pembayaran.
- Email keterlambatan.
- Notifikasi internal.
- Monitoring email gagal.
- Resend email.

### I. Reporting

- Laporan pembayaran.
- Laporan pemasukan.
- Laporan tunggakan.
- Export Excel.
- Export PDF.
- Filtering laporan.

### J. Audit

- Audit log.
- Pencatatan aktivitas Admin.
- Pencatatan aktivitas Super Admin.
- Tampilan audit log untuk Super Admin.

### K. Academic Period

- Tahun ajaran.
- Tahun ajaran aktif.
- Mulai tahun ajaran baru.
- Perpindahan tahun ajaran.
- Kenaikan kelas otomatis untuk prototype.

---

# 7. Out of Scope

❌ Fitur berikut tidak termasuk dalam prototype:

- Sistem akademik lengkap.
- Nilai/rapor.
- Absensi.
- Manajemen kamar/asrama.
- Manajemen inventaris.
- Manajemen kepegawaian.
- Payroll.
- Penggajian karyawan.
- Surat-menyurat pondok.
- Manajemen kurikulum.
- Manajemen kegiatan pondok di luar kebutuhan pembayaran.
- Sistem informasi akademik secara keseluruhan.
- Modul non-keuangan yang tidak berhubungan langsung dengan administrasi pembayaran.

Jika fitur baru diperlukan di kemudian hari, fitur tersebut harus diperlakukan sebagai scope baru dan tidak boleh ditambahkan secara sembarangan ke prototype.

---

# 8. User Roles

Sistem memiliki tiga role utama.

## 8.1 Super Admin

Super Admin adalah **Kepala Bendahara Pondok**.

Super Admin merupakan role dengan hak akses tertinggi dalam sistem.

Super Admin memiliki:

> **Seluruh kemampuan Admin + hak khusus Super Admin.**

Super Admin dapat:

- Mengelola data santri.
- Mengelola data orang tua.
- Mengelola akun orang tua.
- Mengelola pembayaran.
- Mengelola tagihan.
- Melihat laporan.
- Mengelola jenis pembayaran.
- Menentukan nominal pembayaran.
- Mengatur nominal berdasarkan kelompok.
- Melakukan override nominal per santri.
- Mengatur jatuh tempo.
- Mengelola tahun ajaran.
- Memulai tahun ajaran baru.
- Melihat audit log.
- Melihat notifikasi internal.
- Mengelola konfigurasi yang memang menjadi kewenangan Super Admin.

### Hak khusus Super Admin

Hanya Super Admin yang dapat:

- Menambah jenis pembayaran.
- Mengubah konfigurasi jenis pembayaran yang bersifat restricted.
- Menentukan nominal pembayaran.
- Melakukan perubahan nominal berdasarkan kelompok.
- Melakukan override nominal individual.
- Melakukan konfigurasi tertentu yang secara eksplisit dibatasi untuk Super Admin.
- Mengakses Audit Log.

---

# 9. Admin

Admin merupakan pengguna internal pondok yang menjalankan operasional administrasi sehari-hari.

Admin dapat:

- Melihat data santri.
- Menambahkan santri.
- Mengubah data santri.
- Melihat data orang tua.
- Menambahkan akun orang tua.
- Mengubah data orang tua.
- Mengelola akun orang tua.
- Melihat tagihan.
- Melihat pembayaran.
- Mencatat pembayaran manual/offline.
- Melihat laporan.
- Mengirim ulang email yang gagal.
- Menjalankan operasional administrasi pembayaran.

Admin tidak dapat:

- Menambahkan jenis pembayaran.
- Mengubah nominal pembayaran yang menjadi kewenangan Super Admin.
- Mengakses Audit Log.

> **Catatan:** Super Admin memiliki seluruh kemampuan Admin.

---

# 10. User / Orang Tua

User adalah **orang tua/wali santri**, bukan santri.

Satu akun User dapat memiliki lebih dari satu santri.

Contoh:

```text
Orang Tua
├── Santri A
├── Santri B
└── Santri C
```

Orang tua tidak melakukan registrasi sendiri.

Akun orang tua dibuat oleh:

- Admin; atau
- Super Admin.

User dapat:

- Login.
- Melihat dashboard.
- Melihat seluruh anak yang terhubung.
- Memilih anak tertentu.
- Melihat tagihan anak.
- Melihat status pembayaran.
- Membayar melalui Midtrans.
- Melihat histori pembayaran.
- Melihat invoice.
- Melihat bukti pembayaran.
- Mengakses dokumen pembayaran lama.
- Melakukan reset password melalui email.

User tidak dapat:

- Melihat data santri lain.
- Melihat data orang tua lain.
- Melihat laporan internal.
- Mengubah jenis pembayaran.
- Mengubah nominal.
- Mengubah tagihan.
- Mengakses audit log.
- Mengakses data administratif internal pondok.

---

# 11. Parent–Student Relationship

Satu orang tua dapat memiliki beberapa santri.

Contoh:

```text
Bapak Ahmad
├── Ahmad Fauzan
├── Budi Ahmad
└── Citra Ahmad
```

Satu akun orang tua digunakan untuk mengakses seluruh anak yang terhubung.

## 11.1 Flexible Linking

Untuk prototype, hubungan orang tua dan santri harus dapat dibuat dalam dua arah workflow:

### Workflow A

Orang tua dibuat terlebih dahulu:

```text
Buat Orang Tua
↓
Buat Santri
↓
Hubungkan Santri ke Orang Tua
```

### Workflow B

Santri dibuat terlebih dahulu:

```text
Buat Santri
↓
Buat/Pilih Orang Tua
↓
Hubungkan Santri ke Orang Tua
```

Santri dapat sementara berada dalam kondisi belum memiliki akun orang tua dan dapat dihubungkan kemudian.

---

# 12. Student Data

Data santri minimal mencakup:

- ID/NIS Santri.
- Nama lengkap.
- Jenis kelamin.
- Kelas.
- Rombel.
- Tahun masuk.
- Status santri.
- Email.
- Orang tua/wali.
- Nomor HP orang tua.
- Alamat.

## 12.1 Student Status

Status santri:

1. Aktif.
2. Lulus.
3. Keluar.
4. Nonaktif.

Hanya santri dengan status **Aktif** yang secara default menjadi target pembuatan tagihan SPP otomatis.

---

# 13. Class & Rombel

Untuk prototype digunakan struktur:

### Kelas

- Kelas 1
- Kelas 2
- Kelas 3
- Kelas 4
- Kelas 5
- Kelas 6
- Kelas 7
- Kelas 8
- Kelas 9

### Rombel

Setiap kelas memiliki:

- A
- B

Sehingga contoh kelompok:

```text
1-A
1-B
2-A
2-B
...
9-A
9-B
```

Total prototype:

> **18 kelompok kelas/rombel.**

---

# 14. Academic Year

Sistem menggunakan konsep **Tahun Ajaran**.

Contoh:

```text
2026/2027
2027/2028
2028/2029
```

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Konsep Tahun Ajaran digunakan untuk prototype karena mekanisme administrasi pondok sebenarnya masih perlu dikonfirmasi kepada client.

**Requirement ini tetap WAJIB diimplementasikan.**

Sistem harus memiliki:

- Tahun Ajaran.
- Status Tahun Ajaran Aktif.
- Histori Tahun Ajaran.
- Aksi **Mulai Tahun Ajaran Baru**.

## 14.1 Mulai Tahun Ajaran Baru

Super Admin melakukan aksi:

> **Mulai Tahun Ajaran Baru**

Aksi ini **BUKAN reset database**.

Data tahun ajaran lama harus tetap tersedia.

Contoh:

```text
2026/2027
├── Tagihan
├── Pembayaran
├── Santri
└── Laporan

        ↓ Mulai Tahun Ajaran Baru

2027/2028
├── Tagihan baru
├── Pembayaran baru
├── Santri
└── Laporan
```

Data historis tetap dapat diakses.

---

# 15. Automatic Class Promotion

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Untuk prototype, ketika Super Admin memulai tahun ajaran baru:

> Semua santri otomatis naik satu tingkat kelas.

Contoh:

```text
2026/2027
Ahmad → 5-A

        ↓

2027/2028
Ahmad → 6-A
```

Rombel A/B tetap dipertahankan.

### Catatan

Aturan ini masih perlu dikonfirmasi kepada client.

Khusus santri kelas 9, mekanisme setelah tahun ajaran baru masih perlu dikonfirmasi.

Prototype **tidak boleh mengabaikan masalah kelas 9** hanya karena mekanismenya belum final.

---

# 16. Payment Types

Sistem tidak boleh mengunci jenis pembayaran hanya pada SPP.

Super Admin harus dapat menambahkan jenis pembayaran baru.

Contoh:

```text
SPP
Uang Gedung
Uang Makan
Kitab
Seragam
Kegiatan
```

Jenis pembayaran baru harus dapat ditambahkan tanpa perubahan source code utama.

## 16.1 Initial Payment Type

Untuk prototype awal:

> **SPP Bulanan**

merupakan jenis pembayaran utama.

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Jenis pembayaran lain harus tetap didukung oleh sistem karena Super Admin akan membutuhkan kemampuan untuk menambahkannya.

---

# 17. Payment Type Permission

Hanya Super Admin yang dapat:

- Menambah jenis pembayaran.
- Mengatur nominal.
- Mengubah nominal.
- Mengatur konfigurasi pembayaran yang restricted.

Admin hanya dapat menjalankan operasional pembayaran dan administrasi yang diizinkan.

---

# 18. Payment Nominal

Nominal pembayaran ditentukan oleh Super Admin.

Nominal dapat ditentukan berdasarkan kelompok santri.

Contoh:

```text
SPP

Kelas 5-A → Rp500.000
Kelas 5-B → Rp550.000
Kelas 6-A → Rp600.000
Kelas 6-B → Rp600.000
```

Nominal tidak harus sama untuk seluruh santri.

## 18.1 Individual Override

Jika satu santri memiliki nominal khusus, Super Admin dapat melakukan override dari halaman data santri.

Contoh:

```text
SPP
Kelas 5-A → Rp500.000

Ahmad Fauzan
Override → Rp300.000
```

Maka nominal Ahmad adalah Rp300.000 sementara santri lain di 5-A tetap Rp500.000.

---

# 19. Payment Due Date

Sistem harus mendukung pengaturan tanggal jatuh tempo.

## 19.1 SPP

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Untuk prototype:

> SPP dibuat/diperbarui setiap tanggal 1 dan memiliki jatuh tempo tanggal 1.

Keputusan ini wajib diimplementasikan untuk prototype, tetapi harus dikonfirmasi kepada client.

## 19.2 Other Payment Types

Jenis pembayaran lain harus dapat memiliki tanggal jatuh tempo yang dapat diatur.

Admin dan Super Admin dapat menjalankan pengaturan jatuh tempo sesuai hak akses yang diberikan.

## 19.3 Super Admin Override

Super Admin dapat melakukan perubahan jatuh tempo:

- Per jenis pembayaran.
- Per santri.

Contoh:

```text
Default Uang Gedung
→ Jatuh tempo tanggal 15

Santri Ahmad
→ Override jatuh tempo tanggal 20
```

Tanggal jatuh tempo efektif harus digunakan sebagai dasar:

- status terlambat;
- pengingat H-2;
- invoice;
- monitoring pembayaran.

---

# 20. Billing Generation

Tagihan harus dibuat oleh sistem secara otomatis.

## 20.1 SPP

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Untuk prototype:

> Sistem membuat tagihan SPP otomatis setiap awal bulan/tanggal 1.

Tagihan dibuat untuk santri aktif yang memenuhi konfigurasi pembayaran.

Requirement ini **WAJIB diimplementasikan**.

Mekanisme final pembuatan tagihan berdasarkan aturan pondok tetap harus dikonfirmasi.

## 20.2 Other Payment Types

Jenis pembayaran lain harus dapat dibuat/dikonfigurasi tanpa mengubah source code inti.

Tagihan manual bukan jalur utama.

Fitur pembuatan tagihan manual, apabila disediakan, merupakan fitur minor.

---

# 21. Payment Status

Status utama tagihan:

### 🟡 Belum Bayar

Tagihan belum dibayar.

### 🟢 Lunas

Total kewajiban sudah terpenuhi.

### 🔴 Terlambat

Tanggal jatuh tempo telah lewat dan kewajiban belum lunas.

Warna status harus konsisten:

| Status | Warna |
|---|---|
| Belum Bayar | Kuning |
| Lunas | Hijau |
| Terlambat | Merah |

---

# 22. Installment / Cicilan

Sistem harus mendukung pembayaran cicilan.

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Untuk prototype:

> Semua jenis pembayaran secara teknis harus dapat mengakomodasi cicilan.

Namun, jenis pembayaran mana yang benar-benar diperbolehkan dicicil masih perlu dikonfirmasi kepada client.

Requirement ini **tidak boleh di-skip**.

## 22.1 Example

Tagihan:

```text
Rp1.000.000
```

Pembayaran:

```text
Cicilan 1 → Rp400.000
Cicilan 2 → Rp300.000
Cicilan 3 → Rp300.000
```

Total:

```text
Rp1.000.000
```

Status:

```text
Lunas
```

## 22.2 Payment History

Setiap cicilan harus menjadi transaksi yang dapat ditelusuri.

History:

```text
Cicilan #1 → Rp400.000
Cicilan #2 → Rp300.000
Cicilan #3 → Rp300.000
```

## 22.3 Status dan Cicilan

Status utama tetap:

- Belum Bayar
- Lunas
- Terlambat

Kondisi cicilan direpresentasikan melalui:

- Total tagihan.
- Total pembayaran.
- Sisa tagihan.
- Progress pembayaran.

Contoh:

```text
Tagihan     Rp1.000.000
Dibayar     Rp400.000
Sisa        Rp600.000
Progress    40%
Status      Belum Bayar
```

Jika melewati jatuh tempo:

```text
Tagihan     Rp1.000.000
Dibayar     Rp400.000
Sisa        Rp600.000
Progress    40%
Status      Terlambat
```

---

# 23. Payment Methods

Sistem memiliki dua jalur pembayaran.

## 23.1 Online — Midtrans

Midtrans merupakan jalur pembayaran online utama.

Alur:

```text
User melihat tagihan
↓
Bayar Sekarang
↓
Midtrans
↓
Pembayaran
↓
Midtrans callback/webhook
↓
Sistem memproses status
↓
Pembayaran tercatat
↓
Bukti pembayaran
```

## 23.2 Manual / Offline

Admin dan Super Admin dapat mencatat pembayaran manual.

Contoh:

```text
Santri: Ahmad
Tagihan: SPP Agustus
Nominal: Rp500.000
Metode: Manual
Tanggal: 5 Agustus 2026
Dicatat oleh: Admin
```

Pembayaran manual:

- mengubah kondisi pembayaran sesuai nominal yang diterima;
- masuk ke histori;
- masuk ke laporan;
- masuk ke audit;
- memiliki pencatat/operator.

Fitur manual merupakan fitur **minor**, tetapi **WAJIB tersedia**.

---

# 24. Manual Payment Evidence

Pembayaran manual harus dapat memiliki bukti.

Admin/Super Admin dapat menyimpan/upload bukti pembayaran manual, misalnya:

- foto bukti transfer;
- scan bukti pembayaran;
- dokumen pendukung lainnya.

Bukti tersebut harus terhubung dengan transaksi pembayaran terkait.

---

# 25. Parent Payment Flow

Alur pembayaran orang tua:

```text
Login
↓
Dashboard
↓
Lihat semua anak
↓
Pilih anak
↓
Lihat tagihan
↓
Pilih tagihan
↓
Bayar Sekarang
↓
Midtrans
↓
Pembayaran
↓
Webhook
↓
Status pembayaran diperbarui
↓
Bukti pembayaran dibuat
↓
Email bukti pembayaran
↓
Bukti tersedia di history
```

---

# 26. Payment History

User harus dapat melihat histori pembayaran.

Contoh:

```text
SPP Januari
Lunas
Rp500.000

SPP Februari
Lunas
Rp500.000

SPP Maret
Terlambat
Rp500.000

SPP April
Lunas
Rp500.000
```

Histori harus dapat digunakan untuk mengakses dokumen pembayaran lama.

---

# 27. Invoice

Invoice merupakan dokumen tagihan.

Invoice harus:

- dibuat otomatis;
- memiliki informasi tagihan;
- memiliki nominal;
- memiliki periode;
- memiliki tanggal jatuh tempo;
- terhubung dengan santri;
- terhubung dengan orang tua;
- tersedia di web;
- dapat dikirim melalui email.

---

# 28. Payment Receipt

Bukti pembayaran merupakan dokumen yang menunjukkan pembayaran telah tercatat.

Bukti pembayaran harus:

- dibuat otomatis setelah pembayaran berhasil;
- terhubung dengan transaksi pembayaran;
- dapat diakses melalui web;
- dikirim melalui email;
- tetap dapat dilihat pada histori pembayaran lama.

---

# 29. Document Availability

Invoice dan bukti pembayaran tersedia pada dua tempat:

### Web

User dapat membuka/mengakses dokumen melalui sistem.

### Email

Dokumen dikirim kepada email orang tua sesuai trigger.

Dokumen historis tetap dapat diakses.

---

# 30. Automated Email Notifications

Sistem memiliki empat trigger email utama.

## 30.1 Tagihan Baru

Ketika tagihan baru tersedia:

> Sistem mengirim email kepada orang tua.

Tidak ada dokumen yang wajib dilampirkan pada email ini.

## 30.2 H-2 Jatuh Tempo

Dua hari sebelum tanggal jatuh tempo:

> Sistem mengirim reminder.

Email berisi:

- informasi tagihan;
- pengingat jatuh tempo;
- **Invoice PDF**.

Berlaku untuk setiap jenis pembayaran yang memiliki jatuh tempo.

## 30.3 Pembayaran Berhasil

Ketika pembayaran berhasil tercatat:

> Sistem mengirim email kepada orang tua.

Email menyertakan:

> **Bukti Pembayaran PDF**

## 30.4 Tagihan Terlambat

Ketika tagihan memasuki kondisi terlambat:

> Sistem mengirim email kepada orang tua.

Email berisi:

- pemberitahuan bahwa pembayaran telah terlambat;
- **Invoice PDF yang sebelumnya telah dibuat**.

Sistem tidak perlu membuat invoice baru hanya karena status menjadi terlambat.

## 30.5 Payment Failed

Untuk prototype:

> Pembayaran gagal tidak mengirim email kepada orang tua.

---

# 31. Email Failure Handling

Email otomatis harus memiliki status pengiriman.

Status minimal:

- Pending.
- Sent.
- Failed.

Jika email gagal dikirim:

```text
Email gagal
↓
Status Failed
↓
Notifikasi internal
↓
Admin / Super Admin
```

Orang tua tidak menerima email tambahan tentang kegagalan tersebut.

---

# 32. Internal Notification

Notifikasi internal ditampilkan di dalam web.

Tidak perlu mengirim notifikasi kegagalan email melalui email.

Contoh:

```text
🔔 3 Notifikasi

Invoice Ahmad gagal dikirim.
Bukti pembayaran Budi gagal dikirim.
Invoice Citra gagal dikirim.
```

Notifikasi dapat dilihat oleh:

- Admin.
- Super Admin.

---

# 33. Email Resend

Admin dan Super Admin dapat mengirim ulang email yang gagal.

Contoh:

```text
Email gagal
↓
Admin memperbaiki email orang tua
↓
Klik "Kirim Ulang"
↓
Sistem mengirim kembali dokumen
```

Resend tidak membuat transaksi pembayaran baru dan tidak membuat invoice/bukti pembayaran baru apabila dokumen yang sebelumnya masih valid.

---

# 34. Password & Authentication

Semua role harus memiliki mekanisme reset password.

Untuk prototype, reset password dilakukan melalui mekanisme email reset password.

Role yang mendukung:

- User.
- Admin.
- Super Admin.

---

# 35. Reports

Sistem memiliki tiga laporan utama.

## 35.1 Laporan Pembayaran

Menampilkan transaksi pembayaran.

Dapat digunakan untuk mengetahui:

- siapa yang membayar;
- santri;
- orang tua;
- nominal;
- tanggal;
- jenis pembayaran;
- metode pembayaran;
- status;
- periode.

## 35.2 Laporan Pemasukan

Menampilkan rekap uang yang masuk pada periode tertentu.

Contoh:

```text
Total pemasukan Agustus 2026
Rp500.000.000
```

## 35.3 Laporan Tunggakan

Menampilkan tagihan yang belum lunas atau terlambat.

Digunakan untuk:

- mengetahui santri yang belum membayar;
- monitoring tunggakan;
- audit kepala bendahara.

---

# 36. Report Filters

Laporan harus mendukung filter:

- Tahun Ajaran.
- Jenis Pembayaran.
- Kelas.
- Rombel.
- Santri tertentu.
- Orang tua tertentu.
- Status pembayaran.
- Metode pembayaran.
- Periode tanggal.

Periode harus fleksibel.

Contoh:

```text
1 Agustus 2026
sampai
31 Agustus 2026
```

atau rentang tanggal lainnya.

Filtering dapat digunakan untuk menghasilkan laporan:

- harian;
- bulanan;
- tahunan;

tanpa harus membuat tiga sistem laporan yang berbeda.

---

# 37. Report Export

Setiap laporan dapat diekspor dalam dua format:

### Excel

Format:

```text
.xlsx
```

### PDF

Format:

```text
.pdf
```

User internal dapat memilih:

```text
Laporan Pembayaran
Laporan Pemasukan
Laporan Tunggakan
```

kemudian menentukan filter dan periode, lalu memilih:

```text
Generate Excel
Generate PDF
```

---

# 38. Audit Log

Audit Log merupakan fitur wajib.

Tujuan utama:

> Membantu Super Admin melakukan audit terhadap aktivitas administratif.

Audit log minimal mencatat:

- siapa yang melakukan tindakan;
- role;
- tindakan;
- objek/data yang terpengaruh;
- waktu;
- informasi perubahan yang relevan.

Contoh:

```text
Admin Joko
→ mencatat pembayaran manual
→ 16 Agustus 2026 10:30

Admin Sari
→ mengubah data santri
→ 16 Agustus 2026 11:20

Super Admin
→ mengubah nominal SPP kelas 5-A
→ 16 Agustus 2026 12:00
```

## 38.1 Audit Log Access

Audit Log hanya ditampilkan untuk:

> **Super Admin**

Admin tidak memiliki akses ke menu Audit Log.

Namun aktivitas Admin tetap harus dicatat oleh sistem.

---

# 39. Dashboard

Dashboard merupakan bagian dari kebutuhan monitoring.

Untuk prototype, dashboard minimal menyediakan informasi dasar yang berhubungan dengan:

- jumlah santri;
- status pembayaran;
- pembayaran;
- pemasukan;
- tunggakan.

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Detail final dashboard masih akan divalidasi melalui hands-on/vibe coding bersama client.

Namun dashboard dasar tetap **WAJIB dibuat**.

Jangan menunggu validasi dashboard untuk menghilangkan dashboard dari prototype.

---

# 40. Data Visibility

## Super Admin

Dapat melihat seluruh data administratif:

- seluruh santri;
- seluruh orang tua;
- seluruh tagihan;
- seluruh pembayaran;
- seluruh laporan;
- seluruh konfigurasi yang diizinkan;
- audit log.

## Admin

Dapat melihat data operasional:

- seluruh santri;
- seluruh orang tua;
- seluruh tagihan;
- seluruh pembayaran;
- laporan yang tersedia.

Tidak dapat melihat:

- Audit Log;
- konfigurasi yang secara eksplisit hanya untuk Super Admin.

## User

Hanya dapat melihat data miliknya sendiri dan anak yang terhubung.

User tidak boleh mengakses:

- santri lain;
- orang tua lain;
- pembayaran orang lain;
- laporan internal;
- konfigurasi;
- audit log.

---

# 41. Existing Project Context

Project existing sudah memiliki pondasi:

- Laravel 12.
- Filament 5.
- Midtrans.
- MySQL.
- Blade.
- Tailwind CDN.
- Vite.

Struktur existing juga sudah memiliki konsep:

- `users`;
- `products`;
- `orders`;
- `order_details`;
- `payments`;
- controller order;
- controller payment;
- Filament pages/widgets;
- konfigurasi Midtrans.

Referensi existing project berasal dari README yang tersedia.

## Development Principle

Prototype **tidak dimulai dari nol**.

Pondasi existing harus dipertahankan selama masih kompatibel dengan requirement baru.

Perubahan besar terhadap pondasi hanya boleh dilakukan jika:

1. Ada alasan teknis yang jelas.
2. Ada dampak positif yang jelas.
3. Perubahan diperlukan untuk memenuhi requirement.
4. Perubahan tidak dilakukan hanya karena developer memiliki preferensi terhadap teknologi/struktur lain.

---

# 42. Filament

Filament 5 tetap digunakan sebagai fondasi interface aplikasi untuk seluruh role pada prototype.

Role:

```text
Super Admin → Filament
Admin       → Filament
User        → Filament
```

## UI Customization

Penggunaan Filament **tidak berarti UI harus menggunakan tampilan default Filament secara mentah**.

Elemen Filament dapat dikustomisasi apabila tidak sesuai dengan kebutuhan desain.

Customization dapat mencakup:

- Navigation.
- Sidebar.
- Dashboard.
- Widget.
- Table.
- Form.
- Badge.
- Action.
- Theme.
- Branding.
- Layout.
- Component styling.

Jika suatu elemen Filament tidak sesuai dengan kebutuhan produk, elemen tersebut dapat dikustomisasi secara bertahap.

Filament tidak boleh diganti secara keseluruhan hanya karena ketidaksukaan terhadap beberapa elemen UI.

---

# 43. Design Direction

Arah visual prototype:

> **Modern SaaS + Corporate**

Karakter desain:

- modern;
- profesional;
- bersih;
- administratif;
- mudah dipahami;
- tidak terlalu dekoratif;
- cocok untuk aplikasi keuangan/administrasi;
- memiliki hierarki informasi yang jelas.

## Branding

Logo dan warna identitas pondok belum tersedia.

## ⚠️ TEMPORARY — CLIENT CONFIRMATION REQUIRED

Branding pondok akan ditambahkan kemudian.

Prototype tetap harus memiliki sistem visual yang konsisten meskipun branding final belum tersedia.

---

# 44. Core User Journeys

## 44.1 Super Admin

```text
Login
↓
Dashboard
↓
Monitoring pembayaran
↓
Melihat laporan
↓
Melihat tunggakan
↓
Audit aktivitas
↓
Mengelola jenis pembayaran
↓
Mengatur nominal
↓
Mengatur jatuh tempo
↓
Mengelola tahun ajaran
```

## 44.2 Admin

```text
Login
↓
Dashboard
↓
Kelola santri/orang tua
↓
Monitoring tagihan
↓
Monitoring pembayaran
↓
Catat pembayaran manual
↓
Lihat laporan
↓
Tangani notifikasi
↓
Resend email jika diperlukan
```

## 44.3 Orang Tua

```text
Login
↓
Dashboard
↓
Melihat anak
↓
Pilih anak
↓
Melihat tagihan
↓
Bayar
↓
Midtrans
↓
Pembayaran berhasil
↓
Bukti pembayaran
↓
History pembayaran
```

---

# 45. Automated Billing Journey

Untuk SPP prototype:

```text
Tahun Ajaran Aktif
↓
Santri berstatus Aktif
↓
Tanggal 1
↓
Sistem membuat tagihan SPP
↓
Tagihan tersedia
↓
Email tagihan dikirim
↓
H-2 sebelum jatuh tempo
↓
Email reminder + Invoice PDF
↓
Orang tua melakukan pembayaran
↓
Midtrans callback
↓
Sistem memproses pembayaran
↓
Bukti pembayaran dibuat
↓
Email bukti pembayaran
↓
History diperbarui
```

---

# 46. Late Payment Journey

```text
Tagihan belum lunas
↓
Tanggal jatuh tempo terlewati
↓
Status menjadi Terlambat
↓
Email keterlambatan
↓
Invoice sebelumnya dilampirkan
```

---

# 47. Manual Payment Journey

```text
Orang tua melakukan pembayaran offline
↓
Admin/Super Admin membuka tagihan
↓
Input pembayaran manual
↓
Input nominal
↓
Input tanggal
↓
Input metode
↓
Input catatan
↓
Upload bukti
↓
Simpan
↓
Pembayaran masuk history
↓
Audit Log tercatat
↓
Laporan diperbarui
```

---

# 48. Payment Audit Model

Sistem harus memungkinkan Kepala Bendahara menjawab pertanyaan seperti:

### Siapa yang sudah membayar?

Dapat dilihat melalui laporan pembayaran dan data tagihan.

### Siapa yang belum membayar?

Dapat dilihat melalui status tagihan dan laporan tunggakan.

### Siapa yang terlambat?

Dapat dilihat melalui status `Terlambat`.

### Berapa pemasukan bulan ini?

Dapat dilihat melalui laporan pemasukan.

### Pembayaran berasal dari mana?

Dapat dibedakan berdasarkan metode:

- Midtrans.
- Manual.

### Siapa yang mencatat pembayaran manual?

Dapat dilihat melalui audit/payment record.

### Siapa yang mengubah data?

Dapat dilihat melalui Audit Log.

---

# 49. Non-Functional Product Expectations

Prototype harus memperhatikan:

## Usability

Sistem harus mudah digunakan oleh karyawan pondok yang tidak harus memiliki kemampuan teknis tinggi.

## Auditability

Aktivitas finansial dan administratif penting harus dapat ditelusuri.

## Data Isolation

User hanya dapat mengakses data dirinya dan anaknya.

## Maintainability

Struktur harus memungkinkan penambahan jenis pembayaran dan perubahan aturan tanpa perlu membangun ulang sistem dari nol.

## Extensibility

Sistem harus dapat mengakomodasi:

- jenis pembayaran baru;
- nominal berbeda;
- jatuh tempo berbeda;
- cicilan;
- metode pembayaran;
- tahun ajaran baru.

## Prototype Stability

Perubahan besar terhadap pondasi existing harus dihindari kecuali benar-benar diperlukan.

---

# 50. Temporary Decisions Register

Bagian ini adalah daftar penting yang harus diperiksa kembali kepada client.

| Area | Keputusan Prototype | Status |
|---|---|---|
| Tahun Ajaran | Menggunakan Tahun Ajaran | ⚠️ Temporary |
| Tahun Ajaran Baru | Super Admin menekan "Mulai Tahun Ajaran Baru" | ⚠️ Temporary |
| Kenaikan Kelas | Otomatis naik 1 tingkat | ⚠️ Temporary |
| Kelas | 1–9 | ⚠️ Temporary |
| Rombel | A/B | ⚠️ Temporary |
| SPP | Tagihan otomatis tanggal 1 | ⚠️ Temporary |
| Jatuh Tempo SPP | Tanggal 1 | ⚠️ Temporary |
| Pembayaran Lain | Jatuh tempo dapat diatur | ⚠️ Temporary |
| Cicilan | Semua jenis pembayaran mendukung secara teknis | ⚠️ Temporary |
| Status | Belum Bayar, Lunas, Terlambat | ✅ Confirmed |
| Pembayaran Manual | Didukung tetapi fitur minor | ✅ Confirmed |
| Dashboard | Detail final divalidasi saat hands-on | ⚠️ Temporary |
| Branding | Logo/warna ditambahkan kemudian | ⚠️ Temporary |
| Admin/User UI | Menggunakan Filament terlebih dahulu | ⚠️ Temporary |
| Email Provider | Akan dipilih berdasarkan solusi gratis/efisien | ⚠️ Temporary |

---

# 51. Open Requirements

Berikut hal-hal yang masih membutuhkan konfirmasi client.

1. Mekanisme sebenarnya pembuatan tagihan berdasarkan kebijakan pondok.
2. Apakah SPP memang jatuh tempo tanggal 1.
3. Apakah tahun ajaran benar-benar digunakan sebagai periode administrasi pembayaran.
4. Bagaimana perlakuan santri kelas 9 ketika tahun ajaran baru dimulai.
5. Apakah semua jenis pembayaran benar-benar mengizinkan cicilan.
6. Aturan cicilan masing-masing jenis pembayaran.
7. Aturan final kenaikan kelas.
8. Detail operasional role Admin di pondok.
9. Detail dashboard yang paling dibutuhkan Kepala Bendahara.
10. Branding final pondok.
11. Detail email provider production.
12. Detail aturan jatuh tempo masing-masing jenis pembayaran.

Open requirement **tidak boleh digunakan sebagai alasan untuk menghapus fitur prototype yang sudah ditentukan sebagai temporary**.

---

# 52. Success Criteria

Prototype dianggap berhasil apabila pengguna dapat menjalankan alur utama berikut.

## Administrasi

Admin/Super Admin dapat:

- membuat data santri;
- membuat akun orang tua;
- menghubungkan orang tua dengan santri;
- melihat tagihan;
- melihat pembayaran;
- mencatat pembayaran manual;
- melihat laporan.

## Super Admin

Super Admin dapat:

- mengelola jenis pembayaran;
- menentukan nominal;
- mengatur nominal kelompok;
- melakukan override nominal santri;
- mengatur jatuh tempo;
- mengelola tahun ajaran;
- melihat audit log.

## Orang Tua

Orang tua dapat:

- login;
- melihat semua anak;
- memilih anak;
- melihat tagihan;
- membayar melalui Midtrans;
- melihat histori;
- mengakses invoice;
- mengakses bukti pembayaran.

## Automation

Sistem dapat:

- membuat SPP otomatis;
- mengirim email tagihan baru;
- mengirim reminder H-2;
- mengirim invoice;
- menerima callback Midtrans;
- mencatat pembayaran;
- menghasilkan bukti pembayaran;
- mengirim bukti pembayaran;
- mendeteksi keterlambatan;
- mengirim email keterlambatan;
- mencatat email gagal;
- menampilkan notifikasi internal.

## Reporting

Sistem dapat:

- membuat laporan pembayaran;
- membuat laporan pemasukan;
- membuat laporan tunggakan;
- melakukan filter laporan;
- export Excel;
- export PDF.

---

# 53. Product Principle

Prinsip utama pengembangan produk:

> **Sederhanakan pekerjaan bendahara, bukan sekadar mendigitalisasi Excel.**

Sistem harus membantu pengguna mendapatkan jawaban atas pertanyaan administrasi utama dengan cepat:

- Siapa yang sudah bayar?
- Siapa yang belum?
- Siapa yang terlambat?
- Berapa uang yang masuk?
- Pembayaran mana yang dilakukan manual?
- Pembayaran mana yang dilakukan melalui Midtrans?
- Siapa yang melakukan pencatatan?
- Apa yang berubah?
- Bagaimana histori pembayaran seorang santri?
- Apa saja tagihan yang masih berjalan?

---

# 54. Prototype Philosophy

Prototype ini dibuat untuk dua tujuan:

1. Menyediakan sistem yang dapat digunakan untuk mensimulasikan proses administrasi pembayaran.
2. Memberikan gambaran konkret kepada client mengenai arah sistem final.

Karena itu, prototype boleh menggunakan beberapa keputusan sementara.

Namun:

> **Keputusan sementara tetap harus diwujudkan sebagai fitur yang berjalan.**

Tujuan status `TEMPORARY` adalah membantu tim mengetahui:

> "Bagian ini masih harus dikonfirmasi kepada client."

Bukan:

> "Bagian ini tidak perlu dibuat."

---

# 55. Relationship With Other Project Documents

PRD ini menjadi sumber utama untuk **apa yang harus dibangun**.

Dokumen berikutnya memiliki fungsi berbeda:

| File | Fokus |
|---|---|
| `PRD.md` | Apa yang dibangun dan mengapa |
| `ARCHITECTURE.md` | Bagaimana sistem dibangun |
| `SCHEMA.md` | Struktur database dan relasi |
| `DESIGN.md` | UI/UX dan visual system |
| `RULES.md` | Aturan bisnis dan aturan implementasi |
| `AGENTS.md` | Instruksi untuk AI coding agent |

Jika terdapat konflik antara dokumen, requirement produk dalam `PRD.md` menjadi referensi utama, kecuali requirement tersebut secara eksplisit ditandai sebagai temporary/open dan telah mendapatkan keputusan baru dari client.

---

# 56. Final Product Definition

Produk yang dibangun adalah:

> **Sistem Administrasi Pembayaran Pondok berbasis web yang membantu Admin menjalankan operasional pembayaran, membantu Super Admin melakukan monitoring dan audit, serta memungkinkan orang tua melihat dan membayar tagihan anak secara mandiri.**

Sistem menggabungkan:

```text
Data Santri
     ↓
Data Orang Tua
     ↓
Jenis Pembayaran
     ↓
Konfigurasi Nominal
     ↓
Tagihan
     ↓
Pembayaran
 ┌───┴────┐
 ↓        ↓
Midtrans  Manual
 └───┬────┘
     ↓
History
     ↓
Invoice / Receipt
     ↓
Email
     ↓
Reporting
     ↓
Audit
```

Fokus utama prototype:

> **Administrasi Pembayaran + Monitoring + Audit**

Bukan sistem manajemen pondok secara keseluruhan.