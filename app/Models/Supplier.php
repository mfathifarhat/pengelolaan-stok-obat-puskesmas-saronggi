<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'kontak_person',
    ];

    public function batchObats(): HasMany
    {
        return $this->hasMany(BatchObat::class);
    }
}
