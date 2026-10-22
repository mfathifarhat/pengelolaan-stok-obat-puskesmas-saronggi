<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriObat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    public function obats(): HasMany
    {
        return $this->hasMany(Obat::class, 'kategori_obat_id');
    }
}
