<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relasi 1 Kategori memiliki banyak Pertanyaan (1-to-many)
     */
    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class, 'kategori_id');
    }
}
