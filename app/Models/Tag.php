<?php

namespace App\Models;

use App\Models\Pertanyaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'nama_tag',
    ];

    public function pertanyaans()
    {
        return $this->belongsToMany(
            Pertanyaan::class,
            'pertanyaan_tag',
            'tag_id',
            'pertanyaan_id'
        );
    }
}
