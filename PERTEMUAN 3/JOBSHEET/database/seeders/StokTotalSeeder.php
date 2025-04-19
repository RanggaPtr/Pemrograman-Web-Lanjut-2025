<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokTotalSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua barang yang ada di t_stok
        $barang_ids = DB::table('t_stok')->distinct()->pluck('barang_id');

        foreach ($barang_ids as $barang_id) {
            // Hitung total stok untuk setiap barang
            $total_stok = DB::table('t_stok')
                ->where('barang_id', $barang_id)
                ->sum('stok_jumlah');

            // Simpan ke t_stok_total
            DB::table('t_stok_total')->updateOrInsert(
                ['barang_id' => $barang_id],
                ['stok_jumlah' => $total_stok, 'updated_at' => now()]
            );
        }
    }
}