<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PertanyaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pertanyaans')->insert([
            [
                'kategori_id' => 1,
                'user_id' => 1,
                'judul_pertanyaan' => 'Bagaimana alur jika mahasiswa terlambat perwalian KRS?',
                'isi_jawaban' => 'Mahasiswa dapat menghubungi Dosen Wali masing-masing terlebih dahulu, kemudian mengajukan form permohonan perwalian susulan ke loket Direktorat Administrasi Akademik sesuai jadwal periode susulan.',
                'status' => 'disetujui',
                'catatan_validator' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 1,
                'user_id' => 1,
                'judul_pertanyaan' => 'Berapa batas maksimal SKS yang dapat diambil dalam satu semester?',
                'isi_jawaban' => 'Batas maksimal SKS ditentukan berdasarkan perolehan IPS semester sebelumnya: IPS >= 3.00 dapat mengambil maksimal 24 SKS, sedangkan IPS < 2.00 maksimal 16 SKS.',
                'status' => 'disetujui',
                'catatan_validator' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 2,
                'user_id' => 1,
                'judul_pertanyaan' => 'Bagaimana cara mengajukan dispensasi cicilan pembayaran uang kuliah?',
                'isi_jawaban' => 'Pengajuan dispensasi dilakukan secara online melalui portal keuangan mahasiswa atau mendatangi loket Direktorat Keuangan dengan melampirkan surat permohonan yang ditandatangani orang tua.',
                'status' => 'menunggu_validasi',
                'catatan_validator' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 3,
                'user_id' => 1,
                'judul_pertanyaan' => 'Apa saja syarat umum untuk mendaftar beasiswa prestasi akademik?',
                'isi_jawaban' => 'Syarat umumnya adalah mahasiswa aktif minimal semester 3, memiliki IPK minimal 3.50 tanpa nilai D/E, serta aktif di kegiatan organisasi kemahasiswaan.',
                'status' => 'menunggu_validasi',
                'catatan_validator' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_id' => 4,
                'user_id' => 1,
                'judul_pertanyaan' => 'Berapa lama proses pembuatan ijazah dan transkrip nilai setelah yudisium?',
                'isi_jawaban' => 'Proses penerbitan ijazah dan transkrip resmi memakan waktu sekitar 2 hingga 4 minggu setelah tanggal pengesahan SK Yudisium oleh fakultas.',
                'status' => 'draft',
                'catatan_validator' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
