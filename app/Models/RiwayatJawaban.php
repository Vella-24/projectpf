<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatJawaban extends Model
{
    protected $table = 'riwayat_jawaban';

    protected $fillable = [
        'pertanyaan_id',
        'jawaban_lama',
        'jawaban_baru',
        'user_id',
    ];

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getJawabanSebelumnyaAttribute(): ?string
    {
        return $this->jawaban_lama;
    }

    public function getJawabanTerbaruAttribute(): ?string
    {
        return $this->jawaban_baru;
    }
}
