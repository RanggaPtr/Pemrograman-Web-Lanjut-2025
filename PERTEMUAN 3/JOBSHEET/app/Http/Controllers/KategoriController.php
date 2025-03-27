<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    // Display the list of categories
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Kategori Barang',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object)[
            'title' => 'Daftar Kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori'; // Set active menu

        $kategori = KategoriModel::all(); // Fetch all categories

        return view('kategori.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    // Fetch the data for DataTables
    public function list(Request $request)
    {
        $kategoriQuery = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        // If 'kategori_id' is passed via the request, filter by kategori_id
        if ($request->kategori_id) {
            $kategoriQuery->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($kategoriQuery)
            ->addIndexColumn()  // Add index column
            ->addColumn('aksi', function ($kategori) {
                $btn  = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id) . '\')" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Show the form to create a new category
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Kategori Baru'
        ];

        $activeMenu = 'kategori'; // Set active menu

        return view('kategori.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',  // Ensure kategori_kode is unique
            'kategori_nama' => 'required|string|max:100', // Validate kategori_nama
        ]);

        KategoriModel::create([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama
        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
    }

    // Show the details of a category
    public function show(string $id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list' => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Kategori'
        ];

        $activeMenu = 'kategori'; // Set active menu

        return view('kategori.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }



    public function edit($id)
    {
        // Fetch the category by ID
        $kategori = KategoriModel::find($id);

        // If the category is not found, redirect with an error message
        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Kategori'
        ];

        $activeMenu = 'kategori'; // Set active menu

        return view('kategori.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,  // Pass the category data
            'activeMenu' => $activeMenu
        ]);
    }


    // Update the category data
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id', // Validate unique code except for the current category
            'kategori_nama' => 'required|string|max:100', // Validate category name
        ]);

        KategoriModel::find($id)->update([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama
        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
    }

    // Delete a category
    public function destroy(string $id)
    {
        $check = KategoriModel::find($id);
        if (!$check) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        try {
            KategoriModel::destroy($id); // Delete category
            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        if (request()->ajax()) {
            return view('kategori.create_ajax');
        }
        return redirect('/kategori');
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // 'kategori_id' => 'required|numeric',
                'kategori_kode' => 'required|string|min:4|unique:m_kategori,kategori_kode', // Unique constraint for level code
                'kategori_nama' => 'required|string|max:20', // Level name validation
            ];

            // use iluminate/support/Facades/Validator
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Pembuatan kategori Gagal',
                        'msgField' => $validator->errors()
                    ]
                );
            }

            KategoriModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $kategori = KategoriModel::find($id);
        return view('kategori.edit_ajax', ['kategori' => $kategori]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
                'kategori_nama' => 'required|string|max:20',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Edit gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $kategori = KategoriModel::find($id);
            if ($kategori) {
                $kategori->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Kategori updated successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Kategori not found',
            ]);
        }
        return redirect('/kategori');
    }


    public function confirm_ajax(string $id)
    {
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return view('kategori.error_ajax', ['message' => 'Data kategori tidak ditemukan']);
        }
        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    // public function delete_ajax menggunakan try catch
    public function delete_ajax(Request $request, $id)
    {
        try {
            $kategori = KategoriModel::find($id);
            $kategori->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak dapat dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
            ]);
        }
    }

    // Tambahan: Fitur impor dari kode program 2
    public function import()
    {
        return view('kategori.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_kategori' => ['required', 'mimes:xlsx', 'max:1024'] // Validasi file Excel, max 1MB
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
                $file = $request->file('file_kategori');
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
                            if (empty($value['A']) || empty($value['B'])) {
                                throw new \Exception("Data pada baris $baris tidak lengkap.");
                            }

                            // Validasi kategori_id
                            //  $kategori = KategoriModel::find($value['A']);
                            //  if (!$kategori) {
                            //      throw new \Exception("Kategori dengan ID {$value['A']} pada baris $baris tidak ditemukan.");
                            //  }

                            // Validasi kategori_kode unik
                            if (KategoriModel::where('kategori_kode', $value['A'])->exists()) {
                                throw new \Exception("Kode kategori {$value['A']} pada baris $baris sudah ada.");
                            }

                            $insert[] = [
                                'kategori_kode' => $value['A'],
                                'kategori_nama' => $value['B'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        $insertedCount = KategoriModel::insertOrIgnore($insert);
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
        return redirect('/kategori');
    }

    public function export_excel()
    {
        // Pastikan tidak ada output sebelum header dikirim
        if (ob_get_length()) {
            ob_clean();
        }

        // Periksa apakah ekstensi ZipArchive tersedia
        if (!class_exists('ZipArchive')) {
            \Log::error('ZipArchive not found during export in KategoriController');
            return redirect()->back()->with('error', 'Gagal mengekspor data: Ekstensi ZipArchive tidak ditemukan. Silakan aktifkan ekstensi zip di PHP.');
        }

        try {
            // Ambil data kategori yang akan diekspor
            $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')
                ->orderBy('kategori_id')
                ->get();

            // Periksa apakah ada data kategori
            if ($kategoris->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data kategori untuk diekspor.');
            }

            // Buat instance spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Buat header tabel
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode Kategori');
            $sheet->setCellValue('C1', 'Nama Kategori');

            // Bold header
            $sheet->getStyle('A1:C1')->getFont()->setBold(true);

            // Tambahkan border pada header
            $sheet->getStyle('A1:C1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Isi data kategori
            $no = 1;
            $baris = 2;
            foreach ($kategoris as $kategori) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $kategori->kategori_kode);
                $sheet->setCellValue('C' . $baris, $kategori->kategori_nama);

                $no++;
                $baris++;
            }

            // Set lebar kolom secara otomatis
            foreach (range('A', 'C') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Beri nama sheet
            $sheet->setTitle('Data Kategori');

            // Buat writer untuk format Xlsx
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Buat nama file dengan format "Data Kategori YYYY-MM-DD.xlsx"
            $filename = 'Data Kategori ' . date('Y-m-d') . '.xlsx';

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
            // Log error untuk debugging
            \Log::error('Export failed in KategoriController', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    public function export_pdf()
    {
        // Ambil data kategori yang akan diekspor
        $kategoris = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')
            ->orderBy('kategori_id')
            ->get();

        // Gunakan library Barryvdh\DomPDF\Facade\Pdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kategori.export_pdf', ['kategoris' => $kategoris]);
        $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true untuk mengizinkan gambar dari URL
        $pdf->render();

        // Stream PDF ke browser dengan nama file dinamis
        return $pdf->stream('Data Kategori ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
