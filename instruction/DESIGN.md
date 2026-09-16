# DESIGN.md — Panduan Desain UI/UX & Standar Visual
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> **UI/UX Design Specification & Visual Standards**
>
> Dokumen ini mendefinisikan sistem desain, palet warna, tipografi, perilaku responsif, hierarki interaksi, serta standarisasi antarmuka untuk Sistem Administrasi & Pembayaran MTs. Miftahul 'Ulum.

---

# 1. Prinsip & Filosofi Desain

Aplikasi melayani dua kelompok audiens dengan karakteristik antarmuka berbeda namun harmonis:

1. **Panel Administrasi Internal (`/admin`)**:
   - Berbasis **Filament 5**.
   - Fokus: Kecepatan kerja (*Efficiency*), kemudahan entri data (*Clarity*), dan visualisasi data finansial yang akurat (*Precision*).
   - Tampilan profesional bernuansa korporat modern dengan aksen *Emerald Green*.

2. **Portal Mandiri Wali Santri (`/portal`)**:
   - Berbasis **Laravel Blade + Tailwind CSS**.
   - Fokus: *Mobile-First*, kemudahan navigasi bagi orang tua santri dari ponsel pintar (*Simplicity*), dan pengalaman pembayaran online 1-klik yang mulus (*Seamless Checkout*).
   - Tema utama: **Emerald Green (#059669 / #10B981)** yang merefleksikan nilai-nilai keislaman dan ketenangan madrasah.

---

# 2. Sistem Warna & Identitas Visual (Color Palette)

### 2.1 Palet Warna Utama (Primary Brand Palette)
- **Primary Emerald (Utama)**: `#059669` (Tailwind `emerald-600`) — Tombol aksi utama, header kartu, aksen navigasi.
- **Primary Emerald Dark**: `#047857` (Tailwind `emerald-700`) — Status hover dan header portal.
- **Emerald Subtle / Light**: `#ECFDF5` (Tailwind `emerald-50`) — Background kartu statistik dan badge aktif.
- **Neutral Dark (Teks & Body)**: `#1E293B` (Tailwind `slate-800`) — Tipografi judul dan teks utama.
- **Neutral Muted**: `#64748B` (Tailwind `slate-500`) — Teks sekunder, label, dan helper text.
- **Surface & Background**: `#F8FAFC` (Tailwind `slate-50`) — Latar belakang aplikasi.

### 2.2 Status Finansial (Financial Semantic Badges)
Status tagihan dan transaksi wajib menggunakan standarisasi visual berikut:

| Status Tagihan / Transaksi | Warna Badge | Kode Tailwind | Arti & Penjelasan |
|---|---|---|---|
| **Belum Bayar (`unpaid`)** | 🟡 Kuning / Amber | `bg-amber-50 text-amber-700 border-amber-200` | Tagihan aktif, belum lunas, dan belum melewati jatuh tempo |
| **Lunas (`paid`)** | 🟢 Hijau / Emerald | `bg-emerald-50 text-emerald-700 border-emerald-200` | Tagihan telah dibayar penuh (sisa Rp 0) |
| **Terlambat (`overdue`)** | 🔴 Merah / Rose | `bg-rose-50 text-rose-700 border-rose-200` | Melewati tanggal jatuh tempo dan masih ada tunggakan |
| **Pending Gateway** | 🔵 Biru / Sky | `bg-sky-50 text-sky-700 border-sky-200` | Menunggu pembayaran Snap Midtrans diselesaikan |

---

# 3. Tipografi & Hierarki Teks

- **Font Family**: Menggunakan font modern sans-serif **Inter** atau system-ui sans-serif dengan keterbacaan tinggi pada layar beresolusi tinggi maupun rendah.
- **Hierarki**:
  - `H1` (Judul Halaman): `text-2xl font-bold tracking-tight text-slate-900`
  - `H2` (Judul Bagian/Kartu): `text-lg font-semibold text-slate-800`
  - `Body / Paragraf`: `text-sm text-slate-600 leading-relaxed`
  - `Data Nominal Uang`: `font-semibold text-slate-900 tabular-nums` (memastikan angka sejajar vertikal pada tabel)
  - `Label & Helper`: `text-xs font-medium text-slate-500`

---

# 4. Tata Letak & Navigasi

### 4.1 Struktur Panel Admin (`/admin`)
- **Header**: Menampilkan nama madrasah, breadcrumb navigasi, indikator tahun ajaran aktif, notifikasi, dan avatar profil staf.
- **Sidebar**: Dikelompokkan ke dalam grup fungsional logis:
  - **DASHBOARD**: Ringkasan Statistik & Widget Tagihan Jatuh Tempo.
  - **DATA MASTER**: Siswa (Rombel Dinamis), Orang Tua / Wali, Tahun Ajaran, Pos Pembayaran (11 Pos Resmi).
  - **TRANSAKSI & PENAGIHAN**: Tagihan (Bulk Generator & Cicilan), Pembayaran Loket Kasir, Log WhatsApp.
  - **LAPORAN**: Laporan Pemasukan, Laporan Tunggakan, Laporan Transaksi.
  - **SISTEM & AUDIT**: Audit Trail, Profil & Tanda Tangan Bendahara (`/admin/profile`).

### 4.2 Halaman Profil Bendahara (`/admin/profile`)
- **Judul**: Diberi label sederhana dan jelas **"Profile"**.
- **Komponen Form**:
  1. **Nama Lengkap Bendahara**: Text input untuk nama pejabat resmi (contoh: *Hj. Titi Maryati, S.Pd.I*).
  2. **Tanda Tangan Digital**: File upload komponen dengan preview gambar tanda tangan PNG transparan.
- **Feedback Visual**: Notifikasi toast sukses saat profil disimpan, dengan jaminan langsung terintegrasi ke cetakan PDF.

### 4.3 Struktur Portal Wali Santri (`/portal`)
- **Desain Mobile-First**: Dioptimalkan penuh untuk layar ponsel pintar wali murid (lebar 360px s/d 428px) tanpa horizontal scroll yang mengganggu.
- **Top Bar Hijau Emerald**: Menampilkan identitas madrasah, salam hangat kepada wali santri, dan tombol logout.
- **Kartu Ringkasan Finansial**:
  - Total Tunggakan (dengan nominal besar yang jelas).
  - Jumlah Anak yang Terdaftar di MTs Miftahul 'Ulum.
  - Nomor Rekening Resmi BRI Madrasah (`176901000210569`).
- **Daftar Tagihan Interaktif**: Menampilkan daftar kartu tagihan per anak dengan tombol aksi cepat **"Bayar Online"** dan **"Invoice PDF"**.
- **Riwayat Pembayaran**: Tab arsip transaksi lunas dilengkapi tombol unduh **"Kuitansi PDF"**.

---

# 5. Standar Form & Interaktivitas

### 5.1 Dynamic Cascading Dropdown (Tingkat Kelas $\rightarrow$ Rombel)
Pada form input siswa dan filter tagihan:
- Pilihan **Tingkat Kelas** dipilih terlebih dahulu: `7`, `8`, atau `9`.
- Dropdown **Rombel** otomatis memuat pilihan yang valid saja:
  - Kelas 7 $\rightarrow$ `7.1`, `7.2`, `7.3`
  - Kelas 8 $\rightarrow$ `8.1`, `8.2`, `8.3`, `8.4`
  - Kelas 9 $\rightarrow$ `9.1`, `9.2`, `9.3`, `9.4`
- Mencegah kesalahan manusia (*human error*) dalam penempatan rombel siswa.

### 5.2 Form Transisi Tahun Ajaran Baru
- Cukup menyediakan 2 field tanggal: **Tanggal Mulai** (*Start Date*) dan **Tanggal Selesai** (*End Date*).
- Nama tahun ajaran (contoh: `2026/2027`) otomatis dikalkulasi oleh sistem, menyederhanakan input pengguna.

### 5.3 Konfirmasi Tindakan Berdampak Finansial (Safety Dialog)
Setiap tindakan krusial seperti:
- Pembatalan transaksi kasir,
- Eksekusi kenaikan kelas massal,
- Penerbitan tagihan massal,
wajib menampilkan modal konfirmasi yang menjelaskan dampak tindakan secara transparan sebelum dieksekusi.

---

# 6. Desain Dokumen Cetak & PDF Resmi

Dokumen PDF yang dihasilkan oleh sistem (Invoice dan Kuitansi) dirancang dengan tata letak resmi kop surat madrasah:
- **Kop Surat**:
  - Logo Yayasan Perguruan Islam Miftahul 'Ulum.
  - Teks Resmi: *MADRASAH TSANAWIYAH MIFTAHUL 'ULUM*.
  - Alamat lengkap dan kontak resmi institusi.
- **Tanda Tangan & Cap Stempel**:
  - Posisi kanan bawah dokumen.
  - Cap Stempel Digital Bulat MTs Miftahul 'Ulum.
  - Tanda Tangan Digital Bendahara Aktif (Base64 Render).
  - Nama Lengkap Pejabat Bendahara Terdaftar.
- **Rincian Finansial**:
  - Tabel rincian pos biaya, periode tagihan, tanggal jatuh tempo / tanggal pembayaran.
  - Nominal angka dalam format standar mata uang Rupiah (`Rp 75.000`).
  - Nominal terbilang dalam bahasa Indonesia (*"Tujuh Puluh Lima Ribu Rupiah"*).