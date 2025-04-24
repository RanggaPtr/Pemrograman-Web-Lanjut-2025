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

    public function stokTotalList(Request $request)
    {
        // Ambil semua data stok, kelompokkan berdasarkan barang_id, dan hanya tampilkan stok >= 0
        $stok = StokModel::selectRaw('barang_id, SUM(stok_jumlah) as total_stok')
            ->groupBy('barang_id')
            ->havingRaw('SUM(stok_jumlah) >= 0')
            ->with('barang');

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

        $formattedOmset =  number_format($totalOmset ?? 0, 2, ',', '.');

        // Format data untuk response JSON
        $data = [
            [
                'title' => 'Total Omset Kotor',
                'amount' => $formattedOmset ?? 0,
            ]
        ];

        return response()->json(['data' => $data]);
    }
}