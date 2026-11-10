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
        Schema::create('pertanyaan_lanjutans', function (Blueprint $table) {
        $table->id();

        // FAQ yang membuat mahasiswa mengajukan pertanyaan lanjutan
        $table->foreignId('pertanyaan_id')
            ->constrained('pertanyaans')
            ->cascadeOnDelete();

        $table->string('email_mahasiswa');

        $table->text('isi_pertanyaan');

        // Petugas Data Entry yang mengambil pekerjaan
        $table->foreignId('data_entry_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        // Status pekerjaan
        $table->enum('status', [
            'baru',
            'sedang_ditangani',
            'dijawab',
            'ditutup'
        ])->default('baru');

        $table->timestamp('diambil_pada')->nullable();

        // Jawaban dari petugas
        $table->text('jawaban')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_lanjutans');
    }
};
