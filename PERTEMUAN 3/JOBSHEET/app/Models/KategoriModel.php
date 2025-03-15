<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriModel extends Model
{
    use HasFactory;
    protected $table = 'm_kategori';
    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'kategori_kode',  // Allow mass assignment for level_kode
        'kategori_nama',  // Allow mass assignment for level_nama
    ];
    // Relasi ke User (jika ada relasi dengan User)
    public function KategoriModel(): HasMany
    {
        return $this->hasMany(UserModel::class, 'kategori_id', 'kategori_id');
    }
}
