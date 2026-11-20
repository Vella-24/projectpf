<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_jawaban', function (Blueprint $table) {
            $table->longText('jawaban_lama')->nullable()->after('pertanyaan_id');
            $table->longText('jawaban_baru')->nullable()->after('jawaban_lama');
        });

        // Salin isi_jawaban ke jawaban_baru untuk data yang sudah ada sebelumnya
        DB::table('riwayat_jawaban')
            ->whereNull('jawaban_baru')
            ->update([
                'jawaban_baru' => DB::raw('isi_jawaban')
            ]);
    }

    public function down(): void
    {
        Schema::table('riwayat_jawaban', function (Blueprint $table) {
            $table->dropColumn(['jawaban_lama', 'jawaban_baru']);
        });
    }
};
