<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokTotalModel extends Model
{
    use HasFactory;

    protected $table = 't_stok_total';
    protected $primaryKey = 'stok_total_id';

    protected $fillable = [
        'barang_id',
        'stok_jumlah',
    ];

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }
}