<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Supplier 1 (PT Maju Jaya)
            ['barang_id' => 1, 'kategori_id' => 1, 'barang_kode' => 'BRG001', 'barang_nama' => 'Nasi Kotak', 'harga_beli' => 15000, 'harga_jual' => 20000],
            ['barang_id' => 2, 'kategori_id' => 1, 'barang_kode' => 'BRG002', 'barang_nama' => 'Mie Instan', 'harga_beli' => 2500, 'harga_jual' => 3500],
            ['barang_id' => 3, 'kategori_id' => 2, 'barang_kode' => 'BRG003', 'barang_nama' => 'Teh Botol', 'harga_beli' => 4000, 'harga_jual' => 6000],
            ['barang_id' => 4, 'kategori_id' => 2, 'barang_kode' => 'BRG004', 'barang_nama' => 'Kopi Sachet', 'harga_beli' => 2000, 'harga_jual' => 3000],
            ['barang_id' => 5, 'kategori_id' => 3, 'barang_kode' => 'BRG005', 'barang_nama' => 'Rice Cooker', 'harga_beli' => 250000, 'harga_jual' => 300000],

            // Supplier 2 (CV Sukses Bersama)
            ['barang_id' => 6, 'kategori_id' => 3, 'barang_kode' => 'BRG006', 'barang_nama' => 'Blender', 'harga_beli' => 180000, 'harga_jual' => 220000],
            ['barang_id' => 7, 'kategori_id' => 3, 'barang_kode' => 'BRG007', 'barang_nama' => 'Setrika', 'harga_beli' => 120000, 'harga_jual' => 150000],
            ['barang_id' => 8, 'kategori_id' => 4, 'barang_kode' => 'BRG008', 'barang_nama' => 'Kemeja Pria', 'harga_beli' => 100000, 'harga_jual' => 150000],
            ['barang_id' => 9, 'kategori_id' => 4, 'barang_kode' => 'BRG009', 'barang_nama' => 'Celana Jeans', 'harga_beli' => 180000, 'harga_jual' => 220000],
            ['barang_id' => 10, 'kategori_id' => 5, 'barang_kode' => 'BRG010', 'barang_nama' => 'Meja Belajar', 'harga_beli' => 350000, 'harga_jual' => 400000],

            // Supplier 3 (UD Makmur Sentosa)
            ['barang_id' => 11, 'kategori_id' => 5, 'barang_kode' => 'BRG011', 'barang_nama' => 'Kursi Kantor', 'harga_beli' => 500000, 'harga_jual' => 600000],
            ['barang_id' => 12, 'kategori_id' => 5, 'barang_kode' => 'BRG012', 'barang_nama' => 'Lemari Kayu', 'harga_beli' => 750000, 'harga_jual' => 850000],
            ['barang_id' => 13, 'kategori_id' => 1, 'barang_kode' => 'BRG013', 'barang_nama' => 'Roti Tawar', 'harga_beli' => 12000, 'harga_jual' => 15000],
            ['barang_id' => 14, 'kategori_id' => 2, 'barang_kode' => 'BRG014', 'barang_nama' => 'Susu Kotak', 'harga_beli' => 5000, 'harga_jual' => 7000],
            ['barang_id' => 15, 'kategori_id' => 4, 'barang_kode' => 'BRG015', 'barang_nama' => 'Jaket Kulit', 'harga_beli' => 300000, 'harga_jual' => 350000],
        ];
        DB::table('m_barang')->insert($data);
    }
}
