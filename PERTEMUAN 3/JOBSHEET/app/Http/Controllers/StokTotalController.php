<?php

namespace App\Http\Controllers;

use App\Models\StokTotalModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StokTotalController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Stok Total',
            'list' => ['Home', 'Stok Total']
        ];

        $page = (object) [
            'title' => 'Daftar stok total barang dalam sistem'
        ];

        $activeMenu = 'stok_total';
        $barangs = BarangModel::all();

        return view('stokTotal.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barangs' => $barangs,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $stokTotals = StokTotalModel::select('stok_total_id', 'barang_id', 'stok_jumlah')
            ->with('barang');

        if ($request->barang_id) {
            $stokTotals->where('barang_id', $request->barang_id);
        }

        return DataTables::of($stokTotals)
            ->addIndexColumn()
            ->addColumn('barang_nama', function ($stokTotal) {
                return $stokTotal->barang ? $stokTotal->barang->barang_nama : 'N/A';
            })
            ->make(true);
    }
}