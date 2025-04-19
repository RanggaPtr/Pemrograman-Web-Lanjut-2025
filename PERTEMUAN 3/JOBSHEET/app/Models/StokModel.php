<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    use HasFactory;

    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';

    protected $fillable = [
        'supplier_id',
        'barang_id',
        'user_id',
        'stock_tanggal',
        'stok_jumlah',
    ];

    protected static function boot()
    {
        parent::boot();

        // Saat stok baru ditambahkan
        static::created(function ($stok) {
            $stokTotal = StokTotalModel::firstOrCreate(
                ['barang_id' => $stok->barang_id],
                ['stok_jumlah' => 0]
            );
            $stokTotal->stok_jumlah += $stok->stok_jumlah;
            $stokTotal->save();
        });

        // Saat stok diupdate
        static::updated(function ($stok) {
            $stokTotal = StokTotalModel::where('barang_id', $stok->barang_id)->first();
            if ($stokTotal) {
                $originalStok = $stok->getOriginal('stok_jumlah');
                $stokTotal->stok_jumlah = $stokTotal->stok_jumlah - $originalStok + $stok->stok_jumlah;
                $stokTotal->save();
            }
        });

        // Saat stok dihapus
        static::deleted(function ($stok) {
            $stokTotal = StokTotalModel::where('barang_id', $stok->barang_id)->first();
            if ($stokTotal) {
                $stokTotal->stok_jumlah -= $stok->stok_jumlah;
                $stokTotal->save();
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}