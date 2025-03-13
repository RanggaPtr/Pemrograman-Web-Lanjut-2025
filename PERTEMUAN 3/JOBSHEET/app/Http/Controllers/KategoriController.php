<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class KategoriController extends Controller
{
    public function index(){
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
            'title' => 'Kategori User',
            'list' => ['Home', 'kategori']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user'; //set menu yang aktif

        $level=KategoriModel::all(); //ambil data level untuk filter level

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page,'level'=>$level, 'activeMenu' => $activeMenu]);
    }
}
