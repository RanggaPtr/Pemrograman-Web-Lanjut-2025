<?php
namespace App\Http\Controllers\Api;

use App\Models\UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:m_user,username|max:255',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
            'level_id' => 'required|integer|exists:m_level,level_id',
            'foto' => 'nullable|string', // Opsional, bisa berupa URL atau nama file
        ]);

        // Jika validasi gagal, kembalikan error 422
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Buat pengguna baru
        $user = UserModel::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Hash password
            'nama' => $request->nama,
            'level_id' => $request->level_id,
            'foto' => $request->foto, // Nullable, akan null jika tidak diisi
        ]);

        // Buat token JWT untuk pengguna
        try {
            $token = JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan token',
            ], 500);
        }

        // Kembalikan respons sukses dengan data pengguna dan token
        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}