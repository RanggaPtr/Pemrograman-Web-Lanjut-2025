<?php

namespace App\Http\Controllers;

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
            ->with('barang'); // Pastikan relasi dengan m_barang

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
}
