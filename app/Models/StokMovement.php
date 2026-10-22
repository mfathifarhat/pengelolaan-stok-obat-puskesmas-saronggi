<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class StokMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_obat_id',
        'user_id',
        'tipe', // 'masuk' atau 'keluar'
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    public function batchObat(): BelongsTo
    {
        return $this->belongsTo(BatchObat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function obat(): HasOneThrough
    {
        return $this->hasOneThrough(
            Obat::class,
            BatchObat::class,
            'id', // Foreign key on batch_obats table...
            'id', // Foreign key on obats table...
            'batch_obat_id', // Local key on stok_movements table...
            'obat_id' // Local key on batch_obats table...
        );
    }
}
