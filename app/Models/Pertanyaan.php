<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\Tag;

class Pertanyaan extends Model
{
    use HasFactory, Searchable;

    // Status Integer Constants
    public const DRAFT = 0;
    public const MENUNGGU_VALIDASI = 1;
    public const DISETUJUI = 2;
    public const DITOLAK = 3;

    // Status Pembaruan Kebijakan Baru
    public const STATUS_PEMBARUAN_PERLU = 'perlu_pembaruan';
    public const STATUS_PEMBARUAN_MENUNGGU = 'menunggu_validasi';

    protected $fillable = [
        'kategori_id',
        'fakultas',
        'user_id',
        'judul_pertanyaan',
        'isi_jawaban',
        'status',
        'status_pembaruan',
        'draft_jawaban',
        'catatan_pembaruan',
        'pembaruan_diminta_oleh',
        'pembaruan_diminta_pada',
        'catatan_validator',
        'validator_id',
        'review_diambil_pada',
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
            self::DRAFT => 'Draft',
            self::MENUNGGU_VALIDASI => 'Menunggu Validasi',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak / Perlu Revisi',
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
            'draft' => self::DRAFT,
            'menunggu_validasi', 'menunggu' => self::MENUNGGU_VALIDASI,
            'disetujui' => self::DISETUJUI,
            'ditolak', 'revisi' => self::DITOLAK,
            default => null,
        };
    }

    /**
     * Mendapatkan label string dari status untuk tampilan
     */
    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::DRAFT => 'Draft',
            self::MENUNGGU_VALIDASI => 'Menunggu Validasi',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak / Perlu Revisi',
            default => 'Draft',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return $this->status_label;
    }

    /**
     * Mendapatkan nama fakultas target
     */
    public function getNamaFakultasAttribute(): string
    {
        return \App\Helpers\FakultasHelper::getNamaByKode($this->fakultas);
    }

    /**
     * Mendapatkan class styling badge fakultas
     */
    public function getBadgeClassAttribute(): string
    {
        return \App\Helpers\FakultasHelper::getBadgeClass($this->fakultas);
    }

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

    // Relasi tag
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
            'fakultas' => (string) ($this->fakultas ?? 'umum'),
            'tags' => $this->tags ? $this->tags->pluck('nama_tag')->implode(' ') : '',
            'status' => (int) $this->status,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return (int) $this->status === self::DISETUJUI;
    }

    public function sumberPertanyaanLanjutan()
    {
        return $this->hasOne(
            PertanyaanLanjutan::class,
            'faq_baru_id'
        );
    }

    public function riwayatJawaban()
    {
        return $this->hasMany(RiwayatJawaban::class, 'pertanyaan_id')
            ->orderBy('created_at', 'desc');
    }

    public function riwayatTerbaru()
    {
        return $this->hasOne(RiwayatJawaban::class, 'pertanyaan_id')
            ->latestOfMany();
    }

    public function pembaruanDimintaOlehUser()
    {
        return $this->belongsTo(User::class, 'pembaruan_diminta_oleh');
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class, 'pertanyaan_id');
    }

    /**
     * Memeriksa apakah pertanyaan terkunci dari edit oleh Data Entry.
     * Pertanyaan disetujui (2) terkunci KECUALI ada instruksi "perlu_pembaruan".
     * Pertanyaan yang sedang menunggu validasi (1) juga terkunci.
     */
    public function isLockedForDataEntry(): bool
    {
        if ((int) $this->status === self::DISETUJUI) {
            return $this->status_pembaruan !== self::STATUS_PEMBARUAN_PERLU;
        }

        if ((int) $this->status === self::MENUNGGU_VALIDASI) {
            return true;
        }

        return false;
    }

    /**
     * Memeriksa apakah pertanyaan terkunci dari hapus oleh Data Entry.
     * Pertanyaan yang sudah disetujui/tayang tidak boleh dihapus.
     */
    public function isLockedDeleteForDataEntry(): bool
    {
        return (int) $this->status === self::DISETUJUI;
    }

    public function getIsPerluPembaruanAttribute(): bool
    {
        return $this->status_pembaruan === self::STATUS_PEMBARUAN_PERLU;
    }

    public function getIsPembaruanMenungguValidasiAttribute(): bool
    {
        return $this->status_pembaruan === self::STATUS_PEMBARUAN_MENUNGGU;
    }
}
