<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah enum pertanyaans menjadi integer
        DB::statement("ALTER TABLE pertanyaans MODIFY status VARCHAR(50) NOT NULL");
        DB::statement("
            UPDATE pertanyaans
            SET status = CASE status
                WHEN 'draft' THEN '0'
                WHEN 'menunggu_validasi' THEN '1'
                WHEN 'disetujui' THEN '2'
                WHEN 'ditolak' THEN '3'
                ELSE '0'
            END
        ");
        DB::statement("ALTER TABLE pertanyaans MODIFY status TINYINT UNSIGNED NOT NULL DEFAULT 1");

        // 2. Ubah enum pertanyaan_lanjutans menjadi integer
        DB::statement("ALTER TABLE pertanyaan_lanjutans MODIFY status VARCHAR(50) NOT NULL");
        DB::statement("
            UPDATE pertanyaan_lanjutans
            SET status = CASE status
                WHEN 'baru' THEN '0'
                WHEN 'sedang_ditangani' THEN '1'
                WHEN 'dijawab' THEN '2'
                WHEN 'ditutup' THEN '3'
                ELSE '0'
            END
        ");
        DB::statement("ALTER TABLE pertanyaan_lanjutans MODIFY status TINYINT UNSIGNED NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        // Kembalikan pertanyaans menjadi ENUM
        DB::statement("ALTER TABLE pertanyaans MODIFY status VARCHAR(50) NOT NULL");
        DB::statement("
            UPDATE pertanyaans
            SET status = CASE status
                WHEN '0' THEN 'draft'
                WHEN '1' THEN 'menunggu_validasi'
                WHEN '2' THEN 'disetujui'
                WHEN '3' THEN 'ditolak'
                ELSE 'draft'
            END
        ");
        DB::statement("ALTER TABLE pertanyaans MODIFY status ENUM('draft', 'menunggu_validasi', 'disetujui', 'ditolak') NOT NULL DEFAULT 'menunggu_validasi'");

        // Kembalikan pertanyaan_lanjutans menjadi ENUM
        DB::statement("ALTER TABLE pertanyaan_lanjutans MODIFY status VARCHAR(50) NOT NULL");
        DB::statement("
            UPDATE pertanyaan_lanjutans
            SET status = CASE status
                WHEN '0' THEN 'baru'
                WHEN '1' THEN 'sedang_ditangani'
                WHEN '2' THEN 'dijawab'
                WHEN '3' THEN 'ditutup'
                ELSE 'baru'
            END
        ");
        DB::statement("ALTER TABLE pertanyaan_lanjutans MODIFY status ENUM('baru', 'sedang_ditangani', 'dijawab', 'ditutup') NOT NULL DEFAULT 'baru'");
    }
};
