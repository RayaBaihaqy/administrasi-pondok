# 📖 Katalog Lengkap Fitur Sistem Administrasi & Pembayaran
### MTs. Miftahul 'Ulum (Yayasan Perguruan Islam Miftahul 'Ulum)

> Dokumen ini merangkum seluruh fitur sistem secara terstruktur dalam bentuk penomoran berurutan, yang dikelompokkan ke dalam **3 Hak Akses / Bagian**:
> 1. [👑 Bagian 1: Fitur Role Super Admin (Kepala Yayasan / Kepala Bendahara)](#-bagian-1-fitur-role-super-admin-kepala-yayasan--kepala-bendahara)
> 2. [💼 Bagian 2: Fitur Role Admin (Operator Keuangan / Kasir Bendahara)](#-bagian-2-fitur-role-admin-operator-keuangan--kasir-bendahara)
> 3. [👨‍👩‍👧‍👦 Bagian 3: Fitur Role Users (Orang Tua / Wali Santri)](#-bagian-3-fitur-role-users-orang-tua--wali-santri)

---

## 👑 Bagian 1: Fitur Role Super Admin (Kepala Yayasan / Kepala Bendahara)

Super Admin memiliki hak akses tertinggi di sistem dengan wewenang mengelola konfigurasi fundamental, tarif, kebijakan akademik, audit keamanan, profil & tanda tangan bendahara, serta seluruh fitur operasional admin.

1. **Dashboard Eksekutif & Analitik Keuangan Real-Time**
   * Monitoring total pendapatan madrasah (bulan ini, tahun ini, dan per rentang waktu).
   * Monitoring total tunggakan aktif (*outstanding balance*) seluruh siswa.
   * Grafik tren penerimaan SPP bulanan dan grafik pendapatan non-SPP.
   * Diagram komposisi pendapatan per pos pembayaran dan per metode bayar (Tunai vs Gateway Midtrans).
   * Tabel pantauan 5 transaksi pembayaran terbaru dan daftar tagihan mendesak.

2. **Manajemen Profil & Tanda Tangan Digital Bendahara (Halaman Profile `/admin/profile`)**
   * Pembaruan Nama Resmi Bendahara dan unggah Tanda Tangan Digital (format PNG/JPG transparan).
   * **Auto-Redirect ke Dashboard**: Setelah data profil bendahara berhasil disimpan, sistem otomatis mengarahkan admin kembali ke **Dashboard Utama** (`/admin`).
   * Integrasi otomatis tanda tangan digital dan nama bendahara ke seluruh dokumen PDF yang di-generate (Invoice Tagihan dan Kuitansi Pembayaran Sah).
   * Mendukung pergantian pejabat bendahara kapan saja tanpa mengubah kode program.

3. **Manajemen Master Tahun Ajaran & Status Periode Aktif**
   * Pembuatan dan pengelolaan data tahun ajaran baru secara intuitif (cukup input Tanggal Mulai dan Tanggal Selesai, nama tahun ajaran seperti `2026/2027` digenerate otomatis).
   * Penetapan tanggal mulai (*start date*) dan tanggal berakhir (*end date*) tahun ajaran.
   * Fitur aktivasi 1 tahun ajaran berjalan secara otomatis menonaktifkan tahun ajaran sebelumnya.

4. **Kenaikan Kelas Massal & Transisi Tahun Ajaran Baru**
   * Eksekusi kenaikan tingkat otomatis seluruh siswa aktif (Kelas 7 naik ke Kelas 8, Kelas 8 naik ke Kelas 9).
   * Kelulusan otomatis (*auto-graduation*) bagi seluruh siswa Kelas 9 menjadi status Lulus (`graduated`).
   * Form *repeater* dinamis pengecualian untuk siswa tinggal kelas yang dilengkapi fitur pencarian terkelompok per kelas.
   * Pencatatan riwayat penempatan kelas siswa di tabel histori akademik (`student_academic_years`).

5. **Akses & Monitoring Audit Trail Sistem (Eksklusif Super Admin)**
   * Rekam jejak lengkap seluruh aktivitas administratif dan finansial di sistem (termasuk mutasi siswa, pembatalan tagihan, dan kenaikan kelas).
   * Mencatat waktu kejadian, nama pelaku (*actor*), role, tipe entitas data (*auditable type*), ID data, IP address, dan User Agent browser.
   * Perbandingan visual perubahan data sebelum (*old values*) dan sesudah (*new values*).
   * Bersifat *immutable* (tidak dapat diedit/dihapus oleh siapapun) untuk kepatuhan audit transparansi.

6. **Konfigurasi Master Jenis Pembayaran & Matriks Tarif Resmi Klien (11 Kategori Biaya)**
   * Pengelolaan 11 kategori biaya resmi madrasah: 
     1. **SPP Bulanan**: Rp 75.000 / bulan
     2. **Pendaftaran Siswa Baru (PPDB)**: Rp 1.190.000 (Satu Kali Bayar)
     3. **Daftar Ulang Tahun Ajaran Baru Siswa Kelas 8 dan 9**: Rp 440.000 (Tahunan)
     4. **ASTS / PTS Ganjil**: Rp 75.000 (Semester Ganjil)
     5. **ASAS Ganjil**: Rp 150.000 (Semester Ganjil)
     6. **LDKS (Latihan Dasar Kepemimpinan Siswa)**: Rp 450.000 (Satu Kali Bayar)
     7. **Daftar Ulang Semester Genap**: Rp 440.000 (Semester Genap)
     8. **ASTS / PTS Genap**: Rp 75.000 (Semester Genap)
     9. **ASATA / PAT**: Rp 150.000 (Semester Genap)
     10. **Study Tour**: Rp 450.000 (Satu Kali Bayar)
     11. **Akhir Tahun (Pelepasan & Wisuda)**: Rp 2.000.000 (Kelas 9)
   * Fleksibilitas penentuan tarif per jenjang kelas (7, 8, 9) dan rombel (*class level + rombel price matrix*).

7. **Pengaturan Dispensasi Jatuh Tempo Khusus Siswa (Due Date Override)**
   * Penetapan tanggal jatuh tempo khusus per siswa berdasarkan kesepakatan tertulis dispensasi dengan pihak madrasah.
   * Override otomatis menggantikan tanggal jatuh tempo default (tgl 10) saat tagihan diterbitkan.

8. **Manajemen Akun Pengguna & Hak Akses Staff**
   * Pengelolaan akun login staf internal (Super Admin dan Admin).
   * Dukungan autentikasi ganda (Email atau Nomor HP) dengan pesan kesalahan yang informatif dan terisolasi.
   * Pengaturan kata sandi terenkripsi Bcrypt (12 rounds) dan otorisasi panel `/admin`.

9. **Pusat Konfigurasi Identitas Lembaga & Rekening Bank Yayasan**
   * Pengaturan terpusat data nama yayasan, nama madrasah, alamat lengkap, dan nomor rekening resmi (`config/school.php`).
   * Konfigurasi rekening bank resmi: Bank BRI, No. Rekening `176901000210569`, A/N *Madrasah Tsanawiyah Miftahul Ulum*.

10. **Konfigurasi Integrasi Payment Gateway Midtrans**
    * Pengaturan Server Key, Client Key, Merchant ID, mode Sandbox / Production, dan aktivasi fitur 3D-Secure (3DS).

11. **Ekspor & Unduh Laporan Rekapitulasi Keuangan Tingkat Eksekutif**
    * Unduh Rekap Pemasukan dan Rekap Tunggakan dalam format PDF Berstempel resmi dan CSV Clean untuk laporan rapat yayasan dan komite sekolah.

---

## 💼 Bagian 2: Fitur Role Admin (Operator Keuangan / Kasir Bendahara)

Admin bertugas menangani operasional harian madrasah, mencakup pengelolaan data siswa, penerbitan tagihan, penerimaan pembayaran loket kasir, pengiriman notifikasi WhatsApp, dan penyusunan laporan keuangan.

1. **Dashboard Operasional Keuangan Harian**
   * Memantau daftar tagihan jatuh tempo terdekat yang belum dibayar (*Unpaid Bills Widget*).
   * Memantau riwayat transaksi pembayaran masuk secara *real-time*.

2. **Manajemen Data Siswa / Santri Lengkap & Rombel Dinamis**
   * Pengelolaan data santri: NISN (unik), NISM, Nama Lengkap, Jenis Kelamin, Tanggal Lahir, Tingkat Kelas (7, 8, 9), Rombel Dinamis (Kelas 7: 7.1-7.3, Kelas 8: 8.1-8.4, Kelas 9: 9.1-9.4), Tahun Masuk, Status Default Aktif (`active`), Email, No. HP, dan Alamat.
   * Relasi langsung dengan akun orang tua/wali siswa (termasuk dukungan multi-anak / *siblings* dengan nomor kontak orang tua yang sama).

3. **Manajemen Mutasi Siswa (Pindah / Keluar) & Pembatalan Tagihan Otomatis**
   * **Tombol Aksi Cepat `[ Mutasi / Pindah ]`**: Mengubah status siswa aktif menjadi Keluar / Pindah (`withdrawn`).
   * **Dialog Konfirmasi Cerdas**: Menampilkan jumlah dan total nominal tagihan belum lunas yang akan dibatalkan, serta input tanggal mutasi dan alasan / sekolah tujuan.
   * **Auto-Cancel Tagihan Belum Lunas**: Semua tagihan berstatus `unpaid` atau `overdue` otomatis diubah statusnya menjadi `cancelled` (`outstanding_amount = 0`) dengan catatan alasan mutasi.
   * **Integritas Pembukuan Terjaga**: Seluruh tagihan dan pembayaran yang sudah lunas (`paid`/`success`) tetap dikunci dan tidak terhapus untuk kebutuhan audit dan laporan tahunan akuntan.
   * **Aksi `[ Aktifkan Kembali ]`**: Mengembalikan status siswa menjadi aktif jika terjadi kekeliruan mutasi.

4. **Header Tabs Filter Status Siswa**
   * Tab filter di atas tabel siswa:
     - `🟢 Siswa Aktif` *(Default)*: Hanya menampilkan siswa aktif.
     - `🟠 Siswa Mutasi / Pindah`: Menampilkan arsip siswa keluar/pindah.
     - `🔵 Alumni / Lulus`: Menampilkan siswa yang telah lulus.
     - `Semua Siswa`: Menampilkan seluruh database siswa.
   * Dilengkapi badge penghitung (*counter badge*) jumlah siswa secara dinamis.

5. **Import Data Siswa Massal dari File Excel Asli (.xlsx)**
   * Upload file data siswa massal via spreadsheet Excel native (`.xlsx`) menggunakan template resmi `template_import_siswa_mts_miftahul_ulum.xlsx`.
   * Format kolom presisi: Nomor (0 Desimal) untuk NISM, Tingkat Kelas, dan Tahun Masuk; Teks untuk NISN dan No. Telepon; Bebas notasi ilmiah `e+17`.
   * Validasi otomatis duplikasi NISN, format rombel sesuai tingkat, dan pembuatan otomatis akun login orang tua/wali (*auto-provisioning parent account*).
   * Tombol unduh Template Excel resmi langsung dari panel admin.

6. **Manajemen Data & Profil Orang Tua / Wali Siswa**
   * Pengelolaan profil wali santri: Nama Lengkap, No. Telepon / WhatsApp, Email Kontak Utama, dan Alamat Lengkap.
   * Pengelolaan akun login wali murid untuk akses Portal Wali (`/portal`) via Email atau No. Telepon.
   * Penanganan satu akun wali untuk beberapa anak kandung (*multi-children parent account*).

7. **Penerbitan Tagihan Massal Cerdas (Bulk Bill Generator)**
   * Pembuatan tagihan sekaligus dengan 3 opsi target:
     - Seluruh Siswa Aktif.
     - Per Angkatan / Tingkat Kelas (misal: Khusus Kelas 7 saja).
     - Per Rombel Spesifik (misal: Khusus Kelas 7.1 saja).
   * Proteksi *Idempotency*: Mencegah pembuatan tagihan duplikat jika tagihan untuk periode dan siswa tersebut sudah pernah dibuat.

8. **Penerbitan Tagihan Satuan / Individual**
   * Form pembuatan tagihan per siswa dengan fitur *auto-fill* nominal tarif resmi berdasarkan kelas dan jenis pembayaran yang dipilih.

9. **Penerbitan Tagihan dengan Skema Cicilan (Sistem A)**
   * Fitur pemecahan tagihan bernilai besar menjadi beberapa kali cicilan bulanan (pilihan tenor 2 s/d 36 bulan).
   * Kalkulasi otomatis pembagian nominal angsuran dengan penanganan pembulatan presisi (*rounding adjustment* pada tenor terakhir).
   * Otomatis menetapkan tanggal jatuh tempo bertahap per bulan secara berurutan.
   * Dilengkapi *live preview* estimasi nominal tagihan per bulan.

10. **Loket Kasir Pembayaran Tunai & Bank Manual (Offline Cashier)**
    * Tombol aksi cepat **"Bayar Manual / Kasir"** langsung dari baris tabel tagihan.
    * Mendukung pelunasan langsung maupun pembayaran sebagian / cicilan.
    * Proteksi *anti-overpayment* (sistem menolak nominal pembayaran yang melebihi sisa tunggakan).
    * Kalkulasi otomatis sisa tagihan (*outstanding amount*) dan pembaruan status tagihan menjadi Lunas (`paid`).

11. **Upload & Pengarsipan Bukti Pembayaran (Payment Evidence)**
    * Fitur unggah berkas bukti transfer fisik / resi ATM / slip setoran bank yang diserahkan wali saat membayar di loket.

12. **Penerbitan Kuitansi PDF Sah Berstempel Digital & Tanda Tangan Bendahara**
    * Pembuatan kuitansi resmi otomatis setiap kali transaksi berhasil tercatat.
    * Nomor kuitansi unik (`PAY-MAN-{NIS}-{YYYYMMDD}-{RAND}`).
    * Memuat cap stempel bulat digital madrasah, nama & tanda tangan dinamis bendahara, rincian pembayaran, dan kalimat terbilang nominal rupiah.
    * Tersedia aksi cetak langsung (*stream*) dan unduh berkas PDF (*download*).

13. **Penerbitan Invoice Tagihan PDF Resmi Berkop Surat**
    * Dokumen penagihan resmi dengan format nomor standar (`INV-{NIS}-{YYYYMM}-{RAND}`).
    * Berisi rincian biaya, identitas santri, instruksi transfer rekening resmi BRI, watermark status tagihan, serta nama & tanda tangan bendahara.

14. **Pengiriman Notifikasi WhatsApp 1-Klik (`wa.me`) Bebas Biaya Langganan**
    * Tombol aksi **"Kirim WA"** di baris tagihan dan pembayaran yang langsung membuka WhatsApp Web / Aplikasi WhatsApp.
    * Otomatis memuat pesan berstandar resmi institusi dengan salam islami, rincian biaya, batas waktu, dan tautan unduh dokumen PDF resmi.
    * Normalisasi otomatis nomor handphone lokal (`08xx`, `+628xx`, `628xx`).

15. **Monitoring Riwayat Log WhatsApp (WhatsApp Logs)**
    * Tabel riwayat seluruh notifikasi WhatsApp yang disiapkan dan dikirimkan sistem.
    * Mencatat nama penerima, nomor telepon, jenis pesan (Tagihan Baru, Pengingat H-2, Pembayaran Sukses), nominal, waktu kirim, dan isi pesan lengkap.

16. **Laporan Riwayat Pembayaran (Payment Report)**
    * Audit tabel seluruh transaksi pembayaran sukses.
    * Filter berdasarkan metode pembayaran (Midtrans vs Kasir Tunai), rentang tanggal transaksi, dan pencarian siswa.

17. **Laporan Pemasukan (Revenue Report)**
    * Rekapitulasi total dana yang masuk per pos anggaran.
    * Breakdown persentase pemasukan dari pembayaran online Midtrans vs kasir manual.
    * Filter periode cepat (*Bulan Ini, Bulan Kemarin, 3 Bulan, 6 Bulan, Tahun Ini, Custom Tanggal*).
    * Tombol ekspor dokumen: **Download PDF Rekap** dan **Download CSV/Excel Clean**.

18. **Laporan Tunggakan Tagihan (Outstanding Report)**
    * Monitoring seluruh siswa yang belum menyelesaikan kewajiban administrasi.
    * Pemilahan status tagihan: Belum Lunas (belum jatuh tempo) vs Terlambat / Overdue (melewati tanggal tempo).
    * Filter rentang tanggal jatuh tempo dengan indikator badge aktif.
    * Tombol ekspor dokumen: **Download PDF Rekapitulasi Tunggakan** dan **Download CSV/Excel**.

19. **Eksekusi Perintah Otomasi (Scheduled Artisan Commands)**
    * `php artisan spp:generate`: Menjalankan generate tagihan bulanan secara terjadwal.
    * `php artisan bills:detect-overdue`: Mendeteksi dan memperbarui status tagihan menjadi *overdue*.
    * `php artisan bills:send-reminders`: Menyiapkan log pengingat WhatsApp H-2 jatuh tempo secara otomatis.

---

## 👨‍👩‍👧‍👦 Bagian 3: Fitur Role Users (Orang Tua / Wali Santri)

Fitur pada Portal Mandiri Wali Siswa (`/portal`) yang dirancang khusus untuk kemudahan orang tua santri dalam memantau kewajiban pendidikan anak dan melakukan pembayaran online secara mandiri dari HP maupun komputer.

1. **Akses Mandiri Portal Wali Siswa (`/portal`) & Dual Identifier Login**
   * Halaman login mandiri khusus wali santri dengan tampilan responsif bertema *Emerald Green*.
   * Dapat login menggunakan **Email** maupun **Nomor Telepon/WhatsApp** (`08xx`, `+628xx`, `628xx`).
   * Terpisah secara aman dari panel internal staf sekolah (`/admin`).

2. **Isolasi Keamanan Data Anak Kandung (Multi-Tenant Parent Isolation)**
   * Otorisasi ketat berbasis session orang tua: Wali murid **hanya dapat melihat dan mengakses data putra/putri kandungnya sendiri** dan tidak dapat melihat data siswa lain.

3. **Dashboard Ringkasan Finansial Santri**
   * **Kartu Ringkasan Tunggakan**: Menampilkan total sisa tagihan pendidikan yang belum diselesaikan.
   * **Kartu Informasi Anak**: Menampilkan jumlah putra/putri yang terdaftar aktif di madrasah.
   * **Kartu Rekening Resmi Madrasah**: Informasi nomor rekening Bank BRI yayasan untuk kemudahan transfer langsung.

4. **Tabel Interaktif Tagihan Aktif & Jatuh Tempo**
   * Menampilkan seluruh daftar tagihan aktif (SPP bulanan, uang ujian, daftar ulang, dll.).
   * Menampilkan informasi nama anak, jenis pembayaran, periode, tanggal jatuh tempo, sisa tagihan, dan badge status (Belum Lunas / Terlambat).

5. **Pembayaran Online 1-Klik via Midtrans Snap Popup**
   * Tombol **"Bayar Online"** langsung pada tabel tagihan dashboard portal.
   * Membuka popup pembayaran Midtrans Snap di atas layar tanpa meninggalkan halaman portal.
   * Pilihan metode bayar online instan:
     - **QRIS** (GoPay, OVO, DANA, ShopeePay, LinkAja, BCA QR, dll.).
     - **Virtual Account (VA)** (BRI, BCA, BNI, Mandiri, Permata).
     - **Kartu Kredit / Debit Online** (Visa, Mastercard, JCB).
     - **Gerai Retail** (Indomaret, Alfamart).
   * Status pembayaran langsung terverifikasi secara *real-time* via webhook otomatis.

6. **Unduh Dokumen Invoice Tagihan PDF Resmi**
   * Tombol **"Invoice PDF"** pada setiap baris tagihan untuk mengunduh surat tagihan resmi berkop madrasah, berstempel digital, dan bertanda tangan bendahara.

7. **Menu "Anak Saya" (Profil Lengkap Santri)**
   * Menampilkan daftar profil putra/putri yang terdaftar di MTs Miftahul 'Ulum.
   * Rincian NISN, NISM, Nama Lengkap, Jenis Kelamin, Kelas & Rombel (misal: Kelas 7.1), serta status keaktifan di madrasah.

8. **Menu "Riwayat Pembayaran" (Arsip Seluruh Transaksi Lunas)**
   * Menampilkan daftar riwayat seluruh transaksi pembayaran yang pernah dilakukan (baik via online Midtrans maupun via loket kasir tunai sekolah).
   * Rincian nomor transaksi, nama siswa, pos tagihan, nominal yang dibayarkan, metode pembayaran, dan waktu pembayaran berhasil.

9. **Unduh Dokumen Kuitansi Sah PDF Berstempel Resmi**
   * Tombol **"Bukti Bayar PDF"** pada setiap transaksi lunas untuk mengunduh kuitansi resmi bertanda tangan bendahara dan bercap stempel basah digital sebagai bukti pembayaran yang sah.

10. **Akses Cepat Dokumen Publik via Tautan WhatsApp Tanpa Login**
    * Orang tua dapat langsung membuka dan mengunduh invoice (`/docs/invoice/{no}`) atau kuitansi sah (`/docs/receipt/{no}`) langsung dari link aman yang dikirimkan oleh pihak madrasah melalui WhatsApp.

---

*Dokumen ini merupakan katalog resmi dan terlengkap dari seluruh fungsionalitas Sistem Informasi Administrasi & Pembayaran MTs. Miftahul 'Ulum.*
