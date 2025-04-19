<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetailModel extends Model
{
    use HasFactory;

    protected $table = 't_penjualan_detail';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'harga',
        'jumlah',
    ];

    protected static function boot()
    {
        parent::boot();

        // Saat detail penjualan ditambahkan
        static::created(function ($detail) {
            $stokTotal = StokTotalModel::where('barang_id', $detail->barang_id)->first();
            if ($stokTotal) {
                $stokTotal->stok_jumlah -= $detail->jumlah;
                if ($stokTotal->stok_jumlah < 0) {
                    throw new \Exception('Stok barang ' . $detail->barang->barang_nama . ' tidak cukup untuk transaksi ini.');
                }
                $stokTotal->save();
            } else {
                throw new \Exception('Stok barang ' . $detail->barang->barang_nama . ' belum tersedia di stok total.');
            }
        });

        // Saat detail penjualan diupdate
        static::updated(function ($detail) {
            $stokTotal = StokTotalModel::where('barang_id', $detail->barang_id)->first();
            if ($stokTotal) {
                $originalJumlah = $detail->getOriginal('jumlah');
                $stokTotal->stok_jumlah = $stokTotal->stok_jumlah + $originalJumlah - $detail->jumlah;
                if ($stokTotal->stok_jumlah < 0) {
                    throw new \Exception('Stok barang ' . $detail->barang->barang_nama . ' tidak cukup untuk transaksi ini.');
                }
                $stokTotal->save();
            }
        });

        // Saat detail penjualan dihapus
        static::deleted(function ($detail) {
            $stokTotal = StokTotalModel::where('barang_id', $detail->barang_id)->first();
            if ($stokTotal) {
                $stokTotal->stok_jumlah += $detail->jumlah;
                $stokTotal->save();
            }
        });
    }

    public function penjualan()
    {
        return $this->belongsTo(PenjualanModel::class, 'penjualan_id', 'penjualan_id');
    }

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }
}