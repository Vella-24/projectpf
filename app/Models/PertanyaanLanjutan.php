<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaanLanjutan extends Model
{
    protected $table = 'pertanyaan_lanjutans';

    protected $fillable = [
        'pertanyaan_id',
        'nama_mahasiswa',
        'email_mahasiswa',
        'isi_pertanyaan',
        'data_entry_id',
        'status',
        'diambil_pada',
        'jawaban'
    ];

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function dataEntry()
    {
        return $this->belongsTo(User::class, 'data_entry_id');
    }

    public function faqBaru()
    {
        return $this->belongsTo(Pertanyaan::class, 'faq_baru_id');
    }
}
