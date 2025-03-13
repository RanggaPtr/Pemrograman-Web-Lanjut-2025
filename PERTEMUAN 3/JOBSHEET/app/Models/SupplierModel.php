<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierModel extends Model
{
    use HasFactory;
    protected $table = 'm_supplier';
    protected $primaryKey = 'supplier_id';

    // Relasi ke User (jika ada relasi dengan User)
    public function user(): HasMany
    {
        return $this->hasMany(SupplierModel::class, 'supplier_id', 'supplier_id');
    }
}
