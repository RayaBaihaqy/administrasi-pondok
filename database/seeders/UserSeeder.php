<?php

namespace Database\Seeders;

use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. System Admin Accounts
        User::firstOrCreate([
            'email' => 'superadmin@pondok.test',
        ], [
            'name' => 'Kepala Bendahara (Super Admin)',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        User::firstOrCreate([
            'email' => 'admin@pondok.test',
        ], [
            'name' => 'Hj. Titi Nurhayati, S. Pd',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Data Master Siswa Nyata MTs Miftahul 'Ulum (Total 241 Siswa)
        $studentsData = [
            // ─── KELAS 7.1 (22 Siswa) ───────────────────────────
            ['name' => 'ABDUL AZMI', 'gender' => 'male', 'nisn' => '3144228827', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'AINA TALITA ZAHRAN', 'gender' => 'female', 'nisn' => '0138571201', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'AMORA SAYYIDATI ZAENAB', 'gender' => 'female', 'nisn' => '3147065065', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'AQILAH KHUMAIRA', 'gender' => 'female', 'nisn' => '3149793631', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'AZKA MAULA AFIDIN', 'gender' => 'male', 'nisn' => '3136037424', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'DAFFA ABYAN PRASETYA', 'gender' => 'male', 'nisn' => '3143198065', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'DARA AGNITA ABDULLAH', 'gender' => 'female', 'nisn' => '0149850479', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'DIAZ WIYAHYA RAMADHAN', 'gender' => 'male', 'nisn' => '3142648554', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'FAUZAN RIZKY RAMADHAN', 'gender' => 'male', 'nisn' => '3132171713', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'KALILA NAFISA PUTRI', 'gender' => 'female', 'nisn' => '0136611939', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'KYELATUN NURIZQIA ZAHRANI', 'gender' => 'female', 'nisn' => '0130773803', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'M. ANWAR FUADI', 'gender' => 'male', 'nisn' => '3131803563', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'MUHAMAD FAHMI KHAIRI RAHMAN', 'gender' => 'male', 'nisn' => '3132550108', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD FATHANSYAH ZEN', 'gender' => 'male', 'nisn' => '0137188681', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD RAYHAN AL GHIFARI', 'gender' => 'male', 'nisn' => '0148856959', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD RIZKY', 'gender' => 'male', 'nisn' => '0136383143', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'NAZAR WIJAYA', 'gender' => 'male', 'nisn' => '0136452682', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'NAJWA KHAIRA WILDA', 'gender' => 'female', 'nisn' => '3130918117', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'RAIHAN ADITYA SYAMSUDIN', 'gender' => 'male', 'nisn' => '0144067058', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'RELLA RELDIYANI RAHAYU PERMATA', 'gender' => 'female', 'nisn' => '3145605953', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'RIZKI ADI NUGRAHA', 'gender' => 'male', 'nisn' => '3139169567', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],
            ['name' => 'SITI CHUMAIROH', 'gender' => 'female', 'nisn' => '3140170913', 'nism' => null, 'class' => 7, 'rombel' => '1', 'entry_year' => 2026],

            // ─── KELAS 7.2 (22 Siswa) ───────────────────────────
            ['name' => 'AFIFAH SHOLIHAH', 'gender' => 'female', 'nisn' => '0131433187', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'AGUS BUDI UTOMO', 'gender' => 'male', 'nisn' => '3138101696', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'AISHA INTAN NUR\'AINI', 'gender' => 'female', 'nisn' => '3144777226', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'ASSYIFA APRILIA', 'gender' => 'female', 'nisn' => '3144770440', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'AZIREN FIRNANSYAH', 'gender' => 'male', 'nisn' => '0147164275', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'AZWAN AFRIJAL MAULANA', 'gender' => 'male', 'nisn' => '0136397067', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'AZZA AZKYA QOLBY', 'gender' => 'female', 'nisn' => '0131781834', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'DAVA SAPUTRA', 'gender' => 'male', 'nisn' => '3131273679', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'ELZHAFIRAH AZKA ARAHIWANG', 'gender' => 'female', 'nisn' => '3137730568', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'ERLAN BACHTIAR PRATAMA', 'gender' => 'male', 'nisn' => '0134226378', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'FAREEZ TSANY DAMARIO', 'gender' => 'male', 'nisn' => '3140832471', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'KEVIN RADIKA ADITYA', 'gender' => 'male', 'nisn' => '0136081006', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'KHANSAA HUMAIRAA NAZIFAA', 'gender' => 'female', 'nisn' => '0136101536', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'M. FAQIH AL BAEHAQI', 'gender' => 'male', 'nisn' => '3148140773', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD HANIF NAZMI', 'gender' => 'male', 'nisn' => '3138855850', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'MUHAMAD REZKY ADITIA', 'gender' => 'male', 'nisn' => '3138079678', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'MOHAMMAD SYAFIQ', 'gender' => 'male', 'nisn' => '3135517284', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'NABILA KHAIRINNISWA ANINDITA', 'gender' => 'female', 'nisn' => '3136691567', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'NINA SHAFIYAH', 'gender' => 'female', 'nisn' => '0141035656', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'RAFAEL DIKA PERMANA', 'gender' => 'male', 'nisn' => '3133414311', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'RASYID IQBAL NASRULLAH', 'gender' => 'male', 'nisn' => '3137919233', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],
            ['name' => 'SAKHIA APRIANI', 'gender' => 'female', 'nisn' => '3143910694', 'nism' => null, 'class' => 7, 'rombel' => '2', 'entry_year' => 2026],

            // ─── KELAS 7.3 (17 Siswa) ───────────────────────────
            ['name' => 'ABDUL ZAYYAN MALIK', 'gender' => 'male', 'nisn' => '3130821963', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'AKMAL FAJAR HAIKAL', 'gender' => 'male', 'nisn' => '3149769090', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'ANGGORO SYAMSUL', 'gender' => 'male', 'nisn' => '0148401089', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'ANNA ALTHA UFU NISSA', 'gender' => 'female', 'nisn' => '3140903196', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'ARDIAN SEPTI HIDAYAT', 'gender' => 'male', 'nisn' => '3123209929', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'ARSIL IKHWAN', 'gender' => 'male', 'nisn' => '3133491734', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD ARYA MAHARDIKA', 'gender' => 'male', 'nisn' => '3144181526', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD FERDI ADRIANSYAH', 'gender' => 'male', 'nisn' => '0135030853', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD HAFFIZ SYAH REZA', 'gender' => 'male', 'nisn' => '3134210018', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'MUHAMAD MAULANA GILANG RAMADHAN', 'gender' => 'male', 'nisn' => '0139387452', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'MUHAMAD AKBAR', 'gender' => 'male', 'nisn' => '3135231152', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'MUHAMMAD RADITYA RAHMAT', 'gender' => 'male', 'nisn' => '3144302965', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'NABIL NAJMI', 'gender' => 'male', 'nisn' => '3139108785', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'NAUFAL AZKA FADHILLAH', 'gender' => 'male', 'nisn' => '3148558270', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'PERI APRIANSYAH', 'gender' => 'male', 'nisn' => '0136802470', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'RISMA NUR SABILILLAH', 'gender' => 'female', 'nisn' => '3139094887', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],
            ['name' => 'SUKMA JATI', 'gender' => 'female', 'nisn' => '3131398186', 'nism' => null, 'class' => 7, 'rombel' => '3', 'entry_year' => 2026],

            // ─── KELAS 8.1 (18 Siswa) ───────────────────────────
            ['name' => 'ABDU AQILA ALBAR YUSUF', 'gender' => 'male', 'nisn' => '0124672749', 'nism' => '121232160048250018', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'AIDA FITRIYA', 'gender' => 'female', 'nisn' => '3127603763', 'nism' => '121232160048250020', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'ALFIANSYAH AZ ZAHRAN', 'gender' => 'male', 'nisn' => '3136592376', 'nism' => '121232160048250021', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'ASHIILATUL AMALIYAH', 'gender' => 'female', 'nisn' => '3131183169', 'nism' => '121232160048250022', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'CAHYA KAMILA RAMADHANI', 'gender' => 'female', 'nisn' => '3128121868', 'nism' => '121232160048250023', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'DAFFA ALKHALIFFI DZIKRI', 'gender' => 'male', 'nisn' => '3138343715', 'nism' => '121232160048250025', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'DZALVA SAVANA NAJA', 'gender' => 'female', 'nisn' => '0121412442', 'nism' => '121232160048250026', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'FAIRUZ NADHIR AMRULLOH', 'gender' => 'male', 'nisn' => '3150585313', 'nism' => '121232160048250028', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'FRITZIE AZFAR ALTHAF BAHTIAR', 'gender' => 'male', 'nisn' => '3133753968', 'nism' => '121232160048250029', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'ICHA AULIA PUTRI', 'gender' => 'female', 'nisn' => '0124177156', 'nism' => '121232160048250030', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'KAILA ZULFA SANTOSO', 'gender' => 'female', 'nisn' => '3126653192', 'nism' => '121232160048250031', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'MUHAMAD ABDULAH', 'gender' => 'male', 'nisn' => '0126996365', 'nism' => '121232160048250033', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD REZA ANUGERAH', 'gender' => 'male', 'nisn' => '3139601532', 'nism' => '121232160048250036', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'MUTHIA HERA SALSABILA', 'gender' => 'female', 'nisn' => '3129187948', 'nism' => '121232160048250037', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'NURZAHRA', 'gender' => 'female', 'nisn' => '3121815269', 'nism' => '121232160048250038', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'REYHAN', 'gender' => 'male', 'nisn' => '0122601835', 'nism' => '121232160048250039', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'RHEVAN CHAIRUL SYAHPUTRA', 'gender' => 'male', 'nisn' => '3123116070', 'nism' => '121232160048250040', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],
            ['name' => 'RICHARD RAIHAN AL BIRUNI', 'gender' => 'male', 'nisn' => '3126392957', 'nism' => '121232160048250041', 'class' => 8, 'rombel' => '1', 'entry_year' => 2025],

            // ─── KELAS 8.2 (20 Siswa) ───────────────────────────
            ['name' => 'ABY ASYROF KURNIAWAN', 'gender' => 'male', 'nisn' => '3125550150', 'nism' => '121232160048250042', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'AHMAD PRADITYA LA TAHZAN', 'gender' => 'male', 'nisn' => '3135829995', 'nism' => '121232160048250043', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'AIDA FIRZANA', 'gender' => 'female', 'nisn' => '3125974674', 'nism' => '121232160048250044', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'AULIA ADESWITA SARI', 'gender' => 'female', 'nisn' => '3113462930', 'nism' => '121232160048250046', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'EEP SYAIFULLAH', 'gender' => 'male', 'nisn' => '3123748856', 'nism' => '121232160048250049', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'FARRAS ARKANA SOMANTRI', 'gender' => 'male', 'nisn' => '3126032411', 'nism' => '121232160048250051', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'HABIB HAUZAN MA\'RIFATUDDIN HIDAYAT', 'gender' => 'male', 'nisn' => '3137194572', 'nism' => '121232160048250053', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'FEBY DIAN AZZAHRA', 'gender' => 'female', 'nisn' => '3138768146', 'nism' => '121232160048250052', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'IRA SYAHWA', 'gender' => 'female', 'nisn' => '0122400852', 'nism' => '121232160048250054', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'KAMALIA RAMADHAN', 'gender' => 'female', 'nisn' => '3126365454', 'nism' => '121232160048250055', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'MOH. KHOIRUL AZZAM', 'gender' => 'male', 'nisn' => '3135690524', 'nism' => '121232160048250056', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD SAKTI', 'gender' => 'male', 'nisn' => '0125636129', 'nism' => '121232160048250057', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD NIYAZ RIZQULLAH', 'gender' => 'male', 'nisn' => '3120553376', 'nism' => '121232160048250058', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'MUHAMAD YUSUF REYHAN', 'gender' => 'male', 'nisn' => '3132859137', 'nism' => '121232160048250059', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'MULYANA ADITYANTO', 'gender' => 'male', 'nisn' => '3138097082', 'nism' => '121232160048250060', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'MUTIARA SAKINAH RAMADHANI', 'gender' => 'female', 'nisn' => '0137310747', 'nism' => '121232160048250061', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'SALWA SAKILA', 'gender' => 'female', 'nisn' => '3129563147', 'nism' => '121232160048250063', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'RAHARDIAN BAGUS WICAKSONO', 'gender' => 'male', 'nisn' => '0125402932', 'nism' => '121232160048250062', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'SITI ZULAIKHA SALSABILLA', 'gender' => 'female', 'nisn' => '3134307124', 'nism' => '121232160048250064', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],
            ['name' => 'ZAHRA AULIA NUFUS', 'gender' => 'female', 'nisn' => '3133235739', 'nism' => '121232160048250065', 'class' => 8, 'rombel' => '2', 'entry_year' => 2025],

            // ─── KELAS 8.3 (19 Siswa) ───────────────────────────
            ['name' => 'ADITYA NAUVAL DARY ABIYYU', 'gender' => 'male', 'nisn' => '3129150428', 'nism' => '121232160048250066', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'AHMAD RIFA\'I', 'gender' => 'male', 'nisn' => '3130270113', 'nism' => '121232160048250067', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'ALEXI DWI RUHIYAT', 'gender' => 'male', 'nisn' => '0136946606', 'nism' => '121232160048250068', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'ALIF NUR KHADAFI', 'gender' => 'male', 'nisn' => '3132857014', 'nism' => '121232160048250069', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'CHELSEA APRILIA ADSAR', 'gender' => 'female', 'nisn' => '3138607066', 'nism' => '121232160048250071', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'CINTYA DWI MELIANSYAH', 'gender' => 'female', 'nisn' => '3138802822', 'nism' => '121232160048250072', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'DEFA IRDINA MAULIDA', 'gender' => 'female', 'nisn' => '3134409237', 'nism' => '121232160048250073', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'FAHRI YUSUF ARDABILI', 'gender' => 'male', 'nisn' => '3137112443', 'nism' => '121232160048250074', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'HANUN ADITYA RAMADHAN', 'gender' => 'male', 'nisn' => '3139507401', 'nism' => '121232160048250077', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'KEYSHA SABRINA PUTRI', 'gender' => 'female', 'nisn' => '3127250490', 'nism' => '121232160048250078', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'KYAN AFDHAL HAMONANGAN HUTAJULU', 'gender' => 'male', 'nisn' => '3126224686', 'nism' => '121232160048250079', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'MESSY AURELIA', 'gender' => 'female', 'nisn' => '0134396011', 'nism' => '121232160048250080', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'MOH. ROZIKIN', 'gender' => 'male', 'nisn' => '3123944256', 'nism' => '121232160048250081', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD FARHAN MUBHINA', 'gender' => 'male', 'nisn' => '3138046683', 'nism' => '121232160048250082', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD NOUVAL AL BARR', 'gender' => 'male', 'nisn' => '3120472829', 'nism' => '121232160048250084', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'NAFIZA AZZAHRA HAKIM', 'gender' => 'female', 'nisn' => '0137754431', 'nism' => '121232160048250085', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'QUROTUL \'UYUN', 'gender' => 'female', 'nisn' => '0123686960', 'nism' => '121232160048250086', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'RAIZA GEIFHAN MAULANA', 'gender' => 'male', 'nisn' => '3123889382', 'nism' => '121232160048250087', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],
            ['name' => 'SAHRUL ARIFIN ILHAM', 'gender' => 'male', 'nisn' => '3134637371', 'nism' => '121232160048250089', 'class' => 8, 'rombel' => '3', 'entry_year' => 2025],

            // ─── KELAS 8.4 (18 Siswa) ───────────────────────────
            ['name' => 'AHMAD FAIRUZ ZABADI', 'gender' => 'male', 'nisn' => '3123290745', 'nism' => '121232160048250001', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'ALYA SAPTINI', 'gender' => 'female', 'nisn' => '3125689724', 'nism' => '121232160048250002', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'AQILA CANTIKA PUTRI', 'gender' => 'female', 'nisn' => '3124919058', 'nism' => '121232160048250003', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'CELLO ADITYA', 'gender' => 'male', 'nisn' => '3126854810', 'nism' => '121232160048250004', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'DIKA MAHENDRA', 'gender' => 'male', 'nisn' => '3134445089', 'nism' => '121232160048250005', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'ERIK RAHMATIKA', 'gender' => 'male', 'nisn' => '0136521830', 'nism' => '121232160048250006', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'FACHRI FADILAH', 'gender' => 'male', 'nisn' => '3138668200', 'nism' => '121232160048250050', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'LIFYA AGUSTIN', 'gender' => 'female', 'nisn' => '3133634470', 'nism' => '121232160048250007', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'MUHAMAD HAFIZH DARMAWAN', 'gender' => 'male', 'nisn' => '0121646330', 'nism' => '121232160048250009', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'MUHAMAD RAAIHAN FAIRUZ', 'gender' => 'male', 'nisn' => '3123921555', 'nism' => '121232160048250010', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'MUHAMAD RIZKI', 'gender' => 'male', 'nisn' => '3114155977', 'nism' => '121232160048250011', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD ILHAM FADHIEL ALSHARAWY', 'gender' => 'male', 'nisn' => '3138279526', 'nism' => '121232160048250008', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD KHAIRUL AZZAM', 'gender' => 'male', 'nisn' => '3128869765', 'nism' => '121232160048250035', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'NADYA ANISA PUTRI', 'gender' => 'female', 'nisn' => '0121177769', 'nism' => '121232160048250012', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'NAIRA AJWA FAHIRA', 'gender' => 'female', 'nisn' => '3134937775', 'nism' => '121232160048250013', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'SINTA APRILIA', 'gender' => 'female', 'nisn' => '3136233527', 'nism' => '121232160048250016', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'SITI KARMILA HASANAH', 'gender' => 'female', 'nisn' => '3126223075', 'nism' => '121232160048250017', 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],
            ['name' => 'MUHAMMAD REVI YUNUS', 'gender' => 'male', 'nisn' => '3120008418', 'nism' => null, 'class' => 8, 'rombel' => '4', 'entry_year' => 2025],

            // ─── KELAS 9.1 (29 Siswa) ───────────────────────────
            ['name' => 'AHMAD ABYAN', 'gender' => 'male', 'nisn' => '0111892188', 'nism' => '121232160048240019', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'AHMAD DAHLAN', 'gender' => 'male', 'nisn' => '0129418498', 'nism' => '121232160048240020', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'AHMAD RAMZI FADLAN', 'gender' => 'male', 'nisn' => '0114982047', 'nism' => '121232160048240089', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'AL-BAROKAH SATHER RAHMAT', 'gender' => 'male', 'nisn' => '3122669824', 'nism' => '121232160048240021', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'ARIF HIDAYATULLAH', 'gender' => 'male', 'nisn' => '0121891440', 'nism' => '121232160048240022', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'ASY SYIFA KHALFANI FADHILAH', 'gender' => 'female', 'nisn' => '0129451694', 'nism' => '121232160048240023', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'AZKA NUFUS SABILILLAH', 'gender' => 'female', 'nisn' => '0115121135', 'nism' => '121232160048240024', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'AZZLINA MAULIDA', 'gender' => 'female', 'nisn' => '0129816088', 'nism' => '121232160048240092', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'DINDA KARENTA PUTRI', 'gender' => 'female', 'nisn' => '3121527301', 'nism' => '121232160048240025', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'DINI PUTRI AINI', 'gender' => 'female', 'nisn' => '0129879630', 'nism' => '121232160048240026', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'FAYYAD FADHILLAH SUHAELI', 'gender' => 'male', 'nisn' => '3120231069', 'nism' => '121232160048240095', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'HAFIZ SHAPUTRA', 'gender' => 'male', 'nisn' => '0117486243', 'nism' => '121232160048240027', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'HAMDA SHAKIA RAHMAT', 'gender' => 'female', 'nisn' => '3122449935', 'nism' => '121232160048240028', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'HERLI NURLIANA', 'gender' => 'female', 'nisn' => '0126035790', 'nism' => '121232160048240029', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'IHSAN SEPTIYAN HADI', 'gender' => 'male', 'nisn' => '0118589620', 'nism' => '121232160048240030', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'KHOERUN BAHIR', 'gender' => 'male', 'nisn' => '0124097549', 'nism' => '121232160048240098', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'M. LATIF ZALDAN NAIZAR', 'gender' => 'male', 'nisn' => '0118532700', 'nism' => '121232160048240031', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'MALIKA BILQIST BILBINA', 'gender' => 'female', 'nisn' => '0127940222', 'nism' => '121232160048240032', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'MAWARDATUL ASRIAH', 'gender' => 'female', 'nisn' => '0129064518', 'nism' => '121232160048240033', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'MOCHAMMAD AKMAL SHALEH', 'gender' => 'male', 'nisn' => '0115963404', 'nism' => '121232160048240034', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'MOHAMMAD RISKI PADILAH', 'gender' => 'male', 'nisn' => '3128823151', 'nism' => '121232160048240101', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD SHADDAM ANJAR IBRAHIM', 'gender' => 'male', 'nisn' => '0126621469', 'nism' => '121232160048240036', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'NADIF ISYA AN-NAQI', 'gender' => 'male', 'nisn' => '0122254790', 'nism' => '121232160048240037', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'NAUFAL AL SHAADIQ', 'gender' => 'male', 'nisn' => '0128425464', 'nism' => '121232160048240104', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'NURNOVIYANTI AZZAHRA', 'gender' => 'female', 'nisn' => '0117771629', 'nism' => '121232160048240038', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'RADITIA KURNIAWAN', 'gender' => 'male', 'nisn' => '0111823419', 'nism' => '121232160048240039', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'RENIA PUTRI SINAR DEWI', 'gender' => 'female', 'nisn' => '0116769412', 'nism' => '121232160048240107', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'SOFIAH HUSNA SETIAWAN', 'gender' => 'female', 'nisn' => '3112544954', 'nism' => '121232160048240040', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],
            ['name' => 'SYAFA FEBRIANI', 'gender' => 'female', 'nisn' => '3127383853', 'nism' => '121232160048240041', 'class' => 9, 'rombel' => '1', 'entry_year' => 2024],

            // ─── KELAS 9.2 (28 Siswa) ───────────────────────────
            ['name' => 'AHMAD NUR RASYID', 'gender' => 'male', 'nisn' => '0116580838', 'nism' => '121232160048240044', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'AINUN NUR FADILLAH', 'gender' => 'female', 'nisn' => '3129142414', 'nism' => '121232160048240090', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'ALIYYA ZAHRATU SITTA', 'gender' => 'female', 'nisn' => '3125611565', 'nism' => '121232160048240045', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'ALYA FAHIRA SURIE', 'gender' => 'female', 'nisn' => '0122861291', 'nism' => '121232160048240046', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'ALYA GUSTI PATIMAH', 'gender' => 'female', 'nisn' => '0115310704', 'nism' => '121232160048240047', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'BAGAS ARITIAN V.', 'gender' => 'male', 'nisn' => '3119916089', 'nism' => '121232160048240048', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'DINDA PRAMESWARI SEPTIANINGRUM', 'gender' => 'female', 'nisn' => '0112562070', 'nism' => '121232160048240049', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'DITA ALIYA HEMAS ERVITA', 'gender' => 'female', 'nisn' => '0126708152', 'nism' => '121232160048240093', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'FATHIMAH ZAHRATUL SANNYAH', 'gender' => 'female', 'nisn' => '3127471484', 'nism' => '121232160048240050', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'FEBRI YANTI', 'gender' => 'female', 'nisn' => '3127471247', 'nism' => '121232160048240051', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'FIQIH RAYHAN HAFIDZ', 'gender' => 'male', 'nisn' => '0126115487', 'nism' => '121232160048240052', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'GHINA FATHIMATUSZAHRO', 'gender' => 'female', 'nisn' => '0128079401', 'nism' => '121232160048240053', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'HUMAAM AZMI', 'gender' => 'male', 'nisn' => '0115351542', 'nism' => '121232160048240054', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'HUSNA KAMILAH', 'gender' => 'female', 'nisn' => '3115715431', 'nism' => '121232160048240096', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'LAELA RUBY MUSYAFA', 'gender' => 'female', 'nisn' => '3113843896', 'nism' => '121232160048240056', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'M. SULTAN HASANUDIN', 'gender' => 'male', 'nisn' => '3112247866', 'nism' => '121232160048240057', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'MAULANA ISYAMUL UTSMAN', 'gender' => 'male', 'nisn' => '0112713625', 'nism' => '121232160048240099', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'MUHAMAD PAUJAN HAMBALI', 'gender' => 'male', 'nisn' => '0128826069', 'nism' => '121232160048240111', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD KHOIRUL ANAM', 'gender' => 'male', 'nisn' => '0126159043', 'nism' => '121232160048240058', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD RAIHAN GHANY ALGHIFARI', 'gender' => 'male', 'nisn' => '0117361213', 'nism' => '121232160048240059', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD REVAN MAULANA', 'gender' => 'male', 'nisn' => '0123447676', 'nism' => '121232160048240102', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'NAILAH FARAH AS\'ARI', 'gender' => 'female', 'nisn' => '0114530921', 'nism' => '121232160048240060', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'NESHA MAULIDA ARIFIN', 'gender' => 'female', 'nisn' => '3112282411', 'nism' => '121232160048240105', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'PANJI ALTHOFI SHAUQI', 'gender' => 'male', 'nisn' => '0116541812', 'nism' => '121232160048240061', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'REVANO FERDIAN SAFUTRA', 'gender' => 'male', 'nisn' => '0112754358', 'nism' => '121232160048240108', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'SUHADI PRATAMA', 'gender' => 'male', 'nisn' => '0111083136', 'nism' => '121232160048240062', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'WISNU DZAKI SAPUTRO', 'gender' => 'male', 'nisn' => '0127318098', 'nism' => '121232160048240063', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],
            ['name' => 'ZAHIR MOHANA', 'gender' => 'male', 'nisn' => '3118009545', 'nism' => '121232160048240064', 'class' => 9, 'rombel' => '2', 'entry_year' => 2024],

            // ─── KELAS 9.3 (28 Siswa) ───────────────────────────
            ['name' => 'ALFIYAH NURHASANAH', 'gender' => 'female', 'nisn' => '0119830059', 'nism' => '121232160048240091', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'ALYAA FAKHIRA KHANSA', 'gender' => 'female', 'nisn' => '0111833857', 'nism' => '121232160048240066', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'AMANDA SETIAWAN', 'gender' => 'female', 'nisn' => '3118047811', 'nism' => '121232160048240067', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'DARIS ABASIDIK SUTISNA', 'gender' => 'male', 'nisn' => '0114334428', 'nism' => '121232160048240070', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'FACHRI HUSAINI GAZALBA', 'gender' => 'male', 'nisn' => '3124420764', 'nism' => '121232160048240094', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'IKHWAN HANIF SARIAN', 'gender' => 'male', 'nisn' => '0114482355', 'nism' => '121232160048240071', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'INTAN FITRIANI', 'gender' => 'female', 'nisn' => '3113776536', 'nism' => '121232160048240097', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'M. ALHADI BAYHAQI', 'gender' => 'male', 'nisn' => '0115759784', 'nism' => '121232160048240072', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'M. RAKA ATTHALA FARDES', 'gender' => 'male', 'nisn' => '3114254162', 'nism' => '121232160048240073', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MOCHAMMAD VIRGIAWAN FAUDZI', 'gender' => 'male', 'nisn' => '0117178638', 'nism' => '121232160048240100', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MOHAMMAD IRHAM ABID', 'gender' => 'male', 'nisn' => '0125901658', 'nism' => '121232160048240074', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD ARIEF PRATAMA', 'gender' => 'male', 'nisn' => '3127219121', 'nism' => '121232160048240075', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD FAKHRI MUZAKKI', 'gender' => 'male', 'nisn' => '0124484904', 'nism' => '121232160048240010', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD FAQIH ABDILLAH', 'gender' => 'male', 'nisn' => '3123369411', 'nism' => '121232160048240076', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD IHSAN HAFIDZIN', 'gender' => 'male', 'nisn' => '0112633007', 'nism' => '121232160048240077', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD IRFAN MAULANA', 'gender' => 'male', 'nisn' => '0122575274', 'nism' => '121232160048240078', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD NAUVAL AKBAR', 'gender' => 'male', 'nisn' => '0115370739', 'nism' => '121232160048240079', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'NAILA SAFITRI', 'gender' => 'female', 'nisn' => '0113783344', 'nism' => '121232160048240081', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'NANDA ALFARIZY H.S', 'gender' => 'male', 'nisn' => '0118046503', 'nism' => '121232160048240103', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'NAVILLA KHAIRRUNNISA', 'gender' => 'female', 'nisn' => '3113148144', 'nism' => '121232160048240082', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'PASYA FABRIZIO AL ZABAR', 'gender' => 'male', 'nisn' => '3121367351', 'nism' => '121232160048240083', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'RAHMA HAYUNINGTYAS', 'gender' => 'female', 'nisn' => '0116998610', 'nism' => '121232160048240084', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'RAMDAN REZKY PANGESTU', 'gender' => 'male', 'nisn' => '0111866809', 'nism' => '121232160048240106', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'RINJANI', 'gender' => 'female', 'nisn' => '0116984092', 'nism' => '121232160048240085', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'SHERYL AZZAHRA', 'gender' => 'female', 'nisn' => '0125924150', 'nism' => '121232160048240109', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'SITI KHAERUNNISA', 'gender' => 'female', 'nisn' => '3112857268', 'nism' => '121232160048240086', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'SYAFIAH RAHMA', 'gender' => 'female', 'nisn' => '0115543015', 'nism' => '121232160048240087', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],
            ['name' => 'YUMNA FEBRIANI PUTRI', 'gender' => 'female', 'nisn' => '3124388076', 'nism' => '121232160048240110', 'class' => 9, 'rombel' => '3', 'entry_year' => 2024],

            // ─── KELAS 9.4 (20 Siswa) ───────────────────────────
            ['name' => 'ABDUL MUKLAS', 'gender' => 'male', 'nisn' => '0118413165', 'nism' => '121232160048240001', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'ABDUL PALAN', 'gender' => 'male', 'nisn' => '0118272800', 'nism' => '121232160048240002', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'ABDULLAH MA\'SHUM FACHRURROZI', 'gender' => 'male', 'nisn' => '3124186095', 'nism' => '121232160048240112', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'ANDREA ARDIANSYAH JANUARDI', 'gender' => 'male', 'nisn' => '3125182123', 'nism' => '121232160048240003', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'ANGGARA PUTRA DERAJAT', 'gender' => 'male', 'nisn' => '0111169908', 'nism' => '121232160048240004', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'HARTATI', 'gender' => 'female', 'nisn' => '3128595290', 'nism' => '121232160048240005', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'KHRESNA GUNTUR SAMUDRA', 'gender' => 'male', 'nisn' => '3129815662', 'nism' => '121232160048240006', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'LUH PRASTISTA SARI', 'gender' => 'female', 'nisn' => '3110999262', 'nism' => '121232160048240114', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'M. SATRIA FIRDAUS', 'gender' => 'male', 'nisn' => '3114746146', 'nism' => '121232160048240007', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'MUHAMAD FAHREZZA', 'gender' => 'male', 'nisn' => '0116191159', 'nism' => '121232160048240035', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'MUHAMAD FARHAN KURNIAWAN', 'gender' => 'male', 'nisn' => '0126706589', 'nism' => '121232160048240008', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD FAJRI', 'gender' => 'male', 'nisn' => '0114643813', 'nism' => '121232160048240009', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD RIZKI PADILLAH', 'gender' => 'male', 'nisn' => '3118268527', 'nism' => '121232160048240011', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'MUHAMMAD SYAHRIL PRATAMA', 'gender' => 'male', 'nisn' => '3115177230', 'nism' => '121232160048240012', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'NASSYA AGNY DAHLAN', 'gender' => 'female', 'nisn' => '0117303306', 'nism' => '121232160048240113', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'NUR ZAKIAH ALYA NABILA', 'gender' => 'female', 'nisn' => '3120643429', 'nism' => '121232160048240013', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'RIZKI ADITIA FIRDAUS', 'gender' => 'male', 'nisn' => '3111100035', 'nism' => '121232160048240015', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'ROBIATUL ADAWIYAH', 'gender' => 'female', 'nisn' => '0111624009', 'nism' => '121232160048240016', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'SALSABILA PAKPAHAN', 'gender' => 'female', 'nisn' => '0129137248', 'nism' => '121232160048240017', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
            ['name' => 'SEPTIAN SULISTIO', 'gender' => 'male', 'nisn' => '0119782036', 'nism' => '121232160048240018', 'class' => 9, 'rombel' => '4', 'entry_year' => 2024],
        ];

        foreach ($studentsData as $index => $s) {
            $nisnClean = preg_replace('/\D/', '', $s['nisn']);
            $emailWali = 'wali_' . $nisnClean . '@parent.test';
            $phoneWali = '0812' . substr($nisnClean, -8);

            // Buat Akun User Orang Tua
            $userParent = User::create([
                'name' => 'Wali ' . ucwords(strtolower($s['name'])),
                'email' => $emailWali,
                'password' => Hash::make('password'),
                'role' => 'parent',
            ]);

            // Buat Profil Orang Tua
            $parentProfile = ParentProfile::create([
                'user_id' => $userParent->id,
                'full_name' => 'Wali ' . ucwords(strtolower($s['name'])),
                'phone' => $phoneWali,
                'contact_email' => $emailWali,
                'address' => 'Desa Cibuntu, Kec. Cibitung, Kab. Bekasi',
            ]);

            // Tanggal lahir realistis (Kelas 7: 2013-2014, Kelas 8: 2012-2013, Kelas 9: 2011-2012)
            $birthYear = 2026 - (6 + (int)$s['class']);
            $birthMonth = str_pad((($index * 3) % 12) + 1, 2, '0', STR_PAD_LEFT);
            $birthDay = str_pad((($index * 5) % 28) + 1, 2, '0', STR_PAD_LEFT);
            $birthDate = "{$birthYear}-{$birthMonth}-{$birthDay}";

            // Buat Data Siswa Nyata
            Student::create([
                'parent_id' => $parentProfile->id,
                'nis' => $s['nisn'],
                'nism' => $s['nism'] ?? null,
                'full_name' => $s['name'],
                'gender' => $s['gender'],
                'birth_date' => $birthDate,
                'class_level' => $s['class'],
                'rombel' => $s['rombel'],
                'entry_year' => $s['entry_year'],
                'status' => Student::STATUS_ACTIVE,
                'email' => 'siswa_' . $nisnClean . '@pondok.test',
                'phone' => '0813' . substr($nisnClean, -8),
                'address' => 'Desa Cibuntu RT. 002/007, Kec. Cibitung, Kab. Bekasi',
            ]);
        }
    }
}
