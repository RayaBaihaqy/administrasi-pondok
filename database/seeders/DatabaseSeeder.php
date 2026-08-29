<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,         // Users + Parents + Students
            AcademicYearSeeder::class,  // Tahun ajaran + placement
            PaymentTypeSeeder::class,   // Master Jenis Pembayaran + Pricing + Override
            BillSeeder::class,          // Tagihan & Transaksi Sampel
        ]);
    }
}
