<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penjualanDetailData = [
            ['penjualan_id' => 1, 'barang_id' => 1, 'harga' => 15000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 1, 'barang_id' => 2, 'harga' => 20000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 1, 'barang_id' => 3, 'harga' => 10000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 2, 'barang_id' => 4, 'harga' => 18000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 2, 'barang_id' => 5, 'harga' => 22000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 2, 'barang_id' => 6, 'harga' => 12000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 3, 'barang_id' => 7, 'harga' => 25000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 3, 'barang_id' => 8, 'harga' => 17000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 3, 'barang_id' => 9, 'harga' => 19000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 4, 'barang_id' => 10, 'harga' => 30000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 4, 'barang_id' => 11, 'harga' => 16000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 4, 'barang_id' => 12, 'harga' => 14000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 5, 'barang_id' => 13, 'harga' => 27000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 5, 'barang_id' => 14, 'harga' => 21000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 5, 'barang_id' => 15, 'harga' => 23000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 6, 'barang_id' => 1, 'harga' => 26000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 6, 'barang_id' => 2, 'harga' => 24000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 6, 'barang_id' => 3, 'harga' => 15000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 7, 'barang_id' => 4, 'harga' => 18000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 7, 'barang_id' => 5, 'harga' => 22000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 7, 'barang_id' => 6, 'harga' => 12000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 8, 'barang_id' => 7, 'harga' => 25000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 8, 'barang_id' => 8, 'harga' => 17000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 8, 'barang_id' => 9, 'harga' => 19000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 9, 'barang_id' => 10, 'harga' => 30000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 9, 'barang_id' => 11, 'harga' => 16000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 9, 'barang_id' => 12, 'harga' => 14000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 10, 'barang_id' => 13, 'harga' => 27000, 'jumlah' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 10, 'barang_id' => 14, 'harga' => 21000, 'jumlah' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['penjualan_id' => 10, 'barang_id' => 15, 'harga' => 23000, 'jumlah' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('t_penjualan_detail')->insert($penjualanDetailData);
    }
}
