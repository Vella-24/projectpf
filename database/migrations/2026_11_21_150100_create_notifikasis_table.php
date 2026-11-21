<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('role', 50)->nullable();
            $table->foreignId('pertanyaan_id')->nullable()->constrained('pertanyaans')->cascadeOnDelete();
            $table->string('tipe', 50);
            $table->string('judul', 255);
            $table->text('pesan');
            $table->string('link', 255)->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
