# DESIGN.md — Sistem Administrasi Pembayaran Pondok

> **UI/UX Design Specification**
>
> Dokumen ini mendefinisikan visual language, design system, responsive behavior, interaction design, usability rules, accessibility, component behavior, dan user experience untuk Sistem Administrasi Pembayaran Pondok.

---

# 1. Document Information

| Item | Detail |
|---|---|
| Nama Sistem | Sistem Administrasi Pembayaran Pondok |
| Dokumen | Design |
| Versi | 1.0 — Prototype |
| UI Framework | Filament 5 |
| CSS Foundation | Tailwind CSS |
| Frontend | Blade + Filament |
| Target Device | Mobile, Tablet, Laptop/Desktop |
| Design Direction | Modern SaaS + Corporate |
| Status | Design Specification |

---

# 2. Design Objective

Sistem ini merupakan sistem administrasi pembayaran pondok.

UI harus membuat pekerjaan administratif terasa:

- sederhana;
- jelas;
- cepat;
- profesional;
- terpercaya;
- tidak membingungkan;
- mudah dipelajari oleh karyawan;
- nyaman digunakan dalam pekerjaan berulang.

Prioritas utama:

> **Clarity > Speed > Consistency > Visual Decoration**

Design tidak boleh terlihat terlalu ramai hanya demi terlihat modern.

---

# 3. Design Direction

Visual direction:

> **Modern SaaS + Corporate Administration**

Bukan:

- dashboard enterprise yang terlalu kompleks;
- UI startup yang terlalu playful;
- UI government yang terlalu kaku;
- template admin generik;
- Filament default tanpa customization.

Target visual:

```text
Modern
   +
Professional
   +
Calm
   +
Trustworthy
   +
Efficient
```

---

# 4. Design Personality

UI harus terasa:

### Professional

Karena berhubungan dengan administrasi dan uang.

### Modern

Menggunakan spacing, typography, cards, subtle interaction dan layout modern.

### Calm

Tidak menggunakan terlalu banyak warna mencolok.

### Clear

Status dan informasi penting langsung terlihat.

### Human

Pesan sistem harus mudah dimengerti oleh orang non-teknis.

---

# 5. Core UX Principle

Setiap halaman harus menjawab:

> "Saya sekarang berada di mana?"

> "Apa yang bisa saya lakukan?"

> "Apa dampaknya kalau saya menekan tombol ini?"

> "Apakah tindakan saya berhasil?"

> "Kalau gagal, saya harus melakukan apa?"

---

# 6. Design Hierarchy

Prioritas visual:

```text
1. Primary Information
2. Status
3. Important Action
4. Supporting Information
5. Secondary Action
6. Metadata
```

Contoh halaman detail tagihan:

```text
Nama Santri
      ↓
Status Tagihan
      ↓
Nominal
      ↓
Jatuh Tempo
      ↓
Progress Pembayaran
      ↓
Riwayat Pembayaran
      ↓
Dokumen
      ↓
Metadata
```

---

# 7. Layout Philosophy

Layout harus menggunakan:

- whitespace;
- consistent spacing;
- clear grouping;
- visual hierarchy;
- restrained cards;
- readable tables.

Hindari:

- terlalu banyak card;
- terlalu banyak border;
- terlalu banyak shadow;
- terlalu banyak icon;
- terlalu banyak badge.

---

# 8. Application Shell

Desktop:

```text
┌─────────────────────────────────────────────────────────┐
│ Sidebar │ Header / Breadcrumb / User                    │
│         ├───────────────────────────────────────────────┤
│         │                                               │
│         │              Main Content                     │
│         │                                               │
│         │                                               │
│         │                                               │
│         └───────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────┘
```

Mobile:

```text
┌───────────────────────────────┐
│ Header            ☰ / Profile│
├───────────────────────────────┤
│                               │
│       Main Content             │
│                               │
│                               │
└───────────────────────────────┘
```

---

# 9. Sidebar

Desktop sidebar digunakan sebagai primary navigation.

Navigation harus dikelompokkan berdasarkan pekerjaan.

Contoh:

```text
DASHBOARD

DATA MASTER
├── Orang Tua
├── Santri
├── Jenis Pembayaran
└── Tahun Ajaran

PEMBAYARAN
├── Tagihan
├── Pembayaran
└── Riwayat

LAPORAN
├── Laporan Pembayaran
├── Laporan Pemasukan
└── Laporan Tunggakan

SISTEM
├── Notifikasi
└── Audit Log
```

Tidak semua menu ditampilkan kepada semua role.

---

# 10. Navigation Rules

Navigation harus:

- konsisten;
- tidak berubah-ubah posisi tanpa alasan;
- memiliki label jelas;
- menggunakan icon sebagai supporting visual;
- tidak hanya mengandalkan icon.

Jangan membuat menu seperti:

```text
💰
📊
👥
⚙️
```

tanpa tooltip/label pada desktop navigation.

---

# 11. Mobile Navigation

Pada mobile sidebar berubah menjadi:

> drawer navigation.

Menu dibuka melalui:

```text
☰
```

Drawer harus dapat:

- dibuka;
- ditutup;
- ditutup ketika user memilih menu;
- tidak menghalangi interaction secara permanen.

---

# 12. Responsive Requirement

## REQUIRED

Web wajib responsive pada:

- Mobile;
- Tablet;
- Laptop/Desktop.

Target:

```text
Mobile
~320px+
```

```text
Tablet
~768px+
```

```text
Desktop
~1024px+
```

Breakpoint exact mengikuti Tailwind/Filament implementation.

---

# 13. Responsive Principle

Tidak boleh hanya:

> "desktop layout diperkecil."

Mobile harus memiliki layout yang dirancang khusus.

Contoh:

Desktop:

```text
Filter | Search | Date | Status | Export
```

Mobile:

```text
Search
Filter
Date
Status
Export
```

dapat menjadi stacked layout.

---

# 14. Mobile Priority

Pada mobile:

> Primary information harus tampil terlebih dahulu.

Contoh tagihan:

```text
SPP Agustus
Rp500.000
🔴 Terlambat

Jatuh tempo
1 Agustus 2026

Sisa
Rp500.000

[ Bayar ]
```

Metadata tidak boleh mengambil tempat lebih besar daripada informasi utama.

---

# 15. Tablet Behavior

Tablet harus menggunakan:

- 2-column layout bila cukup;
- responsive table;
- collapsible navigation;
- touch-friendly buttons.

Jangan menganggap tablet sebagai desktop kecil.

---

# 16. Desktop Behavior

Desktop dapat menggunakan:

- multi-column form;
- data table;
- sidebar persistent;
- dashboard widgets;
- modal width lebih besar;
- filter toolbar horizontal.

---

# 17. Touch Target

Interactive element harus cukup besar untuk touch.

Target minimum:

> sekitar 44×44 px untuk touch target.

Contoh:

- button;
- icon button;
- close button;
- checkbox;
- dropdown;
- pagination.

---

# 18. Typography

Typography harus:

- mudah dibaca;
- professional;
- memiliki hierarchy;
- tidak terlalu dekoratif.

Hierarchy:

```text
Page Title
   ↓
Section Heading
   ↓
Card Heading
   ↓
Body
   ↓
Supporting Text
```

---

# 19. Recommended Font Direction

Untuk prototype gunakan font sans-serif modern.

Prioritas:

1. font existing project jika sudah konsisten;
2. system font;
3. modern sans-serif.

Jangan menambahkan font external hanya demi aesthetic apabila menyebabkan:

- performance issue;
- loading tambahan;
- inconsistent rendering.

---

# 20. Color System

## ⚠️ TEMPORARY — BRANDING NOT FINAL

Logo dan warna resmi pondok belum diberikan.

Maka prototype menggunakan neutral corporate color system.

Warna pondok dapat diganti kemudian melalui design tokens/theme.

---

# 21. Color Philosophy

Primary:

> Professional dark/blue/indigo corporate tone.

Neutral:

- white;
- gray;
- slate;
- near-black.

Semantic colors:

- yellow;
- green;
- red;
- blue.

Semantic colors hanya digunakan ketika memiliki makna.

---

# 22. Payment Status Colors

Ini merupakan requirement visual utama.

### Belum Bayar

```text
🟡 Yellow
```

Meaning:

> Tagihan masih belum dibayar tetapi belum melewati jatuh tempo.

### Lunas

```text
🟢 Green
```

Meaning:

> Tagihan sudah dibayar penuh.

### Terlambat

```text
🔴 Red
```

Meaning:

> Tagihan sudah melewati jatuh tempo dan masih memiliki outstanding.

---

# 23. Status Badge

Status harus selalu ditampilkan sebagai:

```text
[ Belum Bayar ]
[ Lunas ]
[ Terlambat ]
```

Bukan hanya:

```text
🟡
🟢
🔴
```

Color harus menjadi reinforcement, bukan satu-satunya informasi.

---

# 24. Badge Style

Badge:

- compact;
- rounded;
- readable;
- consistent.

Contoh:

```text
🟡 Belum Bayar
🟢 Lunas
🔴 Terlambat
```

Tidak perlu menggunakan badge berukuran besar.

---

# 25. Primary Button

Primary action menggunakan visual paling prominent.

Contoh:

```text
+ Tambah Santri
+ Tambah Orang Tua
+ Buat Tagihan
Simpan Perubahan
```

Button text harus berupa action.

Hindari:

```text
OK
Submit
Click Here
```

jika bisa dibuat lebih jelas.

---

# 26. Secondary Button

Untuk action yang tidak utama:

```text
Batal
Kembali
Lihat Detail
Filter
```

Secondary button tidak boleh secara visual mengalahkan primary action.

---

# 27. Destructive Action

Action destructive:

- Hapus;
- Nonaktifkan;
- Reset;
- Cancel operation;

harus memiliki visual warning.

Contoh:

```text
[ Hapus ]
```

harus berbeda secara visual dari:

```text
[ Simpan ]
```

---

# 28. Dangerous Financial Action

Action yang berdampak terhadap keuangan harus lebih jelas.

Contoh:

> Ubah nominal SPP.

UI harus menjelaskan:

```text
Nominal saat ini
Rp500.000

Nominal baru
Rp550.000

Perubahan akan berlaku untuk
tagihan baru.
```

Jangan membuat user menebak apakah perubahan mempengaruhi tagihan lama.

---

# 29. Form Design

Form harus:

- grouped;
- logically ordered;
- memiliki label;
- memiliki helper text jika diperlukan;
- memiliki validation;
- tidak terlalu panjang dalam satu section.

---

# 30. Form Grouping

Contoh Student Form:

```text
INFORMASI SANTRI
├── NIS
├── Nama
├── Jenis Kelamin

DATA AKADEMIK
├── Kelas
├── Rombel
└── Tahun Ajaran

DATA ORANG TUA
├── Orang Tua
└── Kontak
```

---

# 31. Form Label

Label harus menggunakan bahasa bisnis.

Gunakan:

```text
Nama Santri
Nomor Induk Santri
Tahun Ajaran
Jatuh Tempo
Nominal Pembayaran
```

Jangan:

```text
student_name
nis_number
academic_year_id
```

---

# 32. Helper Text

Helper text digunakan ketika user perlu memahami consequence.

Contoh:

> "Nominal khusus ini akan digunakan menggantikan nominal berdasarkan kelas/rombel untuk santri ini."

---

# 33. Required Field

Required field harus jelas.

Gunakan:

```text
Nama Santri *
```

atau visual convention yang konsisten.

Jangan membuat user submit form lalu baru mengetahui hampir semua field wajib.

---

# 34. Validation

Validation harus muncul:

- dekat field;
- menggunakan bahasa manusia;
- menjelaskan cara memperbaiki.

Contoh buruk:

> `The amount field is invalid.`

Contoh baik:

> `Nominal pembayaran harus lebih besar dari Rp0.`

---

# 35. Unsaved Changes

Ini merupakan requirement penting.

Jika user sedang mengisi form dan melakukan:

```text
X / Close
```

sistem **tidak boleh langsung membuang form** jika ada perubahan yang belum disimpan.

---

# 36. Unsaved Changes Confirmation

Contoh:

```text
┌───────────────────────────────────┐
│ Tutup tanpa menyimpan?             │
│                                   │
│ Perubahan yang Anda buat belum    │
│ disimpan dan akan hilang.         │
│                                   │
│ [ Tetap Mengedit ] [ Tutup ]      │
└───────────────────────────────────┘
```

Primary safe action:

> Tetap Mengedit

Destructive action:

> Tutup

---

# 37. Modal Closing Behavior

Modal harus memiliki smooth interaction.

Sequence:

```text
User click X
      ↓
Check unsaved changes
      ↓
No changes
   ↓       ↓
 Close    Has changes
            ↓
      Confirmation Modal
```

Tidak boleh:

```text
Click X
↓
Modal langsung hilang
↓
Data hilang
```

---

# 38. Modal Animation

Modal opening:

```text
Opacity 0 → 100
Scale slightly smaller → normal
```

Modal closing:

```text
Normal
↓
Opacity decreases
↓
Disappear
```

Animation harus:

- cepat;
- subtle;
- tidak mengganggu.

---

# 39. Animation Duration

Recommended:

```text
Fast interaction
~100–150ms

Normal modal/dropdown
~150–250ms

Page transition if applicable
~200–300ms
```

Hindari animation terlalu lambat.

User tidak boleh merasa UI "menunggu animasi."

---

# 40. Reduced Motion

Jika user menggunakan OS-level:

> `prefers-reduced-motion`

animation harus dikurangi atau dinonaktifkan.

Accessibility lebih penting daripada visual effect.

---

# 41. Loading State

Setiap operation yang membutuhkan waktu harus memiliki feedback.

Contoh:

```text
[Simpan]
```

menjadi:

```text
[ ⟳ Menyimpan... ]
```

Button tidak boleh dapat diklik berkali-kali selama request berlangsung.

---

# 42. Prevent Double Submission

Untuk action seperti:

- bayar;
- simpan;
- generate report;
- mulai tahun ajaran;
- kirim ulang email;

setelah click:

```text
Button disabled
+
Loading indicator
```

Tujuan:

> mencegah duplicate operation.

---

# 43. Success Feedback

Setelah operation berhasil:

Gunakan:

- toast;
- notification;
- inline feedback;

sesuai konteks.

Contoh:

```text
✓ Data santri berhasil disimpan.
```

Feedback harus:

- singkat;
- jelas;
- tidak mengganggu pekerjaan.

---

# 44. Error Feedback

Contoh:

```text
✕ Data gagal disimpan.
Periksa kembali field yang ditandai.
```

Jika system error:

```text
✕ Terjadi kesalahan saat menyimpan data.
Silakan coba lagi.
```

Jangan menampilkan stack trace.

---

# 45. Financial Success Feedback

Untuk pembayaran:

```text
✓ Pembayaran berhasil dicatat.
Bukti pembayaran telah dibuat.
```

Jika email belum terkirim:

```text
✓ Pembayaran berhasil dicatat.

Bukti pembayaran berhasil dibuat,
namun email belum berhasil dikirim.
```

Payment success tidak boleh terlihat gagal hanya karena email gagal.

---

# 46. Empty State

Empty state harus menjelaskan:

1. apa yang kosong;
2. kenapa;
3. apa yang bisa dilakukan.

Contoh:

```text
Belum ada data santri

Belum ada santri yang terdaftar pada sistem.

[ + Tambah Santri ]
```

Bukan hanya:

> No records found.

---

# 47. Empty Report

Contoh:

```text
Tidak ada data untuk periode ini.

Coba ubah:
- periode;
- jenis pembayaran;
- kelas;
- status.
```

---

# 48. Skeleton Loading

Untuk halaman yang memiliki data cukup berat:

gunakan skeleton/loading state.

Contoh:

```text
████████████
████████
████████████████
```

Skeleton tidak harus digunakan pada semua action.

Gunakan ketika loading memang terasa.

---

# 49. Table Design

Table merupakan komponen penting karena sistem bersifat administratif.

Table harus:

- readable;
- sortable;
- filterable;
- searchable;
- responsive;
- memiliki row action;
- memiliki pagination.

---

# 50. Payment Table

Contoh desktop:

| Santri | Jenis | Periode | Nominal | Jatuh Tempo | Status |
|---|---|---|---:|---|---|
| Ahmad | SPP | Agustus | Rp500K | 1 Aug | 🟢 Lunas |
| Budi | SPP | Agustus | Rp500K | 1 Aug | 🟡 Belum Bayar |
| Citra | SPP | Agustus | Rp500K | 1 Aug | 🔴 Terlambat |

---

# 51. Mobile Table

Table tidak boleh memaksa seluruh kolom tampil sekaligus pada layar kecil.

Approach:

```text
Primary columns
↓
Expandable details
```

atau:

```text
Card/list representation
```

Contoh:

```text
┌──────────────────────────┐
│ Ahmad                    │
│ SPP · Agustus            │
│                          │
│ Rp500.000                │
│ 🟢 Lunas                 │
│                          │
│ Jatuh tempo: 1 Aug       │
│                          │
│ [ Lihat Detail ]         │
└──────────────────────────┘
```

---

# 52. Search

Search harus:

- cepat;
- forgiving;
- memiliki placeholder jelas.

Contoh:

> Cari nama santri, NIS, atau nomor tagihan...

---

# 53. Filters

Filter dapat berupa:

- status;
- kelas;
- rombel;
- jenis pembayaran;
- tahun ajaran;
- periode;
- payment method.

Filter harus dapat di-reset.

Contoh:

```text
[ Filter ]
[ Reset Filter ]
```

---

# 54. Filter Mobile

Pada mobile filter dapat dibuka melalui:

```text
[ ⚙ Filter ]
```

dalam modal/drawer.

Jangan membuat 8 dropdown berjajar horizontal pada layar kecil.

---

# 55. Search + Filter State

Saat user melakukan:

```text
Search
+
Filter
```

state harus tetap dipertahankan ketika:

- membuka detail;
- kembali ke list;
- melakukan pagination.

Jika implementation memungkinkan.

---

# 56. Pagination

Pagination harus:

- jelas;
- touch-friendly;
- menampilkan current page;
- tidak terlalu banyak nomor halaman pada mobile.

Contoh:

```text
‹     1  2  3     ›
```

---

# 57. Dashboard Design

Dashboard bukan sekadar kumpulan card.

Tujuan dashboard:

> Memberikan snapshot kondisi administrasi.

---

# 58. Super Admin Dashboard

Prioritas:

```text
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ Total Santri │ Sudah Bayar  │ Tunggakan    │ Pemasukan    │
│      420     │     360      │      60      │ Rp...        │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

Kemudian:

```text
Pembayaran Terbaru
```

```text
Tunggakan Terbaru
```

```text
Pemasukan
```

---

# 59. Admin Dashboard

Admin fokus pada operational information:

- pembayaran;
- tagihan;
- tunggakan;
- pembayaran terbaru;
- notification/error.

Super Admin dapat melihat information yang lebih luas.

---

# 60. Parent Dashboard

Parent fokus pada:

```text
Anak Saya
```

```text
Total Tagihan
```

```text
Belum Dibayar
```

```text
Terlambat
```

```text
Riwayat Pembayaran
```

---

# 61. Parent Multi-Student UI

Jika parent memiliki 3 anak:

```text
Anak Saya

┌──────────────────────┐
│ Ahmad                │
│ Kelas 5-A            │
│ 🟡 1 tagihan         │
└──────────────────────┘

┌──────────────────────┐
│ Budi                 │
│ Kelas 7-B            │
│ 🟢 Semua lunas       │
└──────────────────────┘

┌──────────────────────┐
│ Citra                │
│ Kelas 9-A            │
│ 🔴 1 terlambat       │
└──────────────────────┘
```

---

# 62. Parent Payment Flow

Flow:

```text
Parent Dashboard
↓
Select Child
↓
View Bills
↓
Select Bill
↓
View Detail
↓
Bayar
↓
Midtrans
↓
Payment Result
```

---

# 63. Payment Detail Page

Harus menunjukkan:

```text
Nama Santri
Jenis Pembayaran
Periode
Total Tagihan
Sudah Dibayar
Sisa
Jatuh Tempo
Status
```

Jika cicilan:

```text
Riwayat Pembayaran
```

ditampilkan.

---

# 64. Installment UI

Contoh:

```text
Tagihan
Rp1.000.000

Sudah dibayar
Rp400.000

Sisa
Rp600.000

Progress
██████░░░░ 40%

Riwayat:
1. Rp400.000 — 10 Aug
```

Jika allowed:

```text
[ Bayar Sisa ]
```

---

# 65. Invoice UI

Invoice detail harus memiliki:

```text
Nomor Invoice
Tanggal
Orang Tua
Santri
Jenis Pembayaran
Periode
Nominal
Jatuh Tempo
Status
```

Action:

```text
[ Lihat PDF ]
```

---

# 66. Receipt UI

Receipt detail:

```text
Bukti Pembayaran

Nomor
Tanggal
Santri
Jenis Pembayaran
Nominal
Metode Pembayaran
Status
```

Action:

```text
[ Lihat PDF ]
```

---

# 67. Payment History

History harus chronological.

Contoh:

```text
10 Aug 2026
Rp300.000
✓ Berhasil

5 Aug 2026
Rp200.000
✓ Berhasil
```

---

# 68. Report UX

Report generation harus menggunakan wizard/structured filter.

Flow:

```text
Pilih Jenis Laporan
        ↓
Pilih Periode
        ↓
Pilih Filter Tambahan
        ↓
Preview / Generate
        ↓
Pilih Format
   ┌────┴────┐
 Excel       PDF
```

---

# 69. Report Type UI

Super Admin dapat memilih:

```text
○ Laporan Pembayaran

○ Laporan Pemasukan

○ Laporan Tunggakan
```

---

# 70. Report Period UI

Period selection harus mendukung:

- tanggal;
- bulan;
- tahun;
- tahun ajaran.

Contoh:

```text
Periode
[ 1 Agustus 2026 ] — [ 31 Agustus 2026 ]
```

atau:

```text
Bulan
[ Agustus 2026 ]
```

atau:

```text
Tahun Ajaran
[ 2026/2027 ]
```

---

# 71. Report Export UI

```text
Format Export

[ Excel ]
[ PDF ]
```

Button harus menjelaskan action:

```text
[ Generate Excel ]
[ Generate PDF ]
```

---

# 72. Academic Year UI

Super Admin memiliki halaman:

> Tahun Ajaran

Menampilkan:

```text
2026/2027
ACTIVE

2025/2026
ARCHIVED
```

---

# 73. Start New Academic Year UX

Ini merupakan destructive/high-impact action.

Button:

```text
+ Mulai Tahun Ajaran Baru
```

harus membuka confirmation flow.

---

# 74. Academic Year Confirmation

Contoh:

```text
Mulai Tahun Ajaran Baru?

Anda akan memulai tahun ajaran 2027/2028.

Sistem akan:
✓ Membuat tahun ajaran baru
✓ Menjadikannya sebagai tahun aktif
✓ Menaikkan kelas santri
✓ Mempertahankan histori tahun sebelumnya

[ Batal ] [ Mulai Tahun Ajaran Baru ]
```

---

# 75. Final Confirmation

Untuk action besar, gunakan second confirmation jika diperlukan.

Contoh:

```text
Apakah Anda yakin?

Perubahan ini akan memengaruhi data akademik seluruh santri.

[ Kembali ]
[ Ya, Mulai Tahun Ajaran Baru ]
```

---

# 76. Payment Type Management

List:

```text
Jenis Pembayaran

SPP
Aktif
Bulanan

Uang Gedung
Aktif
Sekali Bayar

...
```

Action:

```text
+ Tambah Jenis Pembayaran
```

---

# 77. Pricing UI

Pada payment type:

```text
Nominal Berdasarkan Kelompok

Kelas 5-A
Rp500.000

Kelas 5-B
Rp550.000

Kelas 6-A
Rp600.000
```

Action:

```text
[ Edit Nominal ]
```

---

# 78. Student Price Override UI

Override harus dilakukan dari:

> Data Santri.

Flow:

```text
Cari Santri
↓
Buka Detail
↓
Pembayaran
↓
Nominal Khusus
↓
Edit
```

Ini sesuai requirement bahwa individual pricing bukan dilakukan dari halaman master payment type.

---

# 79. Due Date UI

Payment Type dapat memiliki:

```text
Jatuh Tempo
Tanggal: 1
```

Untuk jenis pembayaran lain:

```text
Jatuh Tempo
[ Pilih tanggal ]
```

Super Admin dapat mengubah:

- seluruh payment type;
- payment type tertentu;
- student tertentu.

---

# 80. Manual Payment UI

Manual payment merupakan feature minor tetapi tetap harus usable.

Flow:

```text
Tagihan
↓
Catat Pembayaran Manual
↓
Nominal
↓
Tanggal
↓
Metode
↓
Catatan
↓
Bukti
↓
Simpan
```

---

# 81. Manual Payment Confirmation

Sebelum submit:

```text
Konfirmasi Pembayaran

Santri
Ahmad

Tagihan
SPP Agustus

Nominal
Rp300.000

Metode
Tunai

[ Batal ] [ Catat Pembayaran ]
```

---

# 82. Payment Confirmation Principle

Financial action harus menunjukkan:

> siapa

> membayar apa

> berapa

> kapan

sebelum user melakukan final submit.

---

# 83. Delete Confirmation

Delete tidak boleh menggunakan:

```text
"Are you sure?"
```

saja.

Harus menjelaskan object.

Contoh:

```text
Hapus Data Santri?

Anda akan menghapus/menonaktifkan data:
Ahmad Fauzan — 5-A.

Data pembayaran yang sudah tercatat
tidak akan ikut dihapus.

[ Batal ] [ Hapus ]
```

---

# 84. Deletion vs Deactivation

Untuk data yang memiliki historical financial relation:

lebih disarankan menggunakan:

> Nonaktifkan

daripada:

> Hapus permanen.

Contoh:

```text
[ Nonaktifkan Santri ]
```

---

# 85. Confirmation UX Principle

Confirmation harus menjawab:

```text
Apa yang akan terjadi?
Data mana yang terkena?
Apakah bisa dibatalkan?
```

---

# 86. Toast Notification

Toast harus:

- singkat;
- memiliki icon semantic;
- memiliki message;
- tidak menutupi action penting.

Contoh:

```text
✓ Santri berhasil ditambahkan
```

---

# 87. Toast Duration

Success:

> sekitar 3–4 detik.

Error:

> lebih lama atau persistent jika perlu.

Jika error membutuhkan action:

```text
Email gagal dikirim
[ Coba Lagi ]
```

---

# 88. Email Failure UX

Internal notification:

```text
⚠ Email gagal dikirim

Invoice INV-2026-001 untuk
Ahmad Fauzan tidak berhasil dikirim.

[ Lihat Detail ]
[ Kirim Ulang ]
```

---

# 89. Resend Email UX

Sebelum resend:

```text
Kirim Ulang Email?

Email akan dikirim ke:

orangtua@email.com

Dokumen:
Invoice INV-2026-001

[ Batal ] [ Kirim Ulang ]
```

Setelah berhasil:

```text
✓ Email berhasil dimasukkan ke antrean pengiriman.
```

---

# 90. Accessibility

UI harus memperhatikan:

- keyboard navigation;
- focus state;
- readable contrast;
- semantic labels;
- aria label untuk icon-only buttons;
- screen reader compatibility;
- reduced motion.

---

# 91. Color Contrast

Status colors tidak boleh hanya mengandalkan hue.

Contoh:

```text
🟢 Lunas
```

bukan hanya:

```text
●
```

Text harus tetap readable.

---

# 92. Focus State

Interactive element harus memiliki visible focus state.

Jangan menghapus:

```css
outline: none;
```

tanpa menggantinya dengan focus indicator yang jelas.

---

# 93. Icon Usage

Icon digunakan sebagai supporting visual.

Contoh:

```text
+ Tambah
✎ Edit
👁 Lihat
⬇ Export
↻ Kirim Ulang
```

Namun label text tetap diprioritaskan untuk action penting.

---

# 94. Icon-Only Button

Icon-only button hanya digunakan jika:

- action sangat umum;
- context sudah jelas;
- tooltip tersedia.

Contoh:

```text
[ ✎ ]
```

boleh untuk edit pada table.

Namun untuk:

```text
[ 💰 ]
```

lebih baik:

```text
[ Catat Pembayaran ]
```

---

# 95. Breadcrumb

Breadcrumb membantu admin memahami location.

Contoh:

```text
Pembayaran
/
Tagihan
/
SPP Agustus
```

Mobile dapat menyederhanakan breadcrumb.

---

# 96. Page Header

Setiap page harus memiliki:

```text
Page Title
Description
Primary Action
```

Contoh:

```text
Santri
Kelola data seluruh santri pondok.

                         [ + Tambah Santri ]
```

---

# 97. Detail Page Header

Contoh:

```text
Ahmad Fauzan
5-A · NIS 20260123

[ Edit ]
```

Status dapat ditempatkan dekat identity.

---

# 98. Information Density

Sistem administrasi membutuhkan data density lebih tinggi daripada landing page.

Namun:

> dense ≠ crowded.

Gunakan:

- compact table;
- readable spacing;
- clear grouping.

---

# 99. Dashboard Cards

Card hanya digunakan untuk:

- KPI;
- summary;
- important status.

Jangan membuat semua data menjadi card.

---

# 100. Shadows

Shadow harus subtle.

Tujuan:

> hierarchy.

Bukan:

> decoration.

Avoid excessive:

```text
shadow-xl
shadow-2xl
```

pada semua element.

---

# 101. Border Radius

Gunakan consistent radius.

Contoh direction:

```text
small controls → moderate radius
cards → moderate radius
modal → larger radius
```

Jangan setiap component memiliki radius berbeda.

---

# 102. Design Tokens

Branding harus menggunakan token.

Contoh konsep:

```text
primary
primary-hover
background
surface
border
text
muted
success
warning
danger
info
```

Ketika warna pondok diberikan:

```text
Brand Color
↓
Update Theme Tokens
↓
Whole UI changes consistently
```

---

# 103. Logo Strategy

## ⚠️ TEMPORARY — BRANDING NOT FINAL

Logo pondok belum tersedia.

Gunakan placeholder pada prototype.

Logo final harus dapat dimasukkan tanpa mengubah layout.

---

# 104. Branding Locations

Logo dapat muncul di:

- login page;
- sidebar;
- mobile header;
- invoice;
- receipt;
- email;
- PDF report.

---

# 105. Login Page

Login harus sederhana.

```text
┌────────────────────────────┐
│       LOGO PONDOK          │
│                            │
│ Sistem Administrasi        │
│ Pembayaran Pondok          │
│                            │
│ Email                      │
│ [______________________]   │
│                            │
│ Password                   │
│ [______________________]   │
│                            │
│ [       Masuk            ] │
│                            │
│ Lupa Password?             │
└────────────────────────────┘
```

---

# 106. Password Reset

Prototype menggunakan:

> reset password melalui email.

User memasukkan:

```text
Email
```

Kemudian sistem mengirim reset link.

---

# 107. Responsive Login

Login harus nyaman pada:

- mobile;
- tablet;
- desktop.

Desktop dapat menggunakan centered card.

Mobile menggunakan almost full-width form dengan safe margins.

---

# 108. Form Error Preservation

Jika submit gagal:

> input yang valid tidak boleh hilang.

User tidak boleh dipaksa mengisi ulang seluruh form.

---

# 109. Server Error Recovery

Jika server error:

```text
Terjadi kesalahan.

Data Anda belum berhasil disimpan.

Silakan coba lagi.
```

Jika possible, form state harus dipertahankan.

---

# 110. Network State

Jika request membutuhkan waktu:

```text
Menyimpan...
```

Jika timeout:

```text
Koneksi membutuhkan waktu lebih lama dari biasanya.

Periksa koneksi Anda dan coba lagi.
```

Jangan langsung mengatakan:

> Data gagal tersimpan

jika sebenarnya status server belum diketahui.

---

# 111. Prevent Accidental Navigation

Jika form memiliki unsaved changes:

- close modal;
- navigate away;
- browser refresh;

sebisa mungkin harus memberi warning sesuai capability browser/framework.

---

# 112. Modal Size

Modal size berdasarkan task.

### Small

Confirmation sederhana.

### Medium

Simple form.

### Large

Complex form.

### Fullscreen/Sheet

Complex mobile form atau large dataset.

Jangan menggunakan modal besar untuk setiap form.

---

# 113. Mobile Modal

Modal pada mobile dapat berubah menjadi:

> bottom sheet/full-height dialog

jika form panjang.

Tujuannya:

- keyboard friendly;
- scrollable;
- button tetap accessible.

---

# 114. Modal Scroll

Jika content lebih tinggi dari viewport:

```text
Header
────────
Scrollable Body
────────
Footer Actions
```

Footer action harus tetap mudah dijangkau.

---

# 115. Sticky Actions

Pada form panjang:

```text
[ Batal ] [ Simpan ]
```

dapat dibuat sticky pada bottom.

Namun jangan menutupi content.

---

# 116. Table Row Actions

Action order:

```text
Lihat
Edit
Other
```

Destructive:

```text
Hapus
```

diletakkan terpisah dari primary action.

---

# 117. Contextual Actions

Jangan menampilkan action yang tidak relevan.

Contoh:

Jika bill sudah:

```text
Lunas
```

jangan menampilkan:

```text
[ Bayar ]
```

Jika email sudah failed:

```text
[ Kirim Ulang ]
```

boleh muncul.

---

# 118. Disabled State

Disabled button harus menjelaskan alasan jika tidak obvious.

Contoh:

```text
[ Bayar ]
```

disabled karena:

> Tagihan sudah lunas.

---

# 119. Status + Action

Status tidak boleh hanya informatif.

UI harus membantu user menentukan next action.

Contoh:

```text
🔴 Terlambat

[ Catat Pembayaran ]
[ Kirim Ulang Invoice ]
```

---

# 120. Parent Payment Experience

Parent bukan operator.

UI parent harus jauh lebih sederhana daripada Admin.

Parent tidak perlu melihat:

- audit;
- master payment type;
- academic configuration;
- internal notifications;
- administrative reports.

---

# 121. Admin Experience

Admin adalah operator.

Prioritas:

```text
Santri
Tagihan
Pembayaran
Parent
```

Admin tidak boleh:

- menentukan nominal payment;
- mengubah konfigurasi yang hanya boleh Super Admin;
- melihat audit log.

---

# 122. Super Admin Experience

Super Admin memiliki administrative control.

Prioritas:

```text
Dashboard
Master Data
Payment Configuration
Academic Year
Reports
Audit
```

Semua kemampuan Admin tetap tersedia.

---

# 123. Role-Based Navigation

Navigation harus berubah berdasarkan role.

Contoh:

```text
Super Admin
├── Semua menu Admin
├── Jenis Pembayaran
├── Tahun Ajaran
├── Laporan
└── Audit Log
```

Admin:

```text
Admin
├── Orang Tua
├── Santri
├── Tagihan
├── Pembayaran
└── Laporan jika diizinkan
```

Parent:

```text
Parent
├── Dashboard
├── Anak Saya
├── Tagihan
└── Riwayat Pembayaran
```

---

# 124. Visual Difference Between Roles

Jangan membuat UI Super Admin dan Admin terlalu berbeda.

Keduanya tetap berada dalam design system yang sama.

Perbedaan utama:

> capability, navigation, data access.

Bukan:

> warna UI yang berbeda.

---

# 125. Data Privacy UX

Parent hanya melihat data anaknya.

Tidak boleh ada:

```text
Global Student Search
```

yang memperlihatkan student lain kepada parent.

---

# 126. Search Result Privacy

Jika Parent mencari:

```text
Ahmad
```

search hanya boleh mengembalikan:

> anak yang dimiliki parent tersebut.

---

# 127. Financial Privacy

Nominal pembayaran merupakan sensitive financial information.

Parent hanya melihat:

> data pembayaran anaknya.

Admin/Super Admin melihat sesuai authorization.

---

# 128. Report UX for Super Admin

Report generation tidak boleh langsung menghasilkan file tanpa menunjukkan parameter.

Sebelum generate:

```text
Laporan Pembayaran

Tahun Ajaran
2026/2027

Periode
1 Aug — 31 Aug

Status
Semua

Kelas
Semua

Format
Excel
```

---

# 129. Report Generation Feedback

Jika report besar:

```text
Generating report...
```

Jika asynchronous:

```text
Report sedang dibuat.

Anda akan mendapatkan notifikasi ketika file siap.
```

Untuk prototype, synchronous generation masih boleh jika dataset kecil.

---

# 130. Excel/PDF Export

Export button:

```text
Generate Excel
Generate PDF
```

harus memiliki loading state.

Tidak boleh user menekan 10 kali dan menghasilkan 10 file duplicate tanpa feedback.

---

# 131. Audit Log UX

Audit log table:

| Waktu | User | Action | Data | Detail |
|---|---|---|---|---|
| 10 Aug | Admin | Create | Payment | SPP Ahmad |
| 11 Aug | Super Admin | Update | Price | 5-A |

Detail dapat membuka modal.

---

# 132. Audit Detail

Detail:

```text
Action
Update

User
Super Admin

Entity
Payment Type Price

Before
Rp500.000

After
Rp550.000

Time
11 Aug 2026 10:30
```

---

# 133. Notification Center

Notification center dapat menggunakan:

```text
🔔
```

Unread count:

```text
🔔 3
```

Click:

```text
Notification Center
```

---

# 134. Internal Notification Priority

Severity:

```text
Info
Success
Warning
Danger
```

Email failure:

> Warning/Danger.

---

# 135. Interaction Feedback Principle

Setiap action penting harus memiliki:

```text
Before
↓
Action
↓
Loading
↓
Success / Error
↓
Next State
```

Contoh:

```text
[ Simpan ]
     ↓
[ ⟳ Menyimpan ]
     ↓
✓ Berhasil
     ↓
Return to detail
```

---

# 136. No Silent Actions

Jangan melakukan action tanpa feedback.

Contoh buruk:

```text
Click "Kirim Ulang"
↓
nothing
```

Contoh baik:

```text
Click
↓
Sending...
↓
✓ Email dimasukkan ke antrean.
```

---

# 137. Navigation Feedback

Jika page loading membutuhkan waktu:

> tampilkan loading indicator.

Jangan membuat user mengira tombol tidak bekerja.

---

# 138. Error Recovery

Error harus selalu memiliki recovery path jika memungkinkan.

Contoh:

```text
Email gagal dikirim.

[ Coba Lagi ]
```

atau:

```text
Data gagal dimuat.

[ Muat Ulang ]
```

---

# 139. Confirmation Severity

Tidak semua action membutuhkan confirmation.

### Tidak perlu

- membuka detail;
- filter;
- search;
- pagination.

### Perlu

- delete;
- deactivate;
- change price;
- start academic year;
- record financial transaction;
- resend email jika berpotensi duplicate;
- other irreversible action.

---

# 140. Confirmation Copywriting

Copy harus:

> jelas, bukan menakut-nakuti.

Hindari:

> "WARNING!!! THIS ACTION CANNOT BE UNDONE!!!"

Gunakan:

> "Perubahan ini akan berlaku untuk tagihan yang dibuat setelah perubahan disimpan."

---

# 141. Progressive Disclosure

UI tidak perlu menampilkan semua field sekaligus.

Contoh Student Detail:

```text
Informasi Dasar
↓
Data Akademik
↓
Orang Tua
↓
Tagihan
↓
Pembayaran
↓
Nominal Khusus
```

---

# 142. Advanced Settings

Konfigurasi yang jarang digunakan dapat ditempatkan dalam:

> Advanced / Configuration section.

Jangan memenuhi main form dengan semua opsi.

---

# 143. Design for Repeated Work

Admin kemungkinan akan melakukan pekerjaan berulang:

- input santri;
- mencari santri;
- melihat tagihan;
- mencatat pembayaran;
- mengecek tunggakan.

Maka UI harus mengoptimalkan:

> keyboard + search + filter + quick actions.

---

# 144. Search-First Workflow

Untuk pekerjaan administratif, search harus mudah dijangkau.

Contoh:

```text
Cari Santri
[ Ahmad Fauzan________________ ]
```

dan hasil langsung:

```text
Ahmad Fauzan
5-A
NIS 2026001

[ Lihat ]
```

---

# 145. Quick Actions

Pada detail santri:

```text
[ Edit Data ]
[ Lihat Tagihan ]
[ Catat Pembayaran ]
```

Action yang paling sering digunakan ditempatkan paling jelas.

---

# 146. Data Table Density

Desktop table dapat menggunakan compact row height.

Mobile menggunakan card.

Tablet menggunakan responsive table dengan selective columns.

---

# 147. Responsive Testing Matrix

Setiap halaman wajib diuji minimal pada:

| Device | Target |
|---|---|
| Small Mobile | ~320–375px |
| Large Mobile | ~390–430px |
| Tablet Portrait | ~768px |
| Tablet Landscape | ~1024px |
| Laptop | ~1366px |
| Desktop | ~1440px+ |

---

# 148. Responsive QA

Tidak boleh ada:

- horizontal overflow yang tidak disengaja;
- text cut-off;
- button keluar viewport;
- modal terlalu lebar;
- table menghancurkan layout;
- sidebar menutupi content;
- dropdown keluar layar;
- keyboard mobile menutupi input/action;
- footer action tidak bisa dijangkau.

---

# 149. Browser QA

Minimal test:

- Chrome;
- Edge;
- mobile Chrome/Safari jika available.

Primary target:

> modern Chromium browsers.

---

# 150. Visual Consistency

Semua:

- button;
- input;
- modal;
- table;
- badge;
- notification;
- card;

harus menggunakan design tokens dan shared components.

Jangan membuat style ad-hoc per page jika component sudah tersedia.

---

# 151. Filament Customization Rule

Filament default component boleh digunakan jika:

> sudah sesuai design.

Jika tidak sesuai:

> customize.

Jangan mengganti Filament hanya karena visual default tidak disukai.

---

# 152. Avoid Fighting Framework

Customization harus dilakukan melalui:

- theme;
- CSS;
- component configuration;
- Filament extension;
- Blade customization;
- Tailwind.

Hindari hack yang:

- bergantung pada internal DOM yang tidak stabil;
- sulit dipelihara;
- merusak upgradeability.

---

# 153. Upgrade Safety

Custom UI harus sebisa mungkin:

> compatible dengan Filament upgrade.

Jangan override internal Filament secara agresif jika tidak diperlukan.

---

# 154. Performance

UI harus terasa cepat.

Prioritas:

- minimal unnecessary JavaScript;
- optimized queries;
- pagination;
- lazy loading;
- efficient images;
- reasonable animations;
- no excessive client-side libraries.

---

# 155. Animation Philosophy

Animation digunakan untuk:

- feedback;
- orientation;
- continuity.

Bukan untuk:

- dekorasi berlebihan;
- membuat semua element bergerak;
- menunda interaction.

---

# 156. Smoothness Checklist

UI harus terasa smooth pada:

- modal open;
- modal close;
- dropdown;
- sidebar;
- toast;
- loading;
- table refresh;
- page navigation;
- confirmation dialog.

---

# 157. Modal Example

Target behavior:

```text
[ + Tambah Santri ]

       ↓

Modal opens smoothly

       ↓

User fills form

       ↓

User clicks X

       ↓

Unsaved changes detected

       ↓

Confirmation appears

       ↓

User chooses:

[Tetap Mengedit]
        OR
[Tutup]
```

---

# 158. User Feedback Language

Bahasa utama:

> Bahasa Indonesia.

Istilah teknis yang memang umum dapat tetap digunakan.

Contoh:

> Email

> PDF

> Excel

> Dashboard

> Invoice

> Payment

Namun user-facing copy sebisa mungkin natural dalam Bahasa Indonesia.

---

# 159. Avoid Technical Error Messages

Jangan:

> `SQLSTATE[23000]`

Gunakan:

> "Data tidak dapat disimpan karena terdapat data yang sudah digunakan."

Technical detail masuk log.

---

# 160. Financial Formatting

Nominal harus konsisten:

```text
Rp500.000
Rp1.000.000
Rp10.000.000
```

Jangan:

```text
500000
1,000,000
Rp 500.000.00
```

---

# 161. Date Formatting

UI Bahasa Indonesia:

```text
1 Agustus 2026
```

atau:

```text
01/08/2026
```

sesuai konteks.

Untuk detail yang membutuhkan clarity:

> 1 Agustus 2026

lebih disarankan.

---

# 162. Date + Time

Contoh:

```text
10 Agustus 2026, 14.30
```

---

# 163. Status Copy

Gunakan:

```text
Belum Bayar
Lunas
Terlambat
```

Jangan:

```text
UNPAID
PAID
OVERDUE
```

pada UI utama.

Database tetap dapat menggunakan enum/code English.

---

# 164. Payment Source Copy

Internal UI:

```text
Midtrans
Manual
```

---

# 165. Payment Method Copy

Contoh:

```text
Pembayaran Online
Tunai
Transfer Bank
```

---

# 166. Empty Student State

```text
Belum ada santri

Belum ada data santri yang tersedia.

[ + Tambah Santri ]
```

---

# 167. Empty Parent State

```text
Belum ada orang tua

Tambahkan akun orang tua untuk menghubungkan
santri dengan akun pembayaran.

[ + Tambah Orang Tua ]
```

---

# 168. Empty Payment History

```text
Belum ada pembayaran

Belum ada transaksi pembayaran untuk tagihan ini.
```

---

# 169. Empty Notification

```text
Tidak ada notifikasi baru.

Semua aktivitas sudah ditangani.
```

---

# 170. Empty Audit

```text
Belum ada aktivitas audit.
```

---

# 171. Empty Report

```text
Tidak ada data yang sesuai dengan filter.

Coba gunakan periode atau filter lain.
```

---

# 172. Form Autosave

Untuk prototype:

> Tidak wajib melakukan autosave pada seluruh form.

Namun unsaved changes detection tetap diutamakan.

Jika autosave ditambahkan kemudian:

> harus jelas kapan data tersimpan.

---

# 173. Draft State

Form panjang dapat menggunakan draft jika diperlukan.

Namun jangan membuat semua form memiliki draft mechanism tanpa kebutuhan.

---

# 174. Accessibility Copy

Error harus menjelaskan:

```text
Apa yang salah
+
Cara memperbaiki
```

Contoh:

> "Email belum valid. Masukkan alamat email yang benar."

---

# 175. Keyboard Interaction

Desktop harus mendukung:

- Tab;
- Shift+Tab;
- Enter;
- Escape untuk modal;
- arrow navigation untuk dropdown jika framework mendukung.

Escape pada modal:

> harus mengikuti unsaved changes behavior.

---

# 176. Escape Modal

Jika tidak ada perubahan:

```text
ESC
↓
Close
```

Jika ada perubahan:

```text
ESC
↓
Unsaved confirmation
```

---

# 177. Click Outside Modal

Click outside:

### No changes

> close.

### Unsaved changes

> confirmation.

Tidak boleh langsung membuang input.

---

# 178. Browser Back

Pada form unsaved:

> sebisa mungkin beri warning.

Namun jangan mengganggu navigation normal ketika tidak ada perubahan.

---

# 179. Mobile Keyboard

Form harus menghindari:

- input tertutup keyboard;
- button submit tertutup;
- modal tidak dapat scroll.

---

# 180. Input Type

Gunakan input type yang sesuai:

```text
Email → email
Phone → tel
Amount → numeric
Date → date
```

Mobile keyboard harus membantu input.

---

# 181. Numeric Amount Input

Nominal:

```text
Rp [ 500000 ]
```

Display dapat menggunakan formatter:

```text
Rp500.000
```

Namun backend menerima integer.

---

# 182. Amount Validation

Input:

```text
0
```

ditolak jika payment amount harus positif.

Negative:

```text
-500000
```

ditolak.

---

# 183. Student Search UX

Search harus mendukung:

- nama;
- NIS.

Contoh:

```text
Cari "Ahmad"
```

atau:

```text
Cari "2026001"
```

---

# 184. Parent Search UX

Search mendukung:

- nama;
- email;
- phone.

---

# 185. Bill Search UX

Search dapat mendukung:

- bill number;
- invoice number;
- student name;
- NIS.

---

# 186. Invoice Search UX

Search:

- invoice number;
- student;
- parent.

---

# 187. Payment Search UX

Search:

- payment number;
- student;
- bill number;
- transaction reference.

---

# 188. Error Boundary

Unexpected UI errors harus memiliki fallback.

Contoh:

```text
Terjadi kesalahan saat menampilkan halaman.

Silakan muat ulang halaman.
```

---

# 189. Maintenance State

Jika sistem sedang maintenance:

```text
Sistem sedang dalam pemeliharaan.

Silakan coba kembali beberapa saat lagi.
```

---

# 190. Design QA Definition

Sebuah feature dianggap UI-complete apabila:

- responsive;
- tidak overflow;
- loading state tersedia;
- success feedback tersedia;
- error feedback tersedia;
- destructive action memiliki confirmation;
- unsaved changes aman;
- keyboard interaction masuk akal;
- mobile interaction usable;
- status jelas;
- copy Bahasa Indonesia konsisten.

---

# 191. Design Anti-Patterns

Dilarang:

### 1. Instant destructive action

```text
Click delete
↓
langsung delete
```

### 2. Silent save

```text
Click save
↓
nothing
```

### 3. Generic errors

```text
Something went wrong
```

tanpa recovery.

### 4. Desktop-only design

### 5. Color-only status

### 6. Giant modal

### 7. Excessive animations

### 8. Excessive cards

### 9. Hidden important actions

### 10. Technical language untuk user biasa

---

# 192. Prototype Visual Target

Secara visual target akhir:

```text
┌─────────────────────────────────────────────────────────┐
│ Logo Pondok                              User / Profile  │
├─────────────┬───────────────────────────────────────────┤
│             │                                           │
│ Dashboard   │  Dashboard                                │
│             │  Overview administrasi pembayaran         │
│ Santri      │                                           │
│ Orang Tua   │  ┌────────┐ ┌────────┐ ┌────────┐        │
│ Tagihan     │  │ Santri │ │ Lunas  │ │Tunggak │        │
│ Pembayaran  │  │  420   │ │  360   │ │   60   │        │
│             │  └────────┘ └────────┘ └────────┘        │
│ Laporan     │                                           │
│             │  Pembayaran Terbaru                       │
│ Pengaturan  │  ┌─────────────────────────────────────┐  │
│             │  │ Ahmad    SPP    Rp500K   🟢 Lunas │  │
│             │  │ Budi     SPP    Rp500K   🟡 Belum │  │
│             │  └─────────────────────────────────────┘  │
│             │                                           │
└─────────────┴───────────────────────────────────────────┘
```

Visual harus clean, tidak terlalu penuh, dan tetap terasa seperti aplikasi administrasi profesional.

---

# 193. Design System Summary

## Visual

```text
Modern
Corporate
Minimal
Calm
Professional
```

## Interaction

```text
Fast
Smooth
Predictable
Informative
Safe
```

## Responsive

```text
Mobile
Tablet
Desktop
```

## Status

```text
🟡 Belum Bayar
🟢 Lunas
🔴 Terlambat
```

---

# 194. Temporary Design Decisions

| Decision | Status |
|---|---|
| Modern SaaS + Corporate | ✅ Confirmed |
| Responsive Mobile | ✅ Required |
| Responsive Tablet | ✅ Required |
| Responsive Laptop/Desktop | ✅ Required |
| Smooth animation | ✅ Required |
| Unsaved changes confirmation | ✅ Required |
| Filament 5 | ✅ Existing / Retain |
| Customization Filament | ✅ Required |
| Logo pondok | ⚠️ Temporary |
| Brand color | ⚠️ Temporary |
| Final typography | ⚠️ Temporary |
| Final visual branding | ⚠️ Temporary |

---

# 195. Design Rule for Temporary Branding

Logo dan warna yang digunakan saat prototype **tidak boleh menyebar sebagai hardcoded values di setiap component**.

Gunakan:

```text
Theme
↓
Design Tokens
↓
Components
```

Dengan demikian ketika client memberikan:

- logo;
- warna;
- identitas visual;

cukup melakukan theme update.

---

# 196. Final UX Principle

Sistem ini harus terasa seperti:

> **"Aplikasi administrasi yang membantu saya bekerja."**

bukan:

> **"Sebuah template dashboard yang kebetulan digunakan untuk administrasi pondok."**

Setiap visual element harus memiliki alasan.

Setiap animation harus memiliki tujuan.

Setiap confirmation harus mencegah kesalahan.

Setiap status harus mudah dipahami.

Setiap error harus memberi jalan keluar.

---

# 197. Final Design Goal

Target akhir design:

```text
                    MODERN
                       │
                       │
          ┌────────────┴────────────┐
          │                         │
       CORPORATE                 HUMAN
          │                         │
          └────────────┬────────────┘
                       │
                       ↓
                 EASY TO USE
                       │
                       ↓
              ADMINISTRATIVE UX
                       │
                       ↓
                TRUST + CLARITY
```

> **Design bukan hanya tentang membuat sistem terlihat bagus. Design bertugas membuat pekerjaan administrasi menjadi lebih mudah, aman, jelas, dan minim kesalahan.**