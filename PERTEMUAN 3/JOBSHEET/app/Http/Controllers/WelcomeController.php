<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WelcomeController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];
        $activeMenu = 'dashboard';

        return view('welcome', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }

    // Method untuk menangani AJAX call DataTables di Dashboard
    public function stokTotalList(Request $request)
    {
        // Ambil semua data stok dan kelompokkan berdasarkan barang_id
        $stok = StokModel::selectRaw('barang_id, SUM(stok_jumlah) as total_stok')
            ->groupBy('barang_id')
            ->with('barang'); //  relasi dengan m_barang

        // Kembalikan data dalam format DataTables
        return DataTables::of($stok)
            ->addIndexColumn()
            ->addColumn('barang_nama', function ($stok) {
                return $stok->barang->barang_nama ?? 'N/A';
            })
            ->addColumn('total_stok', function ($stok) {
                return $stok->total_stok;
            })
            ->rawColumns(['barang_nama', 'total_stok'])
            ->make(true);
    }

    public function omsetKotor(Request $request)
    {
        // Ambil total omset kotor (sum total harga dari semua transaksi)
        $totalOmset = PenjualanModel::sum('total_harga');

        // Format total omset ke dalam format Rupiah
        $formattedOmset =  number_format($totalOmset ?? 0, 2, ',', '.');

        // Format data untuk response JSON
        $data = [
            [
                'title' => 'Total Omset Kotor',
                'amount' => $formattedOmset, // Mengembalikan nilai yang sudah diformat
            ]
        ];

        return response()->json(['data' => $data]);
    }
}
