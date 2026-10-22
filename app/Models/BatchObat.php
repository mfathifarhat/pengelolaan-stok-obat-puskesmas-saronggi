<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchObat extends Model
{
    use HasFactory;

    protected $fillable = [
        'obat_id',
        'supplier_id',
        'no_batch',
        'tanggal_kadaluarsa',
        'tanggal_masuk',
        'harga_beli',
        'stok',
    ];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
        'tanggal_masuk' => 'date',
        'harga_beli' => 'decimal:2',
        'stok' => 'integer',
    ];

    protected $appends = [
        'status_kadaluarsa',
    ];

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stokMovements(): HasMany
    {
        return $this->hasMany(StokMovement::class);
    }

    public function getStatusKadaluarsaAttribute(): string
    {
        $today = Carbon::today();
        $expiredDate = Carbon::parse($this->tanggal_kadaluarsa);

        if ($expiredDate->isPast()) {
            return 'expired'; // Sudah kadaluarsa
        }

        if ($today->diffInDays($expiredDate, false) <= 90) {
            return 'warning'; // Mendekati kadaluarsa (< 3 bulan)
        }

        return 'safe'; // Masih aman
    }
}
