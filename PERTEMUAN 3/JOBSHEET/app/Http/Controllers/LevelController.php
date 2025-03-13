<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index()
    {
        // DB FACADE (RAW QUERY LARAVEL)

        // DB::insert('insert into m_level(level_kode,level_nama,created_at) values(?,?,?)',['CUS','Pelanggan',now()]);
        // return 'insert data baru berhasil';

        // $row = DB::update('update m_level set level_nama=? where level_kode = ?',['Customer','CUS']);
        // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row.' baris'; 

        // $row=DB::delete('delete from m_level where level_kode=?',['CUS']);
        // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';

        // $data=DB::select('select * from m_level');
        // return view('level',['data'=>$data]);

        $breadcrumb = (object)[
            'title' => 'Daftar Level',
            'list' => ['Home', 'level']
        ];

        $page = (object)[
            'title' => 'Daftar Level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level'; //set menu yang aktif

        $level = LevelModel::all(); //ambil data level untuk filter level

        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        // Ambil data level dengan data terkait user (jika perlu)
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');
        // Filter berdasarkan level_kode (bukan level_id)
        if ($request->level_kode) {
            $levels->where('level_kode', $request->level_kode);
        }

        // Ambil data dan olah untuk DataTables
        return DataTables::of($levels)
            ->addIndexColumn() // Menambahkan kolom index
            ->addColumn('aksi', function ($level) {  // Menambahkan kolom aksi
                $btn  = '<a href="' . url('/level/' . $level->level_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Menandakan kolom aksi adalah HTML
            ->make(true);
    }

     // Menampilkan halaman form tambah user
     public function create()
     {
         $breadcrumb = (object) [
             'title' => 'Tambah Level',
             'list' => ['Home', 'Level', 'Tambah']
         ];
 
         $page = (object) [
             'title' => 'Tambah Level baru'
         ];
 
         $level = LevelModel::all(); // ambil data level untuk ditampilkan di form
         $activeMenu = 'level'; // set menu yang aktif
 
         return view('level.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
     }
 
     // Menyimpan data user baru
     public function store(Request $request)
     {
         $request->validate([
             'level_id' => 'required|integer' ,// level_id harus diisi dan berupa angka
             // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
             'level_kode' => 'required|string|min:3',
             'level_nama' => 'required|string|max:20', // nama harus diisi, berupa string, dan maksimal 100 karakter
         ]);
 
         LevelModel::create([
             'level_id' => $request->level_id,
             'level_kode' => $request->level_kode,
             'level_nama' => $request->level_nama
         ]);
 
         return redirect('/level')->with('success', 'Data user berhasil disimpan');
     }
 
     // Menampilkan detail user
     public function show(string $id)
     {
         $user = LevelModel::with('level')->find($id);
 
         $breadcrumb = (object) [
             'title' => 'Detail Level',
             'list' => ['Home', 'Level', 'Detail']
         ];
 
         $page = (object) [
             'title' => 'Detail Level'
         ];
 
         $activeMenu = 'level'; // set menu yang sedang aktif
 
         return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
     }
 
     // Menampilkan halaman form edit user
     public function edit(string $id)
     {
         $user = LevelModel::find($id);
         $level = LevelModel::all();
 
         $breadcrumb = (object) [
             'title' => 'Edit Level',
             'list' => ['Home', 'Level', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit Level'
         ];
 
         $activeMenu = 'level'; // set menu yang sedang aktif
 
         return view('level.edit', [
             'breadcrumb' => $breadcrumb,
             'page' => $page,
             'user' => $user,
             'level' => $level,
             'activeMenu' => $activeMenu
         ]);
     }
 
     // Menyimpan perubahan data user
     public function update(Request $request, string $id)
     {
         $request->validate([
            'level_id' => 'required|integer' ,// level_id harus diisi dan berupa angka
            // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
            'level_kode' => 'required|string|min:3',
            'level_nama' => 'required|string|max:20', // nama harus diisi, berupa string, dan maksimal 100 karakter
         ]);
 
         LevelModel::find($id)->update([
             'level_id' => $request->level_id,
             'level_kode' => $request->level_kode,
             'level_nama' => $request->level_nama
         ]);
 
         return redirect('/level')->with('success', 'Data user berhasil diubah');
     }
 
     // Menghapus data user
     public function destroy(string $id)
     {
         $check = LevelModel::find($id); // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
         if (!$check) {
             return redirect('/level')->with('error', 'Data user tidak ditemukan');
         }
 
         try {
             LevelModel::destroy($id); // Hapus data level
             return redirect('/level')->with('success', 'Data user berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
             return redirect('/level')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }
}
