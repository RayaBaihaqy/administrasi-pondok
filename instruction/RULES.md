# RULES.md — Aturan Bisnis, Otorisasi, & Integritas Finansial
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> **Business Rules, Authorization, Financial Integrity, and System Constraints**
>
> Dokumen ini memuat seluruh aturan bisnis, validasi, otorisasi, logika finansial, dan batasan teknis yang mengikat pada Sistem Administrasi & Pembayaran MTs. Miftahul 'Ulum.

---

# 1. Hierarki Prioritas Aturan (Rule Priority Hierarchy)

Apabila terjadi keraguan dalam implementasi, urutan prioritas yang wajib dipatuhi adalah:
1. **Keamanan Sistem & Integritas Finansial** (*Financial Correctness & Idempotency*).
2. **Kesesuaian Hak Akses & Otorisasi Pengguna** (*Role-Based Access Control*).
3. **Kesesuaian Tarif Resmi Klien (11 Pos Pembayaran)**.
4. **Kenyamanan & Kejelasan Pengalaman Pengguna (UI/UX)**.

---

# 2. Aturan Hak Akses & Pengguna (User & RBAC Rules)

### 2.1 Role Super Admin (Kepala Yayasan / Kepala Bendahara)
- Memiliki wewenang penuh atas seluruh fitur operasional Admin.
- **Wewenang Eksklusif**:
  - Mengubah matriks tarif pada 11 pos pembayaran resmi.
  - Memulai tahun ajaran baru dan mengeksekusi kenaikan kelas massal.
  - Mengakses log Audit Trail sistem yang bersifat permanen (*immutable*).
  - Mengelola profil dan memperbarui tanda tangan digital bendahara pada menu `Profile` (`/admin/profile`). Setelah simpan berhasil, sistem secara otomatis mengarahkan admin kembali ke dashboard utama (`/admin`).

### 2.2 Role Admin (Operator Keuangan / Kasir)
- Menjalankan operasional pencatatan harian, mengimpor siswa via Excel `.xlsx`, menerbitkan tagihan, mutasi siswa pindah, dan menerima pembayaran kasir offline.
- **Batasan**: Tidak berhak mengubah master tarif nominal pos pembayaran dan tidak dapat melihat log audit sistem.

### 2.3 Role Parent (Orang Tua / Wali Santri)
- **Isolasi Multi-Tenant Mutlak**: Hanya dapat melihat dan membayar tagihan putra/putri kandungnya sendiri (`parent_id == Auth::user()->parent->id`).
- Tidak memiliki akses ke panel internal sekolah (`/admin`).

### 2.4 Aturan Autentikasi Ganda (Dual Identifier Login)
- Pengguna dapat login menggunakan alamat **Email** atau **Nomor Telepon / WhatsApp**.
- Format nomor telepon lokal (`08xx`, `+628xx`, `628xx`) wajib dinormalisasi secara otomatis.
- Pesan kesalahan wajib informatif dan tidak membingungkan:
  - Jika format email tidak ditemukan: `"Email tidak ditemukan."`
  - Jika format nomor telepon tidak ditemukan: `"Nomor telepon tidak ditemukan."`
  - Jika identitas terdaftar tetapi kata sandi salah: `"Password salah."`

---

# 3. Aturan Data Siswa & Struktur Akademik

### 3.1 Jenjang Tingkat Kelas & Batasan Rombel Dinamis
Madrasah memiliki 3 tingkat kelas dengan pembagian rombel yang terikat:
- **Tingkat 7**: Rombel yang sah hanya `7.1`, `7.2`, dan `7.3`.
- **Tingkat 8**: Rombel yang sah hanya `8.1`, `8.2`, `8.3`, dan `8.4`.
- **Tingkat 9**: Rombel yang sah hanya `9.1`, `9.2`, `9.3`, dan `9.4`.
- Sistem **menolak** penyimpanan data siswa jika rombel yang dipilih tidak sesuai dengan tingkat kelasnya.

### 3.2 Status Siswa Default & Status yang Didukung
- Setiap penambahan siswa baru (baik manual maupun via import) secara otomatis memiliki status **Aktif** (`active`).
- Status yang didukung sistem: `active` (Aktif), `withdrawn` (Mutasi / Pindah), `graduated` (Alumni / Lulus), dan `inactive` (Nonaktif).
- Hanya siswa dengan status `active` yang menjadi target penerbitan tagihan otomatis.

### 3.3 Aturan Import Siswa Massal Native Excel (.xlsx)
- Berkas template wajib dalam format Microsoft Excel asli (`.xlsx`) bernama `template_import_siswa_mts_miftahul_ulum.xlsx`.
- Kolom `NISM`, `Tingkat Kelas`, dan `Tahun Masuk` diformat sebagai Number (0 Desimal).
- Kolom `NISN` dan `No. WhatsApp / HP Orang Tua` diformat sebagai Teks untuk menjaga integritas digit nol di depan.
- Parser import wajib memproses teks numerik bersih untuk menghindari notasi ilmiah `e+17`.
- Sistem secara otomatis membuatkan akun login wali santri (*auto-provisioning parent user*) berdasarkan nomor telepon/email siswa yang diimpor. Jika orang tua memiliki beberapa anak (saudara kandung / *siblings*), akun digabungkan ke 1 profil wali.

### 3.4 Transisi Tahun Ajaran Baru & Kenaikan Kelas
- Form pembuatan tahun ajaran baru hanya memerlukan input `start_date` dan `end_date`. Nama tahun ajaran (contoh: `2026/2027`) digenerate otomatis.
- Saat tahun ajaran baru diaktifkan:
  - Siswa Kelas 7 aktif otomatis naik ke Kelas 8.
  - Siswa Kelas 8 aktif otomatis naik ke Kelas 9.
  - Siswa Kelas 9 aktif otomatis diubah statusnya menjadi Lulus (`graduated`).
  - Pengecualian tinggal kelas dicatat via repeater khusus dan tidak dinaikkan tingkatnya.
  - Seluruh riwayat kenaikan dicatat ke tabel `student_academic_years`.

### 3.5 Aturan Mutasi Siswa (Siswa Pindah / Keluar)
- Eksekusi mutasi siswa keluar (`Student::mutateOut($reason, $date)`):
  - Mengubah status siswa dari `active` menjadi `withdrawn`.
  - Mengubah status seluruh tagihan yang belum lunas (`unpaid` dan `overdue`) menjadi `cancelled` (`outstanding_amount = 0`) dengan melampirkan catatan alasan mutasi.
  - Seluruh riwayat tagihan dan pembayaran yang telah lunas (`paid`) **wajib dikunci dan dipertahankan** untuk kebutuhan audit dan rekapitulasi laporan tahunan akuntan yayasan.
  - Generator tagihan massal SPP bulanan mendatang secara otomatis mengecualikan siswa berstatus `withdrawn`.
  - Jika terjadi kesalahan administrasi, tersedia aksi `[ Aktifkan Kembali ]` (`Student::revertMutation()`) untuk mengembalikan status siswa menjadi `active`.
  - Setiap mutasi dan aktivasi kembali wajib dicatat secara lengkap pada `audit_logs`.

---

# 4. Aturan Pos Pembayaran & Tarif Resmi (11 Kategori)

Sistem mengunci 11 kategori pos pembayaran resmi beserta nominal bakunya:

1. **SPP Bulanan**: Rp 75.000 / bulan (Tingkat 7, 8, 9).
2. **Pendaftaran Siswa Baru (PPDB)**: Rp 1.190.000 (Satu kali bayar, khusus Kelas 7 baru).
3. **Daftar Ulang Tahun Ajaran Baru Siswa Kelas 8 dan 9**: Rp 440.000 (Tahunan, khusus Kelas 8 dan 9).
4. **ASTS / PTS Ganjil**: Rp 75.000 (Semester Ganjil, Tingkat 7, 8, 9).
5. **ASAS Ganjil**: Rp 150.000 (Semester Ganjil, Tingkat 7, 8, 9).
6. **LDKS (Latihan Dasar Kepemimpinan Siswa)**: Rp 450.000 (Satu kali bayar, Tingkat 7 / 8).
7. **Daftar Ulang Semester Genap**: Rp 440.000 (Semester Genap, Tingkat 7, 8, 9).
8. **ASTS / PTS Genap**: Rp 75.000 (Semester Genap, Tingkat 7, 8, 9).
9. **ASATA / PAT**: Rp 150.000 (Semester Genap, Tingkat 7, 8, 9).
10. **Study Tour**: Rp 450.000 (Satu kali bayar, Tingkat 8 / 9).
11. **Akhir Tahun (Pelepasan & Wisuda)**: Rp 2.000.000 (Satu kali bayar, khusus Kelas 9).

### 4.1 Prioritas Penentuan Tarif (Price Resolution Hierarchy)
```text
1. Class Level + Rombel Price Matrix (Matriks Tarif Kelas & Rombel Spesifik)
2. Class Level Price Matrix (Matriks Tarif per Tingkat Kelas)
3. Academic Year Default Price (Tarif Bawaan Tahun Ajaran)
4. Payment Type Default Price (Tarif Bawaan Master Pos Biaya)
```

---

# 5. Aturan Penagihan (Billing) & Transaksi Kasir

### 5.1 Idempotensi Pembuatan Tagihan
- Sistem dilarang menerbitkan dua tagihan untuk siswa yang sama pada pos pembayaran dan periode/bulan yang sama.
- Generator tagihan massal wajib memeriksa duplikasi sebelum melakukan `insert`.

### 5.2 Skema Cicilan (Sistem A)
- Pemecahan tagihan bernilai besar menjadi $N$ tenor angsuran wajib memenuhi rumus:
  $$\sum_{i=1}^{N} \text{Installment}_i = \text{Total Nominal}$$
- Sisa pembulatan (*rounding remainder*) wajib dialokasikan ke angsuran tenor terakhir.

### 5.3 Integritas Kasir & Anti-Overpayment
- Kasir menolak transaksi pembayaran jika nominal yang dibayarkan melebihi sisa tunggakan tagihan:
  $$\text{Nominal Bayar} \le (\text{Total Tagihan} - \text{Total Sudah Dibayar})$$
- Jika tagihan telah terbayar penuh, status tagihan otomatis menjadi `paid`.

### 5.4 Perhitungan Status Tagihan
- `paid`: Sisa tunggakan $\le 0$.
- `overdue`: Sisa tunggakan $> 0$ dan $\text{Tanggal Hari Ini} > \text{Jatuh Tempo}$.
- `unpaid`: Sisa tunggakan $> 0$ dan $\text{Tanggal Hari Ini} \le \text{Jatuh Tempo}$.
- `cancelled`: Dibatalkan oleh sistem (akibat siswa mutasi keluar atau pembatalan sah).

---

# 6. Aturan Dokumen PDF & Tanda Tangan Bendahara

### 6.1 Tanda Tangan Digital Bendahara & Historical Snapshot (Document Immutability)
- Nama resmi dan tanda tangan digital bendahara dikelola di `/admin/profile`.
- **Aturan Immutabilitas Dokumen (Historical Snapshotting)**:
  - Setiap kali Invoice atau Kuitansi baru dibuat, sistem wajib melakukan **snapshot** permanen atas `treasurer_name` dan `treasurer_signature_path` dari profil bendahara yang sedang aktif ke tabel `invoices` dan `receipts`.
  - Dokumen masa lalu yang terbit saat masa jabatan bendahara terdahulu (misal: "Bu Putri") **wajib selamanya mencetak nama dan tanda tangan Bu Putri** tanpa terpengaruh perubahan profil di masa mendatang.
  - Dokumen baru yang terbit setelah pergantian bendahara baru (misal: "Pak Putra") otomatis mengunci nama dan tanda tangan Pak Putra.
  - Gambar tanda tangan dirender menggunakan format Base64 (`$receipt->getSignatureBase64()` / `$invoice->getSignatureBase64()`) untuk performa instan dan rendering bebas hambatan pada DomPDF.

### 6.2 Nomor Dokumen Unik
- Nomor Invoice: `INV-{NIS}-{YYYYMM}-{RAND}`
- Nomor Kuitansi Kasir Manual: `PAY-MAN-{NIS}-{YYYYMMDD}-{RAND}`
- Nomor Kuitansi Midtrans: `PAY-MID-{NIS}-{YYYYMMDD}-{RAND}`

---

# 7. Aturan Notifikasi WhatsApp 1-Klik (`wa.me`)

- Tombol kirim WhatsApp tidak membebani biaya API langganan pihak ketiga, melainkan menyusun link `https://wa.me/{phone}?text={encoded_message}`.
- Nomor tujuan dinormalisasi otomatis ke format internasional `628xx`.
- Seluruh pengiriman pesan dicatat ke tabel `whatsapp_logs` untuk audit penagihan.

---

# 8. Aturan Uji Otomatis & Pemeliharaan Kode

- Seluruh alur kerja sistem wajib tercover oleh rangkaian pengujian otomatis (*Automated Test Suite*): **49 Tests Passing 100% (185 Assertions)**.
- Kode PHP wajib diformat mengikuti standar PSR-12 menggunakan **Laravel Pint** (156 files clean).