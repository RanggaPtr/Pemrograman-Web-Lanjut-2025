<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
     // Display the list of categories (kategori)
     public function index()
     {
         $breadcrumb = (object)[
             'title' => 'Daftar Kategori',
             'list' => ['Home', 'Kategori']
         ];
 
         $page = (object)[
             'title' => 'Daftar Kategori yang terdaftar dalam sistem'
         ];
 
         $activeMenu = 'kategori'; // Set active menu
 
         $kategori = KategoriModel::all(); // Fetch all categories (kategori)
 
         return view('kategori.index', [
             'breadcrumb' => $breadcrumb,
             'page' => $page,
             'kategori' => $kategori,  // Pass the kategori data to the view
             'activeMenu' => $activeMenu
         ]);
     }
 
     // Fetch the data for DataTables
     public function list(Request $request)
     {
         $kategoriQuery = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');
 
         // Filter based on kategori_kode if passed in the request
         if ($request->kategori_kode) {
             $kategoriQuery->where('kategori_kode', $request->kategori_kode);
         }
 
         return DataTables::of($kategoriQuery)
             ->addIndexColumn()  // Add index column
             ->addColumn('aksi', function ($kategori) {
                 $btn  = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                 $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                 $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
                     . csrf_field() . method_field('DELETE') . 
                     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                 return $btn;
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }

    // Show the form to create a new barang (product)
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Barang Baru'
        ];

        $kategori = KategoriModel::all(); // Get all categories

        $activeMenu = 'barang'; // Set active menu

        return view('barang.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori, // Pass the kategori data to the view
            'activeMenu' => $activeMenu
        ]);
    }

    // Store a new barang (product)
    public function store(Request $request)
    {
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',  // Ensure barang_kode is unique
            'barang_nama' => 'required|string|max:100', // Validate barang_nama
            'harga_beli' => 'required|numeric', // Validate harga_beli
            'harga_jual' => 'required|numeric', // Validate harga_jual
            'kategori_id' => 'required|exists:m_kategori,kategori_id', // Validate kategori_id exists
        ]);

        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
    }

    // Show the details of a barang (product)
    public function show(string $id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Barang'
        ];

        $activeMenu = 'barang'; // Set active menu

        return view('barang.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,
            'activeMenu' => $activeMenu
        ]);
    }

    // Show the form to edit a barang (product)
    public function edit(string $id)
    {
        $barangData = BarangModel::find($id);

        if (!$barangData) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        $kategori = KategoriModel::all(); // Get all categories

        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Barang'
        ];

        $activeMenu = 'barang'; // Set active menu

        return view('barang.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barangData' => $barangData, // Pass the current barang data to the view
            'kategori' => $kategori, // Pass the categories to the view
            'activeMenu' => $activeMenu
        ]);
    }

    // Update the barang (product) data
    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,' . $id . ',barang_id', // Ensure unique kode
            'barang_nama' => 'required|string|max:100', // Validate barang_nama
            'harga_beli' => 'required|numeric', // Validate harga_beli
            'harga_jual' => 'required|numeric', // Validate harga_jual
            'kategori_id' => 'required|exists:m_kategori,kategori_id', // Validate kategori_id exists
        ]);

        BarangModel::find($id)->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    // Delete a barang (product)
    public function destroy(string $id)
    {
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            BarangModel::destroy($id); // Delete barang
            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
