<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar transaksi penjualan dalam sistem'
        ];

        $activeMenu = 'penjualan';
        $users = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'pembeli', 'penjualan_tanggal', 'user_id', 'total_harga')
            ->with('user');

        if ($request->user_id) {
            $penjualans->where('user_id', $request->user_id);
        }

        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Penjualan',
            'list' => ['Home', 'Penjualan', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah transaksi penjualan baru'
        ];

        $activeMenu = 'penjualan';
        $users = UserModel::all();
        $barangs = BarangModel::all();

        return view('penjualan.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'users' => $users,
            'barangs' => $barangs,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'pembeli' => 'required|string|max:50',
            'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
            'details.*.harga' => 'required|integer|min:1',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Validasi stok
            foreach ($request->details as $detail) {
                $totalStock = StokModel::where('barang_id', $detail['barang_id'])
                    ->sum('stok_jumlah');

                if ($totalStock < $detail['jumlah']) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Stok barang dengan ID {$detail['barang_id']} tidak mencukupi. Stok tersedia: {$totalStock}, jumlah yang diminta: {$detail['jumlah']}")->withInput();
                }
            }

            $totalHarga = 0;
            foreach ($request->details as $detail) {
                $totalHarga += $detail['harga'] * $detail['jumlah'];
            }

            $penjualan = PenjualanModel::create([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal,
                'total_harga' => $totalHarga,
            ]);

            foreach ($request->details as $detail) {
                PenjualanDetailModel::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'barang_id' => $detail['barang_id'],
                    'harga' => $detail['harga'],
                    'jumlah' => $detail['jumlah'],
                ]);

                // Catat stok keluar
                StokModel::create([
                    'barang_id' => $detail['barang_id'],
                    'user_id' => $request->user_id,
                    'supplier_id' => null,
                    'stock_tanggal' => now(),
                    'stok_jumlah' => -$detail['jumlah'],
                ]);
            }

            DB::commit();
            Log::info('Penjualan berhasil (store): penjualan_id=' . $penjualan->penjualan_id);

            return redirect('/penjualan')->with('success', 'Data penjualan berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan penjualan (store): ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(string $id)
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Penjualan',
            'list' => ['Home', 'Penjualan', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail transaksi penjualan'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'penjualan' => $penjualan,
            'activeMenu' => $activeMenu
        ]);
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])->find($id);
        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }

    public function create_ajax()
    {
        $users = UserModel::select('user_id', 'nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama', 'harga_jual')->get();

        $lastPenjualan = PenjualanModel::orderBy('penjualan_id', 'desc')->first();
        $newNumber = $lastPenjualan ? (int) substr($lastPenjualan->penjualan_kode, 2) + 1 : 1;
        $penjualan_kode = 'PJ' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return view('penjualan.create_ajax', [
            'users' => $users,
            'barangs' => $barangs,
            'penjualan_kode' => $penjualan_kode
        ]);
    }

    public function store_ajax(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'pembeli' => 'required|string|max:50',
            'penjualan_kode' => 'required|string|max:20',
            'penjualan_tanggal' => 'required|date',
            'total_harga' => 'required|numeric|min:0',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ], 422);
        }

        // Hitung total jumlah per barang_id
        $barangJumlah = [];
        foreach ($request->details as $detail) {
            $barangId = $detail['barang_id'];
            $jumlah = $detail['jumlah'];

            if (!isset($barangJumlah[$barangId])) {
                $barangJumlah[$barangId] = 0;
            }
            $barangJumlah[$barangId] += $jumlah;
        }

        // Cek ketersediaan stok
        foreach ($barangJumlah as $barangId => $totalJumlah) {
            // Log data yang akan digunakan untuk query
            Log::info('Mencoba mengambil stok untuk barang_id: ' . $barangId);

            // Ambil stok barang
            $stok = StokModel::where('barang_id', $barangId)->first();

            // Log hasil query
            Log::info('Hasil query stok untuk barang_id: ' . $barangId, [
                'stok' => $stok ? $stok->toArray() : null,
                'total_jumlah' => $totalJumlah
            ]);

            // Ambil nama barang untuk pesan error
            $barang = BarangModel::find($barangId);
            $barangNama = $barang ? $barang->barang_nama : 'Tidak Diketahui';

            if (!$stok) {
                Log::error('Stok tidak ditemukan untuk barang_id: ' . $barangId);
                return response()->json([
                    'status' => false,
                    'message' => "Stok barang $barangNama belum tersedia di stok total."
                ], 400);
            }

            if ($stok->stok_jumlah < $totalJumlah) {
                Log::error('Stok tidak cukup untuk barang_id: ' . $barangId, [
                    'stok_jumlah' => $stok->stok_jumlah,
                    'total_jumlah' => $totalJumlah
                ]);
                return response()->json([
                    'status' => false,
                    'message' => "Stok barang $barangNama tidak cukup. Stok tersedia: {$stok->stok_jumlah}, dibutuhkan: $totalJumlah."
                ], 400);
            }

            Log::info('Stok valid untuk barang_id: ' . $barangId, [
                'stok_jumlah' => $stok->stok_jumlah,
                'total_jumlah' => $totalJumlah
            ]);
        }

        // Simpan data penjualan
        try {
            DB::beginTransaction();

            // Simpan ke tabel t_penjualan
            $penjualan = new PenjualanModel();
            $penjualan->user_id = $request->user_id;
            $penjualan->pembeli = $request->pembeli;
            $penjualan->penjualan_kode = $request->penjualan_kode;
            $penjualan->penjualan_tanggal = $request->penjualan_tanggal;
            $penjualan->total_harga = $request->total_harga;
            $penjualan->save();

            // Simpan detail penjualan dan kurangi stok
            foreach ($request->details as $detail) {
                $barangId = $detail['barang_id'];
                $jumlah = $detail['jumlah'];

                // Simpan detail
                $penjualanDetail = new PenjualanDetailModel();
                $penjualanDetail->penjualan_id = $penjualan->penjualan_id;
                $penjualanDetail->barang_id = $barangId;
                $penjualanDetail->harga = $detail['harga'];
                $penjualanDetail->jumlah = $jumlah;
                $penjualanDetail->save();

                // Kurangi stok
                $stok = StokModel::where('barang_id', $barangId)->first();
                $stok->stok_jumlah -= $jumlah;
                $stok->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'user_id' => 'required|integer',
                'pembeli' => 'required|string|max:50',
                'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id',
                'penjualan_tanggal' => 'required|date',
                'details' => 'required|array|min:1',
                'details.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
                'details.*.harga' => 'required|integer|min:1',
                'details.*.jumlah' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                $penjualan = PenjualanModel::find($id);
                if (!$penjualan) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }

                DB::beginTransaction();

                // Ambil detail penjualan lama untuk membatalkan stok keluar
                $oldDetails = PenjualanDetailModel::where('penjualan_id', $id)->get();
                foreach ($oldDetails as $detail) {
                    // Tambahkan entri stok untuk membatalkan stok keluar sebelumnya
                    StokModel::create([
                        'barang_id' => $detail->barang_id,
                        'user_id' => $penjualan->user_id,
                        'supplier_id' => null,
                        'stock_tanggal' => now(),
                        'stok_jumlah' => $detail->jumlah, // Kembalikan stok
                    ]);
                }

                // Validasi stok untuk detail baru
                foreach ($request->details as $detail) {
                    $totalStock = StokModel::where('barang_id', $detail['barang_id'])
                        ->sum('stok_jumlah');

                    if ($totalStock < $detail['jumlah']) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => "Stok barang dengan ID {$detail['barang_id']} tidak mencukupi. Stok tersedia: {$totalStock}, jumlah yang diminta: {$detail['jumlah']}"
                        ], 400);
                    }
                }

                $totalHarga = 0;
                foreach ($request->details as $detail) {
                    $totalHarga += $detail['harga'] * $detail['jumlah'];
                }

                $penjualan->update([
                    'user_id' => $request->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_kode' => $request->penjualan_kode,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                    'total_harga' => $totalHarga,
                ]);

                PenjualanDetailModel::where('penjualan_id', $id)->delete();

                foreach ($request->details as $detail) {
                    PenjualanDetailModel::create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $detail['barang_id'],
                        'harga' => $detail['harga'],
                        'jumlah' => $detail['jumlah'],
                    ]);

                    StokModel::create([
                        'barang_id' => $detail['barang_id'],
                        'user_id' => $request->user_id,
                        'supplier_id' => null,
                        'stock_tanggal' => now(),
                        'stok_jumlah' => -$detail['jumlah'],
                    ]);
                }

                DB::commit();
                Log::info('Penjualan diperbarui (update_ajax): penjualan_id=' . $penjualan->penjualan_id);

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in update_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $penjualan = PenjualanModel::find($id);
                if (!$penjualan) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }

                DB::beginTransaction();

                // Batalkan stok keluar
                $details = PenjualanDetailModel::where('penjualan_id', $id)->get();
                foreach ($details as $detail) {
                    StokModel::create([
                        'barang_id' => $detail->barang_id,
                        'user_id' => $penjualan->user_id,
                        'supplier_id' => null,
                        'stock_tanggal' => now(),
                        'stok_jumlah' => $detail->jumlah, // Kembalikan stok
                    ]);
                }

                PenjualanDetailModel::where('penjualan_id', $id)->delete();
                $penjualan->delete();

                DB::commit();
                Log::info('Penjualan dihapus (delete_ajax): penjualan_id=' . $id);

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil dihapus'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                Log::error('Error in delete_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in delete_ajax: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }
}