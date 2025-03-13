<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        // tambah data user dengan eloquent model
        // $data=[
        //     'username'=>'customer-1',
        //     'nama'=>'Pelanggan',
        //     'password'=>Hash::make('12345'),
        //     'level_id'=>4
        // ];

        // $data=[
        //     'nama'=>'Pelanggan Pertama',
        // ];
        // UserModel::where('username','customer-1')->update($data);

        // // akses table
        // $user =UserModel::all();
        // return view('user',['data'=>$user]);

        // menampilkan halaman aawal user
        $breadcrumb = (object)[
            'title' => 'Daftar Barang',
            'list' => ['Home', 'barang']
        ];

        $page = (object)[
            'title' => 'Daftar barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang'; //set menu yang aktif

        $level = BarangModel::all(); //ambil data level untuk filter level

        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    // public function list(Request $request)
    // {
    //     $barang = BarangModel::select('barang_id','kategori_id','barang_kode','barang_nama','harga_beli','harga_jual');

    //     // filter data user bedasarkan level_id
    //     if ($request->level_id) {
    //         $barang->where('barang_id', $request->level_id);
    //     }

    //     return DataTables::of($barang)
    //         // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($barang) {  // menambahkan kolom aksi
    //             $btn  = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
    //             $btn .= '<a href="' . url('/barang/' . $barang->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    //             $btn .= '<form class="d-inline-block" method="POST" action="' . url('/barang/' . $barang->barang_id) . '">'
    //                 . csrf_field() . method_field('DELETE') .
    //                 '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
    //         ->make(true);
    // }
    public function list(Request $request)
    {
        $barang = BarangModel::with('kategori') // Eager load the 'kategori' relationship
            ->select('barang_id', 'kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual');

        // Optional: filter based on 'barang_id' (not 'level_id' as in your current code)
        if ($request->barang_id) {
            $barang->where('barang_id', $request->barang_id);
        }

        return DataTables::of($barang)
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


    // // Menampilkan halaman form tambah user
    // public function create()
    // {
    //     $breadcrumb = (object) [
    //         'title' => 'Tambah User',
    //         'list' => ['Home', 'User', 'Tambah']
    //     ];

    //     $page = (object) [
    //         'title' => 'Tambah user baru'
    //     ];

    //     $level = LevelModel::all(); // ambil data level untuk ditampilkan di form
    //     $activeMenu = 'user'; // set menu yang aktif

    //     return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    // }

    // // Menyimpan data user baru
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
    //         'username' => 'required|string|min:3|unique:m_user,username',
    //         'nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
    //         'password' => 'required|min:5', // password harus diisi dan minimal 5 karakter
    //         'level_id' => 'required|integer' // level_id harus diisi dan berupa angka
    //     ]);

    //     UserModel::create([
    //         'username' => $request->username,
    //         'nama' => $request->nama,
    //         'password' => bcrypt($request->password), // password dienkripsi sebelum disimpan
    //         'level_id' => $request->level_id
    //     ]);

    //     return redirect('/user')->with('success', 'Data user berhasil disimpan');
    // }

    // // Menampilkan detail user
    // public function show(string $id)
    // {
    //     $user = UserModel::with('level')->find($id);

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

    // // Menyimpan perubahan data user
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
