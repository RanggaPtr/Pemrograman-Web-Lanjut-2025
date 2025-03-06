<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

        // $data=[
        //     'level_id'=>2,
        //     'username'=>'manager-3',
        //     'nama'=>'Manager_3',
        //     'password'=>Hash::make('12345'),
        // ];
        // UserModel::create($data);

        // // akses table
        $user =UserModel::all();
        return view('user',['data'=>$user]);

        // $user=UserModel::find(1);
        // return view('user',['data'=>$user]);

        // $user=UserModel::Where('level_id',1)->first();
        // return view('user',['data'=>$user]);

        // $user=UserModel::firstWhere('level_id',1);
        // return view('user',['data'=>$user]);

        // $user=UserModel::findOr(20,['username','nama'],function(){
        //     abort(404);
        // });
        // return view('user',['data'=>$user]);

        // $user=UserModel::findOrFail(100);
        // return view('user',['data'=>$user]);

        // $user=UserModel::where('username','manager-3')->firstOrFail();
        // return view('user',['data'=>$user]);


        // $user=UserModel::where('level_id',2)->count();
        // // dd($user);
        // return view('user',['data'=>$user]);

        // $user=UserModel::firstOrCreate(
        //     [
        //         'username'=>'manager22',
        //         'nama'=>'Manager dua dua',
        //         'password'=>Hash::make('12345'),
        //         'level_id'=>2
        //     ]
        //     );
        // return view('user',['data'=>$user]);

        // $user = UserModel::firstOrNew(
        //     [
        //         'username' => 'manager',
        //         'nama' => 'Manager',
        //     ]
        // );
        // return view('user', ['data' => $user]);
        // $user = UserModel::firstOrNew(
        //     [
        //         'username' => 'manager33',
        //         'nama' => 'Manager tiga itga',
        //         'password' => Hash::make('12345'),
        //         'level_id' => 2
        //     ]
        // );
        // $user->save();
        // return view('user', ['data' => $user]);

        // $user=UserModel::create([
        //     'username'=>'manager55',
        //     'nama'=>'Manager55',
        //     'password'=>Hash::make('12345'),
        //     'level_id'=>2
        // ]);
        
        // $user->username='manager56';
        
        // $user->isDirty(); //true
        // $user->isDirty('username'); //true
        // $user->isDirty('nama'); //false
        // $user->isDirty(['nama','username']); //true
        
        // $user->isClean(); //false
        // $user->isClean('username'); //false
        // $user->isClean('nama'); //true
        // $user->isClean(['nama','username']); //false
        
        // $user->save();
        
        // $user->isDirty();
        // $user->isClean();
        // dd($user->isDirty());
        
        // $user=UserModel::create([
        //     'username'=>'manager11',
        //     'nama'=>'Manager11',
        //     'password'=>Hash::make('12345'),
        //     'level_id'=>2
        // ]);

        // $user->username='manager12';
        // $user->save();

        // $user->wasChanged();//true
        // $user->wasChanged('username'); //true
        // $user->wasChanged(['username','level_id']); //true
        // $user->wasChanged('nama'); // false
        // dd($user->wasChanged(['nama','username'])); //true

        // $user=UserModel::all();
        // return view('user',['data'=>$user]);

        // $user=UserModel::with('level')->get();
        // dd($user);
    }

    // CONTROLLER ADD
    public function tambah(){
     return view('user_tambah') ;  
    }

    public function tambah_simpan(Request $request){
        // memvalidasi untuk diisi semua agar tidak terjadi input kosong yang tidak diinginkan dan menyebabkan kekacauan sistem
        $request->validate([
            'username' => 'required',
            'nama' => 'required',
            'password' => 'required',
            'level_id' => 'required'
        ]);
    
        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
            'level_id' => $request->level_id
        ]);
    
        return redirect('/user')->with('success', 'User berhasil ditambahkan');
    }
    
    
    // CONTROLLER UPDATE
    public function ubah($id){
        $user = UserModel::find($id);
        return view('user_ubah',['data'=>$user]);
    }

    public function ubah_simpan($id,Request $request){
        $user = UserModel::find($id);

        $user->username=$request->username;
        $user->nama=$request->nama;
        $user->password=Hash::make('$request->password');
        $user->level_id=$request->level_id;

        $user->save();
        return redirect('/user');
    }

    public function hapus($id){
        $user=UserModel::find($id);
        $user->delete();

        return redirect('/user');
    }
}
