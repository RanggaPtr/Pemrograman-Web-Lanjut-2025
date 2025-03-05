<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function index(){
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

        $data=[
            'level_id'=>2,
            'username'=>'manager-3',
            'nama'=>'Manager_3',
            'password'=>Hash::make('12345'),
        ];
        UserModel::create($data);
        
        // akses table
        $user =UserModel::all();
        return view('user',['data'=>$user]);
    }
}
