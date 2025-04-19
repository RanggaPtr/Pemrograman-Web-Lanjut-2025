<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object) [
            'title' => 'Daftar stok barang dalam sistem'
        ];

        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stock_tanggal', 'stok_jumlah')
            ->with(['supplier', 'barang', 'user']);

        if ($request->supplier_id) {
            $stoks->where('supplier_id', $request->supplier_id);
        }

        if ($request->barang_id) {
            $stoks->where('barang_id', $request->barang_id);
        }

        if ($request->user_id) {
            $stoks->where('user_id', $request->user_id);
        }

        return DataTables::of($stoks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Stok',
            'list' => ['Home', 'Stok', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah stok barang baru'
        ];

        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
            'barang_id' => 'required|integer|exists:m_barang,barang_id',
            'user_id' => 'required|integer|exists:m_user,user_id',
            'stock_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        StokModel::create([
            'supplier_id' => $request->supplier_id,
            'barang_id' => $request->barang_id,
            'user_id' => $request->user_id,
            'stock_tanggal' => $request->stock_tanggal,
            'stok_jumlah' => $request->stok_jumlah,
        ]);

        return redirect('/stok')->with('success', 'Data stok berhasil disimpan');
    }

    public function edit(string $id)
    {
        $stok = StokModel::find($id);
        if (!$stok) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Stok',
            'list' => ['Home', 'Stok', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit data stok barang'
        ];

        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'stok' => $stok,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
            'barang_id' => 'required|integer|exists:m_barang,barang_id',
            'user_id' => 'required|integer|exists:m_user,user_id',
            'stock_tanggal' => 'required|date',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        $stok = StokModel::find($id);
        if ($stok) {
            $stok->update([
                'supplier_id' => $request->supplier_id,
                'barang_id' => $request->barang_id,
                'user_id' => $request->user_id,
                'stock_tanggal' => $request->stock_tanggal,
                'stok_jumlah' => $request->stok_jumlah,
            ]);
            return redirect('/stok')->with('success', 'Data stok berhasil diupdate');
        } else {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }
    }

    public function create_ajax()
    {
        $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        $users = UserModel::select('user_id', 'nama')->get();
        return view('stok.create_ajax', [
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users
        ]);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'stock_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            StokModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        $users = UserModel::select('user_id', 'nama')->get();
        return view('stok.edit_ajax', [
            'stok' => $stok,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users
        ]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'stock_tanggal' => 'required|date',
                'stok_jumlah' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $stok = StokModel::find($id);
                if ($stok) {
                    $stok->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data stok berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait'
                ]);
            }
        }
        return redirect('/');
    }

    public function destroy($id)
    {
        $stok = StokModel::find($id);
        if ($stok) {
            try {
                $stok->delete();
                return redirect('/stok')->with('success', 'Data stok berhasil dihapus');
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect('/stok')->with('error', 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait');
            }
        } else {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }
    }
}