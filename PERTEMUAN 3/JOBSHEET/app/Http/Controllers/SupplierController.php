<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    // Display the list of suppliers
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];

        $page = (object)[
            'title' => 'Daftar Supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier'; // Set active menu

        // Retrieve all supplier records.
        // Note: The variable is called $level because your view expects it,
        // even though it represents suppliers.
        $supplier = SupplierModel::all();

        return view('supplier.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,  // Passing supplier data as $level (per your view)
            'activeMenu' => $activeMenu
        ]);
    }

    // Return supplier data in JSON format for DataTables
    public function list(Request $request)
    {
        // Select fields from the supplier table
        $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');

        // Example filter: if a supplier_id is provided, filter the records.
        if ($request->kategori_id) { // Note: This might be a filtering issue. If you intend to filter by supplier_id, use $request->supplier_id.
            $suppliers->where('supplier_id', $request->kategori_id);
        }

        return DataTables::of($suppliers)
            ->addIndexColumn() // Adds a sequential index column
            ->addColumn('aksi', function ($supplier) {
                $btn  = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id) . '\')" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Inform DataTables that the 'aksi' column contains HTML
            ->make(true);
    }

    // Show the form to create a new supplier
    public function create()
    {
        $breadcrumb = (object)[
            'title' => 'Tambah Supplier',
            'list' => ['Home', 'Supplier', 'Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah Supplier baru'
        ];

        // Fetch all supplier records (used in view as a dropdown if needed)
        $supplier = SupplierModel::all();
        $activeMenu = 'supplier';

        return view('supplier.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    // Store a new supplier
    public function store(Request $request)
    {
        // Validasi tanpa supplier_id
        $request->validate([
            'supplier_kode'   => 'required|string|max:6',
            'supplier_nama'   => 'required|string|max:50',
            'supplier_alamat' => 'required|string|max:100'
        ]);

        // Simpan data supplier tanpa mengirim supplier_id
        SupplierModel::create([
            'supplier_kode'   => $request->supplier_kode,
            'supplier_nama'   => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
    }


    // Show details of a single supplier
    public function show(string $id)
    {
        // Retrieve the supplier record by id.
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $breadcrumb = (object)[
            'title' => 'Detail Supplier',
            'list' => ['Home', 'Supplier', 'Detail']
        ];

        $page = (object)[
            'title' => 'Detail Supplier'
        ];

        $activeMenu = 'supplier';

        return view('supplier.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    // Show the form to edit an existing supplier
    public function edit(string $id)
    {
        // Find the supplier record by id.
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        // Retrieve all supplier records (if needed for a dropdown in the form)
        $supplier = SupplierModel::all();

        $breadcrumb = (object)[
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object)[
            'title' => 'Edit Supplier'
        ];

        $activeMenu = 'supplier';

        // Return the 'supplier.edit' view with the current supplier data.
        return view('supplier.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    // Update an existing supplier
    public function update(Request $request, string $id)
    {
        // Validasi tanpa supplier_id
        $request->validate([
            'supplier_kode'   => 'required|string|max:6|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
            'supplier_nama'   => 'required|string|max:50',
            'supplier_alamat' => 'required|string|max:100'
        ]);

        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        // Update data tanpa mengirim supplier_id
        $supplier->update([
            'supplier_kode'   => $request->supplier_kode,
            'supplier_nama'   => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil diubah');
    }



    // Delete a supplier
    public function destroy(string $id)
    {
        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            SupplierModel::destroy($id);
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        if (request()->ajax()) {
            return view('supplier.create_ajax');
        }
        return redirect('/supplier');
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_kode'   => 'required|string|min:3|unique:m_supplier,supplier_kode',
                'supplier_nama'   => 'required|string|max:50',
                'supplier_alamat' => 'required|string|max:100'
            ];

            // use iluminate/support/Facades/Validator
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Pembuatan supplier Gagal',
                        'msgField' => $validator->errors()
                    ]
                );
            }

            SupplierModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data supplier berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);
        return view('supplier.edit_ajax', ['supplier' => $supplier]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // 'supplier_kode'   => 'required|string|min:3|unique:m_supplier,supplier_kode',
                'supplier_nama'   => 'required|string|max:50',
                'supplier_alamat' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Edit gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'supplier updated successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'supplier not found',
            ]);
        }
        return redirect('/supplier');
    }

    public function confirm_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);
        if (!$supplier) {
            return view('supplier.error_ajax', ['message' => 'Data supplier tidak ditemukan']);
        }
        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }


    // public function delete_ajax menggunakan try catch
    public function delete_ajax(Request $request, $id)
    {
        try {
            $supplier = SupplierModel::find($id);
            $supplier->delete();
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
        return view('supplier.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_supplier' => ['required', 'mimes:xlsx', 'max:1024'] // Validasi file Excel, max 1MB
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
                $file = $request->file('file_supplier');
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
                            if (empty($value['A']) || empty($value['B']) || empty($value['C'])) {
                                throw new \Exception("Data pada baris $baris tidak lengkap.");
                            }

                            // Validasi kategori_id
                            // $kategori = KategoriModel::find($value['A']);
                            // if (!$kategori) {
                            //     throw new \Exception("Kategori dengan ID {$value['A']} pada baris $baris tidak ditemukan.");
                            // }

                            // Validasi supplier_kode unik
                            if (SupplierModel::where('supplier_kode', $value['A'])->exists()) {
                                throw new \Exception("Kode supplier {$value['B']} pada baris $baris sudah ada.");
                            }

                            $insert[] = [
                                'supplier_kode' => $value['A'],
                                'supplier_nama' => $value['B'],
                                'supplier_alamat' => $value['C'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        $insertedCount = SupplierModel::insertOrIgnore($insert);
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
        return redirect('/supplier');
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
            \Log::error('ZipArchive not found during export in SupplierController');
            return redirect()->back()->with('error', 'Gagal mengekspor data: Ekstensi ZipArchive tidak ditemukan. Silakan aktifkan ekstensi zip di PHP.');
        }

        try {
            // Ambil data supplier yang akan diekspor
            $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat')
                ->orderBy('supplier_id')
                ->get();

            // Periksa apakah ada data supplier
            if ($suppliers->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data supplier untuk diekspor.');
            }

            // Buat instance spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Buat header tabel
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kode Supplier');
            $sheet->setCellValue('C1', 'Nama Supplier');
            $sheet->setCellValue('D1', 'Alamat Supplier');

            // Bold header
            $sheet->getStyle('A1:D1')->getFont()->setBold(true);

            // Tambahkan border pada header
            $sheet->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Isi data supplier
            $no = 1;
            $baris = 2; // Mulai dari baris 2 karena baris 1 adalah header
            foreach ($suppliers as $supplier) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $supplier->supplier_kode);
                $sheet->setCellValue('C' . $baris, $supplier->supplier_nama);
                $sheet->setCellValue('D' . $baris, $supplier->supplier_alamat);

                $no++;
                $baris++;
            }

            // Set lebar kolom secara otomatis
            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Beri nama sheet
            $sheet->setTitle('Data Supplier');

            // Buat writer untuk format Xlsx
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Buat nama file dengan format "Data Supplier YYYY-MM-DD.xlsx"
            $filename = 'Data Supplier ' . date('Y-m-d') . '.xlsx';

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
            \Log::error('Export failed in SupplierController', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    public function export_pdf()
    {
        // Ambil data supplier yang akan diekspor
        $suppliers = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat')
            ->orderBy('supplier_id')
            ->get();

        // Gunakan library Barryvdh\DomPDF\Facade\Pdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('supplier.export_pdf', ['suppliers' => $suppliers]);
        $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true untuk mengizinkan gambar dari URL
        $pdf->render();

        // Stream PDF ke browser dengan nama file dinamis
        return $pdf->stream('Data Supplier ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
