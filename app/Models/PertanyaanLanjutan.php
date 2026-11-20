<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaanLanjutan extends Model
{
    protected $table = 'pertanyaan_lanjutans';

    // Status Integer Constants
    public const BARU = 0;
    public const SEDANG_DITANGANI = 1;
    public const DIJAWAB = 2;
    public const DITUTUP = 3;

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

    // Status dibaca sebagai integer di database
    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Pemetaan status angka ke teks tampilan
     */
    public static function statusLabelMap(): array
    {
        return [
            self::BARU => 'Belum Ditangani',
            self::SEDANG_DITANGANI => 'Sedang Ditangani',
            self::DIJAWAB => 'Sudah Dijawab',
            self::DITUTUP => 'Ditutup',
        ];
    }

    /**
     * Konversi nilai status (string / angka) ke integer yang valid
     */
    public static function normalizeStatus(mixed $status): ?int
    {
        if ($status === null || $status === '') {
            return null;
        }

        if (is_numeric($status)) {
            return (int) $status;
        }

        return match (strtolower(trim((string) $status))) {
            'baru' => self::BARU,
            'sedang_ditangani', 'diproses' => self::SEDANG_DITANGANI,
            'dijawab', 'selesai' => self::DIJAWAB,
            'ditutup' => self::DITUTUP,
            default => null,
        };
    }

    /**
     * Mendapatkan label string dari status untuk tampilan
     */
    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::BARU => 'Belum Ditangani',
            self::SEDANG_DITANGANI => 'Sedang Ditangani',
            self::DIJAWAB => 'Sudah Dijawab',
            self::DITUTUP => 'Ditutup',
            default => 'Belum Ditangani',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return $this->status_label;
    }

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
