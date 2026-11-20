<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('riwayat_jawaban', 'isi_jawaban')) {
            Schema::table('riwayat_jawaban', function (Blueprint $table) {
                $table->dropColumn('isi_jawaban');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('riwayat_jawaban', 'isi_jawaban')) {
            Schema::table('riwayat_jawaban', function (Blueprint $table) {
                $table->text('isi_jawaban')->nullable()->after('jawaban_baru');
            });
        }
    }
};
