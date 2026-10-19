<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
            [
                'nama_kategori' => 'Layanan Akademik & KRS',
                'deskripsi' => 'Informasi perwalian, pengisian kartu rencana studi, jadwal kuliah, dan ujian.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Keuangan & UKS/UPP',
                'deskripsi' => 'Informasi pembayaran SPP, angsuran kuliah, dispensasi, dan tagihan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Kemahasiswaan & Beasiswa',
                'deskripsi' => 'Informasi beasiswa prestasi/ekonomi, kegiatan ormawa, dan sertifikat kemahasiswaan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Kelulusan & Wisuda',
                'deskripsi' => 'Syarat yudisium, pendaftaran wisuda, dan legalisir ijazah/transkrip.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
