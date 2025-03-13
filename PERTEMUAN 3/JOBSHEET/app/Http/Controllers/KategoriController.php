<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    public function index()
    {
        // QUERY BUILDER

        // $data=[
        //     'kategori_kode'=>'SNK',
        //     'kategori_nama'=>'Snack/Makanan Ringan',
        //     'created_at'=>now()
        // ];
        // DB::table('m_kategori')->insert($data);
        // return 'insert data baru, berhasil !!!';

        // $row=DB::table('m_kategori')->where('kategori_kode','SNK')->update(['kategori_nama'=>'camilan']);
        // return 'Update data berhasil. jumlah data yang diupdate: '. $row .' baris';

        // $row=DB::table('m_kategori')->where('kategori_kode','SNK')->delete();
        // return 'Delete data berhasil. jumlah data yang dihapus: '. $row .' baris';

        // $data=DB::table('m_kategori')->get();
        // return view('kategori',['data'=>$data]);

        $breadcrumb = (object)[
            'title' => 'Kategori Barang',
            'list' => ['Home', 'kategori']
        ];

        $page = (object)[
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori'; //set menu yang aktif

        $level = KategoriModel::all(); //ambil data level untuk filter level

        return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }


    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        // Fetch categories
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        // Filter by kategori_id if provided
        if ($request->kategori_id) {
            $kategori->where('kategori_id', $request->kategori_id);
        }

        // Return data as JSON to DataTables
        return DataTables::of($kategori)
            ->addIndexColumn()  // Adds a row index to DataTables
            ->addColumn('aksi', function ($kategori) {  // Add action buttons for each row
                $btn  = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi'])  // Ensure that HTML content in 'aksi' is rendered correctly
            ->make(true);
    }


    // Menampilkan halaman form tambah user
    // public function create()
    // {
    //     $breadcrumb = (object) [
    //         'title' => 'Tambah Supplier',
    //         'list' => ['Home', 'Supplier', 'Tambah']
    //     ];

    //     $page = (object) [
    //         'title' => 'Tambah Supplier baru'
    //     ];

    //     $level = SupplierModel::all(); // ambil data level untuk ditampilkan di form
    //     $activeMenu = 'supplier'; // set menu yang aktif

    //     return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    // }

    // // Menyimpan data user baru
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
    //         'supplier_id' => 'required|string|min:3',
    //         'supplier_kode' => 'required|string|max:6', // nama harus diisi, berupa string, dan maksimal 100 karakter
    //         'supplier_nama' => 'required|string|max:50', // nama harus diisi, berupa string, dan maksimal 100 karakter
    //         'supplier_alamat' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
    //     ]);

    //     SupplierModel::create([
    //         'supplier_id' => $request->kategori_id,
    //         'supplier_kode' => $request->supplier_kode,
    //         'level_nama' => $request->supplier_nama,
    //         'level_alamat' => $request->supplier_nama

    //     ]);

    //     return redirect('/supplier')->with('success', 'Data user berhasil disimpan');
    // }

    // // Menampilkan detail user
    // public function show(string $id)
    // {
    //     $user = SupplierModel::with('supplier')->find($id);

    //     $breadcrumb = (object) [
    //         'title' => 'Detail User',
    //         'list' => ['Home', 'User', 'Detail']
    //     ];

    //     $page = (object) [
    //         'title' => 'Detail user'
    //     ];

    //     $activeMenu = 'user'; // set menu yang sedang aktif

    //     return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    // }
}
