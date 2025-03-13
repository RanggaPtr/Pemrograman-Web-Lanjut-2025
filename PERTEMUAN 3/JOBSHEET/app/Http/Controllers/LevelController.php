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
}
