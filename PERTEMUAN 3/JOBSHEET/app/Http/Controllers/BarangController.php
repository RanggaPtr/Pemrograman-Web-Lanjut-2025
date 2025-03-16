<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    // Display the list of barang
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object)[
            'title' => 'Daftar Barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang'; // Set active menu

        // Fetch all categories and pass them to the view
        $kategori = KategoriModel::all(); // Correct this, you need to fetch categories

        // Fetch all barang (products)
        $barang = BarangModel::all();

        return view('barang.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,  // Corrected variable name: 'barang' not 'kategori'
            'kategori' => $kategori,  // Pass kategori data
            'activeMenu' => $activeMenu
        ]);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Barang Baru'
        ];

        // Fetch categories for the dropdown
        $kategori = KategoriModel::all();

        $activeMenu = 'barang'; // Set active menu

        // Return the 'barang.create' view
        return view('barang.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    // Fetch the data for DataTables
    public function list(Request $request)
    {
        // Eager-load 'kategori' so that kategori.kategori_id or kategori.kategori_kode is available in JSON
        $barangQuery = BarangModel::with('kategori')
            ->select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id');

        // Optional filter by 'barang_kode' if needed
        if ($request->barang_kode) {
            $barangQuery->where('barang_kode', $request->barang_kode);
        }

        return DataTables::of($barangQuery)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                $btn  = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/barang/' . $barang->barang_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    // Store a new barang
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

    // Show the details of a barang
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

    // Show the form to edit a barang
    public function edit(string $id)
{
    // Find the barang by its id
    $barang = BarangModel::find($id);

    // If not found, redirect with an error message
    if (!$barang) {
        return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
    }

    // Fetch all categories for the dropdown
    $kategori = KategoriModel::all();

    $breadcrumb = (object)[
        'title' => 'Edit Barang',
        'list' => ['Home', 'Barang', 'Edit']
    ];

    $page = (object)[
        'title' => 'Edit Barang'
    ];

    $activeMenu = 'barang';

    // Pass the found barang as $barang (not $barangData) to keep it consistent
    return view('barang.edit', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'barang' => $barang,
        'kategori' => $kategori,
        'activeMenu' => $activeMenu
    ]);
}


    // Update the barang data
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

    // Delete a barang
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
