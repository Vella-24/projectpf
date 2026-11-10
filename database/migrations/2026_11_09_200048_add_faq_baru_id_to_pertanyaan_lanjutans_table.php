<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pertanyaan_lanjutans', function (Blueprint $table) {
            $table->foreignId('faq_baru_id')
                ->nullable()
                ->unique()
                ->constrained('pertanyaans')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertanyaan_lanjutans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('faq_baru_id');
        });
    }
};
