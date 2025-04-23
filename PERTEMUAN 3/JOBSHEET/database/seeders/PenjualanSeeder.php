<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['penjualan_id' => 1, 'user_id' => 1, 'pembeli' => 'Budi Santoso', 'penjualan_kode' => 'PJ001', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>100000],
            ['penjualan_id' => 2, 'user_id' => 2, 'pembeli' => 'Siti Aminah', 'penjualan_kode' => 'PJ002', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>200000],
            ['penjualan_id' => 3, 'user_id' => 3, 'pembeli' => 'Dewi Lestari', 'penjualan_kode' => 'PJ003', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>150000],
            ['penjualan_id' => 4, 'user_id' => 1, 'pembeli' => 'Joko Widodo', 'penjualan_kode' => 'PJ004', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>300000],
            ['penjualan_id' => 5, 'user_id' => 2, 'pembeli' => 'Ahmad Fauzi', 'penjualan_kode' => 'PJ005', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>250000],
            ['penjualan_id' => 6, 'user_id' => 3, 'pembeli' => 'Rina Sari', 'penjualan_kode' => 'PJ006', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>180000],
            ['penjualan_id' => 7, 'user_id' => 1, 'pembeli' => 'Fajar Prasetyo', 'penjualan_kode' => 'PJ007', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>220000],
            ['penjualan_id' => 8, 'user_id' => 2, 'pembeli' => 'Wahyu Saputra', 'penjualan_kode' => 'PJ008', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>160000],
            ['penjualan_id' => 9, 'user_id' => 3, 'pembeli' => 'Lina Susanti', 'penjualan_kode' => 'PJ009', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>190000],
            ['penjualan_id' => 10, 'user_id' => 1, 'pembeli' => 'Samsul Bahri', 'penjualan_kode' => 'PJ010', 'penjualan_tanggal' => Carbon::now(),'total_harga'=>270000],
        ];

        DB::table('t_penjualan')->insert($data);
    }
}
