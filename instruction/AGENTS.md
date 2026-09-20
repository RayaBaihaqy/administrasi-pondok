# AGENTS.md — Panduan & Standar Pengembang AI (AI Coding Agent Guidelines)
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> **Development Guidelines, Architectural Invariants, & Testing Rules**
>
> Dokumen ini merupakan panduan kerja wajib bagi AI Coding Agent dan pengembang dalam memelihara, menguji, dan mengembangkan Sistem Administrasi & Pembayaran MTs. Miftahul 'Ulum.

---

# 1. Dokumen & Spesifikasi Inti

Sebelum melakukan perubahan kode pada repository ini, Agent wajib memahami struktur dokumen terkait:
- [PRD.md](file:///c:/Projects/administrasi-pondok/instruction/PRD.md): Dokumen Kebutuhan Produk & Fungsionalitas.
- [ARCHITECTURE.md](file:///c:/Projects/administrasi-pondok/instruction/ARCHITECTURE.md): Arsitektur Teknis, Layering, & Integrasi.
- [SCHEMA.md](file:///c:/Projects/administrasi-pondok/instruction/SCHEMA.md): Spesifikasi 25 Tabel & Relasi Database.
- [DESIGN.md](file:///c:/Projects/administrasi-pondok/instruction/DESIGN.md): Standar UI/UX, Tema Emerald Green, & Visual Status.
- [RULES.md](file:///c:/Projects/administrasi-pondok/instruction/RULES.md): Aturan Bisnis, Otorisasi, & Integritas Finansial.

---

# 2. Tech Stack & Environment Invariants

| Layer / Tool | Library / Versi | Aturan Penggunaan |
|---|---|---|
| Backend | Laravel 12.x (PHP 8.2+) | Gunakan Eloquent ORM, Form Requests, dan Service Classes |
| Admin UI | Filament 5.x | Pertahankan konfigurasi panel Filament pada `app/Filament` |
| Parent Portal | Blade + Tailwind CSS | Gunakan tema Emerald Green (`emerald-600`) |
| Excel Engine | PhpSpreadsheet (`ext-zip`) | Wajib format sel eksplisit (Number 0 desimal untuk ID/angka) |
| PDF Engine | DomPDF | Render via Blade template dengan Base64 image encoding |
| Linter | Laravel Pint | Jalankan `./vendor/bin/pint` sebelum menyelesaikan task |
| Test Suite | Pest PHP / PHPUnit | Wajib 100% lulus (**48 Tests, 175 Assertions**) |

---

# 3. Batasan Domain & Invarian Bisnis yang Tidak Boleh Dilanggar

### 3.1 Pos Pembayaran Resmi (11 Kategori)
Agent **dilarang** mengubah, menghapus, atau mengarang nominal pos pembayaran baru di luar 11 pos resmi madrasah:
1. `SPP`: Rp 75.000 / bulan
2. `PPDB`: Rp 1.190.000 (Satu kali)
3. `DU_TA_8_9`: Rp 440.000 (Tahunan Kelas 8 & 9)
4. `ASTS_GANJIL`: Rp 75.000 (Semester Ganjil)
5. `ASAS_GANJIL`: Rp 150.000 (Semester Ganjil)
6. `LDKS`: Rp 450.000 (Satu kali)
7. `DU_GENAP`: Rp 440.000 (Semester Genap)
8. `ASTS_GENAP`: Rp 75.000 (Semester Genap)
9. `ASATA_PAT`: Rp 150.000 (Semester Genap)
10. `STUDY_TOUR`: Rp 450.000 (Satu kali)
11. `AKHIR_TAHUN`: Rp 2.000.000 (Satu kali Kelas 9)

### 3.2 Tidak Ada Fitur Beasiswa (No Scholarship)
Fitur Beasiswa telah dihapus secara permanen dari sistem. Agent **dilarang** menambahkan kembali model, migrasi, atau UI beasiswa.

### 3.3 Tingkat Kelas & Rombel Dinamis
- Kelas 7: Rombel `7.1`, `7.2`, `7.3`
- Kelas 8: Rombel `8.1`, `8.2`, `8.3`, `8.4`
- Kelas 9: Rombel `9.1`, `9.2`, `9.3`, `9.4`
- Form dropdown rombel wajib bersifat cascading dari tingkat kelas yang dipilih.

### 3.4 Status Default Siswa
Saat data siswa dibuat atau diimpor, status wajib default ke `active` tanpa memaksa pengguna memilih status secara manual.

### 3.5 Transisi Tahun Ajaran Baru
Pembuatan tahun ajaran baru cukup menerima `start_date` dan `end_date`. Nama tahun ajaran (contoh: `2026/2027`) digenerate otomatis oleh model/service.

### 3.6 Profil & Tanda Tangan Digital Bendahara
Halaman Profile staf di `/admin/profile` (label "Profile") mengelola `name` dan `signature_path` pada tabel `users`.
Setelah penyimpanan profil berhasil, sistem mengarahkan admin kembali ke dashboard utama (`/admin`).
Dokumen PDF (`receipt.blade.php` & `invoice.blade.php`) secara dinamis mengambil data bendahara aktif via `User::getActiveTreasurer()->getSignatureBase64()`.

### 3.7 Autentikasi Ganda (Dual Identifier Login)
Login mendukung Email atau Nomor Telepon (`08xx`, `+628xx`, `628xx`) dengan pesan error terisolasi:
- `"Email tidak ditemukan."` jika format email tidak terdaftar.
- `"Nomor telepon tidak ditemukan."` jika format nomor telepon tidak terdaftar.
- `"Password salah."` jika identitas terdaftar namun password tidak cocok.

### 3.8 Import Siswa Massal Native Excel (.xlsx)
- Generator template menghasilkan file asli `.xlsx` menggunakan `PhpOffice\PhpSpreadsheet\Spreadsheet`.
- NISM, Tingkat Kelas, dan Tahun Masuk diformat sebagai Number (0 Desimal).
- NISN dan No. Telepon diformat sebagai Teks.
- Parser import wajib membaca nilai string numerik bersih untuk menghindari notasi ilmiah `e+17`.
- Penanganan akun orang tua untuk beberapa anak kandung (*siblings*) harus dihubungkan ke 1 entitas parent (`firstOrCreate`).

### 3.9 Manajemen Mutasi Siswa (Siswa Pindah)
- Mutasi siswa (`mutateOut`) mengubah status siswa menjadi `withdrawn`, membatalkan tagihan belum lunas (`unpaid`/`overdue` ➡️ `cancelled`), dan mengunci tagihan/kuitansi yang sudah lunas (`paid`) untuk laporan audit keuangan tahunan.
- Siswa `withdrawn` dilarang dimasukkan ke penerbitan tagihan SPP bulanan otomatis berikutnya.
- Log mutasi dan pengaktifan kembali dicatat ke `audit_logs`.

---

# 4. Prosedur Kerja & Verifikasi (Workflow)

Saat melakukan penambahan atau modifikasi fitur, ikuti urutan kerja:
1. **Pemeriksaan Dokumen**: Baca spesifikasi pada PRD, ARCHITECTURE, dan RULES.
2. **Implementasi Kode**: Lakukan perubahan yang bersih dan terisolasi.
3. **Penyelarasan Format**: Jalankan Laravel Pint:
   ```powershell
   ./vendor/bin/pint
   ```
4. **Eksekusi Pengujian Otomatis**: Jalankan suite pengujian:
   ```powershell
   php artisan test
   ```
   Pastikan seluruh **48 tests** berstatus hijau (PASS).
5. **Penyelarasan Dokumentasi**: Pastikan seluruh berkas `.md` tetap selaras dengan perubahan kode yang dilakukan.
