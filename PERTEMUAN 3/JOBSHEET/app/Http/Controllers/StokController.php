<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];

        $page = (object) [
            'title' => 'Daftar stok barang dalam sistem'
        ];

        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $stoks = StokModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stock_tanggal', 'stok_jumlah')
            ->with(['supplier', 'barang', 'user']);

        if ($request->supplier_id) {
            $stoks->where('supplier_id', $request->supplier_id);
        }

        if ($request->barang_id) {
            $stoks->where('barang_id', $request->barang_id);
        }

        if ($request->user_id) {
            $stoks->where('user_id', $request->user_id);
        }

        return DataTables::of($stoks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stok) {
                $btn = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit Ajax</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Stok',
            'list' => ['Home', 'Stok', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah stok barang baru'
        ];

        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();

        return view('stok.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
            'barang_id' => 'required|integer|exists:m_barang,barang_id',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        try {
            $stok = StokModel::create([
                'supplier_id' => $request->supplier_id,
                'barang_id' => $request->barang_id,
                'user_id' => Auth::user()->user_id,
                'stock_tanggal' => now(),
                'stok_jumlah' => $request->stok_jumlah,
            ]);

            Log::info('Stok ditambahkan (store): barang_id=' . $request->barang_id . ', stok_jumlah=' . $request->stok_jumlah);

            return redirect('/stok')->with('success', 'Data stok berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan stok (store): ' . $e->getMessage());
            return redirect('/stok')->with('error', 'Gagal menyimpan stok: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $stok = StokModel::find($id);
        if (!$stok) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Stok',
            'list' => ['Home', 'Stok', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit stok barang'
        ];

        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();

        return view('stok.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'stok' => $stok,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
            'barang_id' => 'required|integer|exists:m_barang,barang_id',
            'stok_jumlah' => 'required|integer|min:1',
        ]);

        try {
            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update([
                    'supplier_id' => $request->supplier_id,
                    'barang_id' => $request->barang_id,
                    'stock_tanggal' => now(),
                    'stok_jumlah' => $request->stok_jumlah,
                ]);

                Log::info('Stok diperbarui (update): barang_id=' . $request->barang_id . ', stok_jumlah=' . $request->stok_jumlah);

                return redirect('/stok')->with('success', 'Data stok berhasil diupdate');
            }
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui stok (update): ' . $e->getMessage());
            return redirect('/stok')->with('error', 'Gagal memperbarui stok: ' . $e->getMessage());
        }
    }

    public function create_ajax()
    {
        try {
            $suppliers = SupplierModel::all();
            $barangs = BarangModel::all();

            if ($suppliers->isEmpty() || $barangs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data supplier atau barang tidak ditemukan. Silakan tambahkan data terlebih dahulu.'
                ], 404);
            }

            return view('stok.create_ajax', [
                'suppliers' => $suppliers,
                'barangs' => $barangs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat form: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'stok_jumlah' => 'required|integer|min:1',
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
                $stok = StokModel::create([
                    'supplier_id' => $request->supplier_id,
                    'barang_id' => $request->barang_id,
                    'user_id' => Auth::user()->user_id,
                    'stock_tanggal' => now(),
                    'stok_jumlah' => $request->stok_jumlah,
                ]);

                Log::info('Stok ditambahkan (store_ajax): barang_id=' . $request->barang_id . ', stok_jumlah=' . $request->stok_jumlah);

                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal menyimpan stok (store_ajax): ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        if (!$stok) {
            return response()->json([
                'status' => false,
                'message' => 'Data stok tidak ditemukan'
            ]);
        }

        $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        return view('stok.edit_ajax', [
            'stok' => $stok,
            'suppliers' => $suppliers,
            'barangs' => $barangs
        ]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'stok_jumlah' => 'required|integer|min:1',
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
                $stok = StokModel::find($id);
                if ($stok) {
                    $stok->update([
                        'supplier_id' => $request->supplier_id,
                        'barang_id' => $request->barang_id,
                        'stock_tanggal' => now(),
                        'stok_jumlah' => $request->stok_jumlah,
                    ]);

                    Log::info('Stok diperbarui (update_ajax): barang_id=' . $request->barang_id . ', stok_jumlah=' . $request->stok_jumlah);

                    return response()->json([
                        'status' => true,
                        'message' => 'Data stok berhasil diupdate'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal memperbarui stok (update_ajax): ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal memperbarui stok: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $stok = StokModel::find($id);
                if ($stok) {
                    $barangId = $stok->barang_id;
                    $stokJumlah = $stok->stok_jumlah;

                    $stok->delete();

                    Log::info('Stok dihapus (delete_ajax): barang_id=' . $barangId . ', stok_jumlah=' . $stokJumlah);

                    return response()->json([
                        'status' => true,
                        'message' => 'Data stok berhasil dihapus'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Gagal menghapus stok (delete_ajax): ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait'
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal menghapus stok (delete_ajax): ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menghapus stok: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/');
    }

    public function destroy($id)
    {
        try {
            $stok = StokModel::find($id);
            if ($stok) {
                $barangId = $stok->barang_id;
                $stokJumlah = $stok->stok_jumlah;

                $stok->delete();

                Log::info('Stok dihapus (destroy): barang_id=' . $barangId . ', stok_jumlah=' . $stokJumlah);

                return redirect('/stok')->with('success', 'Data stok berhasil dihapus');
            }
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Gagal menghapus stok (destroy): ' . $e->getMessage());
            return redirect('/stok')->with('error', 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus stok (destroy): ' . $e->getMessage());
            return redirect('/stok')->with('error', 'Gagal menghapus stok: ' . $e->getMessage());
        }
    }
    public function export_pdf()
    {
        // Ambil data stok untuk ekspor
        $stok = StokModel::selectRaw('barang_id, SUM(stok_jumlah) as total_stok')
            ->groupBy('barang_id')
            ->havingRaw('SUM(stok_jumlah) >= 0')
            ->with('barang')
            ->get();

        // Gunakan library Barryvdh\DomPDF\Facade\Pdf
        $pdf = FacadePdf::loadView('laporan_stok', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true untuk mengizinkan gambar dari URL
        $pdf->render();

        // Stream PDF ke browser dengan nama file dinamis
        return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function export_excel()
    {
        // Pastikan tidak ada output sebelum header dikirim
        if (ob_get_length()) {
            ob_clean();
        }

        // Periksa apakah ekstensi ZipArchive tersedia
        if (!class_exists('ZipArchive')) {
            \Log::error('ZipArchive not found during export in StokController');
            return redirect()->back()->with('error', 'Gagal mengekspor data: Ekstensi ZipArchive tidak ditemukan. Silakan aktifkan ekstensi zip di PHP.');
        }

        try {
            // Ambil data stok untuk ekspor
            $stok = StokModel::selectRaw('barang_id, SUM(stok_jumlah) as total_stok')
                ->groupBy('barang_id')
                ->havingRaw('SUM(stok_jumlah) >= 0')
                ->with('barang')
                ->get();

            // Periksa apakah ada data stok
            if ($stok->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data stok untuk diekspor.');
            }

            // Buat instance spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Buat header tabel
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode Barang');
            $sheet->setCellValue('C1', 'Nama Barang');
            $sheet->setCellValue('D1', 'Total Stok');

            // Bold header
            $sheet->getStyle('A1:D1')->getFont()->setBold(true);

            // Tambahkan border pada header
            $sheet->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Isi data stok
            $no = 1;
            $baris = 2;
            foreach ($stok as $item) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $item->barang->barang_kode ?? 'N/A');
                $sheet->setCellValue('C' . $baris, $item->barang->barang_nama ?? 'N/A');
                $sheet->setCellValue('D' . $baris, $item->total_stok);

                $no++;
                $baris++;
            }

            // Set lebar kolom secara otomatis
            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Beri nama sheet
            $sheet->setTitle('Data Stok');

            // Buat writer untuk format Xlsx
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Buat nama file dengan format "Data Stok YYYY-MM-DD.xlsx"
            $filename = 'Data Stok ' . date('Y-m-d') . '.xlsx';

            // Set header untuk download file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Pragma: no-cache');

            // Simpan file ke output (download)
            $writer->save('php://output');

            // Bersihkan spreadsheet dari memori
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            // Hentikan eksekusi
            exit;
        } catch (\Exception $e) {
            \Log::error('Export failed in StokController', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}