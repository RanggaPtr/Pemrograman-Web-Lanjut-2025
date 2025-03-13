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

    // Relasi ke User (jika ada relasi dengan User)
    public function KategoriModel(): HasMany
    {
        return $this->hasMany(UserModel::class, 'kategori_id', 'kategori_id');
    }
}
