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

    // Add a fillable array to allow mass assignment on these fields.
    protected $fillable = [
        'supplier_kode',
        'supplier_nama',
        'supplier_alamat',
    ];

    // Example relationship (if you need one)
    public function user(): HasMany
    {
        return $this->hasMany(SupplierModel::class, 'supplier_id', 'supplier_id');
    }
}
