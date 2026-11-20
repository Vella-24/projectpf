<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_jawaban', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pertanyaan_id')
                ->constrained('pertanyaans')
                ->cascadeOnDelete();

            $table->text('isi_jawaban');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jawaban');
    }
};