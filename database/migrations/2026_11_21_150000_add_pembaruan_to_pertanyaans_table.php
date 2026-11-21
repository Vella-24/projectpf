<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->string('status_pembaruan', 50)->nullable()->after('status');
            $table->longText('draft_jawaban')->nullable()->after('status_pembaruan');
            $table->text('catatan_pembaruan')->nullable()->after('draft_jawaban');
            $table->foreignId('pembaruan_diminta_oleh')->nullable()->after('catatan_pembaruan')->constrained('users')->nullOnDelete();
            $table->timestamp('pembaruan_diminta_pada')->nullable()->after('pembaruan_diminta_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->dropForeign(['pembaruan_diminta_oleh']);
            $table->dropColumn([
                'status_pembaruan',
                'draft_jawaban',
                'catatan_pembaruan',
                'pembaruan_diminta_oleh',
                'pembaruan_diminta_pada',
            ]);
        });
    }
};
