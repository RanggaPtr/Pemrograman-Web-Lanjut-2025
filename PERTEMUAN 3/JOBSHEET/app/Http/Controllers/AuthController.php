<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home 
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('login');
    }

    // Metode untuk menampilkan form registrasi
    public function register()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        $levels = LevelModel::all(); // Ambil semua level untuk dropdown
        return view('auth.register', compact('levels'));
    }

    // Metode untuk menyimpan data registrasi
    public function postregister(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            // Validasi input
            $request->validate([
                'username' => 'required|string|min:4|unique:m_user,username',
                'nama' => 'required|string|max:255',
                'password' => 'required|string|min:4|confirmed',
                'level_id' => 'required|exists:m_level,level_id', // Pastikan level_id ada di tabel m_level
            ]);

            // Buat pengguna baru
            $user = new UserModel();
            $user->username = $request->username;
            $user->nama = $request->nama;
            $user->password = Hash::make($request->password); // Hash password
            $user->level_id = $request->level_id;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Registrasi Berhasil! Silakan login.',
                'redirect' => url('login')
            ]);
        }

        return redirect('register');
    }
}
