<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\Tag;


class Pertanyaan extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'kategori_id',
        'user_id',
        'judul_pertanyaan',
        'isi_jawaban',
        'status',
        'catatan_validator',
        'validator_id',
        'review_diambil_pada',
    ];

    /**
     * Relasi Pertanyaan milik 1 Kategori (many-to-1)
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi Pertanyaan dibuat oleh User (Data Entry)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //relasi tag
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'pertanyaan_tag',
            'pertanyaan_id',
            'tag_id'
        );
    }

    public function pertanyaanLanjutans()
    {
        return $this->hasMany(
            PertanyaanLanjutan::class,
            'pertanyaan_id'
        );
    }
    
    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing(['kategori', 'tags']);

        return [
            'id' => (int) $this->id,
            'judul_pertanyaan' => (string) $this->judul_pertanyaan,
            'isi_jawaban' => (string) $this->isi_jawaban,
            'kategori_id' => (int) $this->kategori_id,
            'kategori_nama' => (string) ($this->kategori->nama_kategori ?? ''),
            'tags' => $this->tags ? $this->tags->pluck('nama_tag')->implode(' ') : '',
            'status' => (string) $this->status,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === 'disetujui';
    }

    public function sumberPertanyaanLanjutan()
    {
        return $this->hasOne(
            PertanyaanLanjutan::class,
            'faq_baru_id'
        );
    }
}
