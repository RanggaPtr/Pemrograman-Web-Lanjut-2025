<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenjualanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request)
    {
        Log::info('Request Data: ', $request->all()); // Log semua data yang diterima

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:50',
            'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => now(),
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $penjualanTanggal = Carbon::parse($request->penjualan_tanggal)->format('Y-m-d H:i:s');

        $imageName = $request->file('image')->hashName();
        $request->file('image')->storeAs('penjualan', $imageName, 'public');

        $penjualan = PenjualanModel::create([
            'user_id' => $request->user_id,
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => $penjualanTanggal,
            'image' => $imageName
        ]);

        return response()->json([
            'success' => true,
            'penjualan' => $penjualan
        ], 201);
    }

    public function index()
    {
        $penjualans = PenjualanModel::all();
        return response()->json($penjualans, 200);
    }
}