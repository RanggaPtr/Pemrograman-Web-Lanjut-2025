<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'm_level';  // Table name
    protected $primaryKey = 'level_id';  // Primary key

    // Allow mass assignment for these fields
    // agar bisa diganti atau dilakukan penambahan atau perubahan data
    protected $fillable = [
        'level_kode',  // Allow mass assignment for level_kode
        'level_nama',  // Allow mass assignment for level_nama
    ];

    // Relasi ke User (One-to-Many)
    public function user(): HasMany
    {
        return $this->hasMany(UserModel::class, 'level_id', 'level_id');
    }
}
