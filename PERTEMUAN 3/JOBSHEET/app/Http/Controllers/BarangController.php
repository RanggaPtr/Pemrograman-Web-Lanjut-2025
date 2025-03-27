<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LevelModel; // Tambahan: Impor LevelModel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory; // Tambahan: Impor PhpOffice untuk fitur impor
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object)[
            'title' => 'Daftar Barang yang terdaftar dalam sistem'
        ];

        $activeMenu = 'barang';
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get(); // Perubahan: Hanya ambil kolom yang diperlukan

        return view('barang.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $barangQuery = BarangModel::with('kategori')
            ->select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id');

        if ($request->kategori_id) {
            $barangQuery->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($barangQuery)
            // Hapus addIndexColumn() karena tidak lagi menggunakan DT_RowIndex
            ->addColumn('aksi', function ($barang) {
                $btn  = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/barang/' . $barang->barang_id) . '\')" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/barang/' . $barang->barang_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Barang Baru'
        ];

        $kategori = KategoriModel::all();
        $activeMenu = 'barang';

        return view('barang.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_kode' => ['required', 'string', 'min:3', 'unique:m_barang,barang_kode'], // Perubahan: Format array
            'barang_nama' => ['required', 'string', 'max:100'],
            'harga_beli' => ['required', 'numeric'],
            'harga_jual' => ['required', 'numeric'],
            'kategori_id' => ['required', 'exists:m_kategori,kategori_id'],
        ]);

        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
    }

    public function show(string $id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Barang'
        ];

        $activeMenu = 'barang';

        return view('barang.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit(string $id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        $kategori = KategoriModel::all();
        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Barang'
        ];

        $activeMenu = 'barang';

        return view('barang.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_kode' => ['required', 'string', 'min:3', 'unique:m_barang,barang_kode,' . $id . ',barang_id'], // Perubahan: Format array
            'barang_nama' => ['required', 'string', 'max:100'],
            'harga_beli' => ['required', 'numeric'],
            'harga_jual' => ['required', 'numeric'],
            'kategori_id' => ['required', 'exists:m_kategori,kategori_id'],
        ]);

        BarangModel::find($id)->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    public function destroy(string $id)
    {
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            BarangModel::destroy($id);
            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::all();
        return view('barang.create_ajax', compact('kategori'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_id' => ['required', 'exists:m_kategori,kategori_id'], // Perubahan: Format array
                'barang_kode' => ['required', 'string', 'min:3', 'unique:m_barang,barang_kode'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli' => ['required', 'numeric'],
                'harga_jual' => ['required', 'numeric'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pembuatan Barang Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            BarangModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all();
        // $level = LevelModel::select('level_id', 'level_nama')->get(); // Tambahan: Ambil data level (dikomentari karena tidak jelas relevansinya)
        if (!$barang) {
            return response()->json(['status' => false, 'message' => 'Data barang tidak ditemukan'], 404);
        }
        return view('barang.edit_ajax', compact('barang', 'kategori'/*, 'level'*/));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_id' => ['required', 'exists:m_kategori,kategori_id'], // Perubahan: Format array
                'barang_kode' => ['required', 'string', 'min:3', 'unique:m_barang,barang_kode,' . $id . ',barang_id'],
                'barang_nama' => ['required', 'string', 'max:100'],
                'harga_beli' => ['required', 'numeric'],
                'harga_jual' => ['required', 'numeric'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Edit gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Barang updated successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Barang not found',
            ]);
        }
        return redirect('/barang');
    }

    public function confirm_ajax(string $id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return view('barang.error_ajax', ['message' => 'Data barang tidak ditemukan']);
        }
        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, $id)
    {
        try {
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Data barang tidak ditemukan'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
            ]);
        }
    }

    // Tambahan: Fitur impor dari kode program 2
    public function import()
    {
        return view('barang.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024'] // Validasi file Excel, max 1MB
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                $file = $request->file('file_barang');
                $reader = IOFactory::createReader('Xlsx'); // Perbaiki case
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $insert = [];
                if (count($data) > 1) {
                    foreach ($data as $baris => $value) {
                        if ($baris > 1) { // Lewati baris header
                            // Validasi data sebelum insert
                            if (empty($value['A']) || empty($value['B']) || empty($value['C']) || empty($value['D']) || empty($value['E'])) {
                                throw new \Exception("Data pada baris $baris tidak lengkap.");
                            }

                            // Validasi kategori_id
                            $kategori = KategoriModel::find($value['A']);
                            if (!$kategori) {
                                throw new \Exception("Kategori dengan ID {$value['A']} pada baris $baris tidak ditemukan.");
                            }

                            // Validasi barang_kode unik
                            if (BarangModel::where('barang_kode', $value['B'])->exists()) {
                                throw new \Exception("Kode barang {$value['B']} pada baris $baris sudah ada.");
                            }

                            // Validasi harga_beli dan harga_jual adalah numerik
                            if (!is_numeric($value['D']) || !is_numeric($value['E'])) {
                                throw new \Exception("Harga beli atau harga jual pada baris $baris harus berupa angka.");
                            }

                            $insert[] = [
                                'kategori_id' => $value['A'],
                                'barang_kode' => $value['B'],
                                'barang_nama' => $value['C'],
                                'harga_beli' => $value['D'],
                                'harga_jual' => $value['E'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        $insertedCount = BarangModel::insertOrIgnore($insert);
                        if ($insertedCount === 0) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Tidak ada data yang berhasil diimport. Pastikan data valid dan tidak duplikat.'
                            ]);
                        }

                        return response()->json([
                            'status' => true,
                            'message' => "Data berhasil diimport ($insertedCount baris)"
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada data yang diimport'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'File Excel kosong atau tidak memiliki data'
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengimpor data: ' . $e->getMessage()
                ], 500);
            }
        }
        return redirect('/barang');
    }

    public function export_excel()
    {
        // Pastikan tidak ada output sebelum header dikirim
        // Ini mencegah error "headers already sent"
        if (ob_get_length()) {
            ob_clean();
        }

        // Periksa apakah ekstensi ZipArchive tersedia
        // Xlsx writer membutuhkan ZipArchive untuk membuat file .xlsx
        if (!class_exists('ZipArchive')) {
            \Log::error('ZipArchive not found during export');
            return redirect()->back()->with('error', 'Gagal mengekspor data: Ekstensi ZipArchive tidak ditemukan. Silakan aktifkan ekstensi zip di PHP.');
        }

        try {
            // Ambil data barang yang akan diekspor
            // Select hanya kolom yang diperlukan dan urutkan berdasarkan kategori_id
            $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
                ->orderBy('kategori_id')
                ->with('kategori') // Eager load relasi kategori untuk mengambil kategori_nama
                ->get();

            // Periksa apakah ada data barang
            // Jika tidak ada data, kembalikan pesan error
            if ($barang->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data barang untuk diekspor.');
            }

            // Buat instance spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            // Ambil sheet aktif
            $sheet = $spreadsheet->getActiveSheet();

            // Buat header tabel
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kategori');
            $sheet->setCellValue('C1', 'Kode Barang');
            $sheet->setCellValue('D1', 'Nama Barang');
            $sheet->setCellValue('E1', 'Harga Beli');
            $sheet->setCellValue('F1', 'Harga Jual');

            // Bold header
            // Perbaikan: Gunakan range A1:F1 karena ada 6 kolom
            $sheet->getStyle('A1:F1')->getFont()->setBold(true);

            // Format header dengan border
            // Tambahkan border pada header untuk tampilan yang lebih rapi
            $sheet->getStyle('A1:F1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Format kolom Harga Beli dan Harga Jual sebagai mata uang
            // Gunakan format number dengan pemisah ribuan
            $sheet->getStyle('E2:E' . ($barang->count() + 1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('F2:F' . ($barang->count() + 1))->getNumberFormat()->setFormatCode('#,##0');

            // Isi data barang
            $no = 1;
            $baris = 2; // Mulai dari baris 2 karena baris 1 adalah header
            foreach ($barang as $value) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $value->kategori->kategori_nama ?? 'N/A'); // Gunakan 'N/A' jika kategori tidak ditemukan
                $sheet->setCellValue('C' . $baris, $value->barang_kode);
                $sheet->setCellValue('D' . $baris, $value->barang_nama);
                $sheet->setCellValue('E' . $baris, $value->harga_beli);
                $sheet->setCellValue('F' . $baris, $value->harga_jual);

                $no++;
                $baris++;
            }

            // Set lebar kolom secara otomatis
            // Gunakan range A hingga F karena ada 6 kolom
            foreach (range('A', 'F') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Beri nama sheet
            $sheet->setTitle('Data Barang');

            // Buat writer untuk format Xlsx
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Buat nama file dengan format "Data Barang YYYY-MM-DD.xlsx"
            $filename = 'Data Barang ' . date('Y-m-d') . '.xlsx';

            // Set header untuk download file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            // Konsolidasi header Cache-Control untuk mencegah caching
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
            // Log error untuk debugging
            \Log::error('Export failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}
