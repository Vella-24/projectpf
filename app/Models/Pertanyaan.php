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

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'judul_pertanyaan' => $this->judul_pertanyaan,
            'isi_jawaban' => $this->isi_jawaban,
            'kategori_id' => $this->kategori_id,
            'status' => $this->status,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === 'disetujui';
    }
}
