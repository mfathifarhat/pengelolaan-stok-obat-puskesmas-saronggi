<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori_obat_id',
        'satuan',
        'produsen',
        'stok_minimum',
        'keterangan',
    ];

    protected $appends = [
        'total_stok',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriObat::class, 'kategori_obat_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(BatchObat::class);
    }

    public function activeBatches(): HasMany
    {
        return $this->hasMany(BatchObat::class)->where('stok', '>', 0)->orderBy('tanggal_kadaluarsa', 'asc');
    }

    public function getTotalStokAttribute(): int
    {
        if ($this->relationLoaded('batches')) {
            return (int) $this->batches->sum('stok');
        }

        return (int) $this->batches()->sum('stok');
    }
}
