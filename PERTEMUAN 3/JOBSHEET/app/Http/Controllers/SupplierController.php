<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index()
    {

        $breadcrumb = (object)[
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];

        $page = (object)[
            'title' => 'Daftar Supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier'; //set menu yang aktif

        $level=SupplierModel::all(); //ambil data level untuk filter level

        return view('supplier.index', ['breadcrumb' => $breadcrumb, 'page' => $page,'level'=>$level, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');
           

        // filter data user bedasarkan level_id
        if ($request->kategori_id) {
            $suppliers->where('supplier_id',$request->kategori_id);
        }

        return DataTables::of($suppliers)
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {  // menambahkan kolom aksi
                $btn  = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->supplier_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    // Menampilkan halaman form tambah user
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Supplier baru'
        ];

        $level = SupplierModel::all(); // ambil data level untuk ditampilkan di form
        $activeMenu = 'supplier'; // set menu yang aktif

        return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan data user baru
    public function store(Request $request)
    {
        $request->validate([
            // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
            'supplier_id' => 'required|string|min:3',
            'supplier_kode' => 'required|string|max:6', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'supplier_nama' => 'required|string|max:50', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'supplier_alamat' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
        ]);

        SupplierModel::create([
            'supplier_id' => $request->kategori_id,
            'supplier_kode' => $request->supplier_kode,
            'level_nama' => $request->supplier_nama,
            'level_alamat' => $request->supplier_nama

        ]);

        return redirect('/supplier')->with('success', 'Data user berhasil disimpan');
    }

    // Menampilkan detail user
    public function show(string $id)
    {
        $user = SupplierModel::with('supplier')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail user'
        ];

        $activeMenu = 'user'; // set menu yang sedang aktif

        return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    // // Menampilkan halaman form edit user
    // public function edit(string $id)
    // {
    //     $user = UserModel::find($id);
    //     $level = LevelModel::all();

    //     $breadcrumb = (object) [
    //         'title' => 'Edit User',
    //         'list' => ['Home', 'User', 'Edit']
    //     ];

    //     $page = (object) [
    //         'title' => 'Edit user'
    //     ];

    //     $activeMenu = 'user'; // set menu yang sedang aktif

    //     return view('user.edit', [
    //         'breadcrumb' => $breadcrumb,
    //         'page' => $page,
    //         'user' => $user,
    //         'level' => $level,
    //         'activeMenu' => $activeMenu
    //     ]);
    // }

    // Menyimpan perubahan data user
    // public function update(Request $request, string $id)
    // {
    //     $request->validate([
    //         // username harus diisi, berupa string, minimal 3 karakter,
    //         // dan bernilai unik di tabel m_user kolom username kecuali untuk user dengan id yang sedang diedit
    //         'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
    //         'nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
    //         'password' => 'nullable|min:5', // password bisa diisi (minimal 5 karakter) dan bisa tidak diisi
    //         'level_id' => 'required|integer' // level_id harus diisi dan berupa angka
    //     ]);

    //     UserModel::find($id)->update([
    //         'username' => $request->username,
    //         'nama' => $request->nama,
    //         'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
    //         'level_id' => $request->level_id
    //     ]);

    //     return redirect('/user')->with('success', 'Data user berhasil diubah');
    // }

    // // Menghapus data user
    // public function destroy(string $id)
    // {
    //     $check = UserModel::find($id); // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
    //     if (!$check) {
    //         return redirect('/user')->with('error', 'Data user tidak ditemukan');
    //     }

    //     try {
    //         UserModel::destroy($id); // Hapus data level
    //         return redirect('/user')->with('success', 'Data user berhasil dihapus');
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
    //         return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
    //     }
    // }
}
