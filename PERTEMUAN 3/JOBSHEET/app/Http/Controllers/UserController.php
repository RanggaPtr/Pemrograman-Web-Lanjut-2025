<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Monolog\Level;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        // menampilkan halaman aawal user
        $breadcrumb = (object)[
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user'; //set menu yang aktif

        $level = LevelModel::all(); //ambil data level untuk filter level

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    // public function list(Request $request)
    // {
    //     $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
    //         ->with('level');

    //     // filter data user bedasarkan level_id
    //     if ($request->level_id) {
    //         $users->where('level_id', $request->level_id);
    //     }

    //     return DataTables::of($users)
    //         // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($user) {  // menambahkan kolom aksi
    //             $btn  = '<a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a> ';
    //             $btn .= '<a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    //             $btn .= '<form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '">'
    //                 . csrf_field() . method_field('DELETE') .
    //                 '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
    //         ->make(true);
    // }        

    // Ambil data user dalam bentuk JSON untuk DataTables  
    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');

        // Filter data user berdasarkan level_id 
        if ($request->level_id) {
            $users->where('level_id', $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn()  // Menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)  
            // ->addColumn('aksi', function ($user) {
            //     // Menambahkan kolom aksi
            //     $btn  = '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/show') . '\')" class="btn btn-info btn-sm">Detail</button> ';
            //     $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
            //     $btn .= '<button onclick="modalAction(\'' . url('/user/' . $user->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';

            //     return $btn;
            // })
            ->addColumn('aksi', function ($user) {
                $btn  = '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/user/' . $user->user_id) . '\')" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/user/' . $user->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<a href="javascript:void(0)" onclick="modalAction(\'' . url('/user/' . $user->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</a>';
                return $btn;
            })
            ->rawColumns(['aksi']) // Menandakan bahwa kolom aksi mengandung HTML  
            ->make(true);
    }


    // Menampilkan halaman form tambah user
    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all(); // ambil data level untuk ditampilkan di form
        $activeMenu = 'user'; // set menu yang aktif

        return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan data user baru
    public function store(Request $request)
    {
        $request->validate([
            // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'password' => 'required|min:5', // password harus diisi dan minimal 5 karakter
            'level_id' => 'required|integer' // level_id harus diisi dan berupa angka
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password), // password dienkripsi sebelum disimpan
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }

    // Menampilkan detail user
    public function show(string $id)
    {
        $user = UserModel::with('level')->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail user'
        ];

        $activeMenu = 'user'; // set menu yang sedang aktif

        return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    // Menampilkan halaman form edit user
    public function edit(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit User',
            'list' => ['Home', 'User', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit user'
        ];

        $activeMenu = 'user'; // set menu yang sedang aktif

        return view('user.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'user' => $user,
            'level' => $level,
            'activeMenu' => $activeMenu
        ]);
    }

    // Menyimpan perubahan data user
    public function update(Request $request, string $id)
    {
        $request->validate([
            // username harus diisi, berupa string, minimal 3 karakter,
            // dan bernilai unik di tabel m_user kolom username kecuali untuk user dengan id yang sedang diedit
            'username' => 'required|string|min:3|unique:m_user,username,' . $id . ',user_id',
            'nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'password' => 'nullable|min:5', // password bisa diisi (minimal 5 karakter) dan bisa tidak diisi
            'level_id' => 'required|integer' // level_id harus diisi dan berupa angka
        ]);

        UserModel::find($id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    // Menghapus data user
    public function destroy(string $id)
    {
        $check = UserModel::find($id); // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
        if (!$check) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }

        try {
            UserModel::destroy($id); // Hapus data level
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    // public function create_ajax()
    // {
    //     $level = LevelModel::select('level_id', 'level_nama')->get();

    //     return view('user.create_ajax')
    //         ->with('level', $level);
    // }

    public function create_ajax()
    {
        // if (request()->ajax()) {
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.create_ajax', ['level' => $level]);
        // }
        // return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|max:20|unique:m_user',
                'nama' => 'required|string|min:3|max:100',
                'password' => 'required|string|min:6|max:20'
            ];

            // use iluminate/support/Facades/Validator
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response(
                    [
                        'status' => false,
                        'message' => 'Store Gagal',
                        'msgField' => $validator->errors()
                    ]
                );
            }

            UserModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();
        return view('user.edit_ajax', ['user' => $user, 'level' => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax 
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|max:20|unique:m_user,username,' . $id . ',user_id',
                'nama'     => 'required|max:100',
                'password' => 'nullable|min:6|max:20'
            ];

            // use Illuminate\Support\Facades\Validator; 
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,    // respon json, true: berhasil, false: gagal 
                    'message'  => 'Edit gagal.',
                    'msgField' => $validator->errors()  // menunjukkan field mana yang error 
                ]);
            }

            $check = UserModel::find($id);
            if ($check) {
                if (!$request->filled('password')) { // jika password tidak diisi, maka hapus dari request 
                    $request->request->remove('password');
                }

                $check->update($request->all());
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $user = UserModel::find($id);
        return view('user.confirm_ajax', ['user' => $user]);
    }

    // public function delete_ajax(Request $request, $id)
    // {
    //     if ($request->ajax() || $request->wantsJson()) {

    //         $user = UserModel::find($id);

    //         if ($user) {
    //             $user->delete();
    //             return response()->json([
    //                 'status'  => true,
    //                 'message' => 'Data berhasil dihapus'
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Data tidak ditemukan'
    //             ]);
    //         }
    //     }
    //     return redirect('/');
    // }

    // public function delete_ajax menggunakan try catch
    public function delete_ajax(Request $request, $id)
    {
        try {
            $user = UserModel::find($id);
            $user->delete();
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
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_user' => ['required', 'mimes:xlsx', 'max:1024'] // Validasi file Excel, max 1MB
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
                $file = $request->file('file_user');
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
                            if (empty($value['A']) || empty($value['B']) || empty($value['C']) || empty($value['D'])) {
                                throw new \Exception("Data pada baris $baris tidak lengkap.");
                            }

                            // Validasi level_id
                            $kategori = LevelModel::find($value['A']);
                            if (!$kategori) {
                                throw new \Exception("Level dengan ID {$value['A']} pada baris $baris tidak ditemukan.");
                            }

                            // Validasi username unik
                            if (UserModel::where('username', $value['B'])->exists()) {
                                throw new \Exception("Username {$value['B']} pada baris $baris sudah ada.");
                            }

                            // Validasi level_id harus angka
                            if (!is_numeric($value['A'])) {
                                throw new \Exception("Level ID pada baris $baris harus berupa angka.");
                            }

                            $insert[] = [
                                'level_id' => $value['A'],
                                'username' => $value['B'],
                                'nama' => $value['C'],
                                'password' => $value['D'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (count($insert) > 0) {
                        $insertedCount = UserModel::insertOrIgnore($insert);
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
        return redirect('/user');
    }

    public function export_excel()
    {
        // Pastikan tidak ada output sebelum header dikirim
        if (ob_get_length()) {
            ob_clean();
        }

        // Periksa apakah ekstensi ZipArchive tersedia
        if (!class_exists('ZipArchive')) {
            \Log::error('ZipArchive not found during export in UserController');
            return redirect()->back()->with('error', 'Gagal mengekspor data: Ekstensi ZipArchive tidak ditemukan. Silakan aktifkan ekstensi zip di PHP.');
        }

        try {
            // Ambil data user yang akan diekspor
            $users = UserModel::select('user_id', 'level_id', 'username', 'nama')
                ->with('level')
                ->orderBy('user_id')
                ->get();

            // Periksa apakah ada data user
            if ($users->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data user untuk diekspor.');
            }

            // Buat instance spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Buat header tabel
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Level');
            $sheet->setCellValue('C1', 'Username');
            $sheet->setCellValue('D1', 'Nama');

            // Bold header
            $sheet->getStyle('A1:D1')->getFont()->setBold(true);

            // Tambahkan border pada header
            $sheet->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Isi data user
            $no = 1;
            $baris = 2;
            foreach ($users as $user) {
                $sheet->setCellValue('A' . $baris, $no);
                $sheet->setCellValue('B' . $baris, $user->level->level_nama ?? 'N/A');
                $sheet->setCellValue('C' . $baris, $user->username);
                $sheet->setCellValue('D' . $baris, $user->nama);

                $no++;
                $baris++;
            }

            // Set lebar kolom secara otomatis
            foreach (range('A', 'D') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Beri nama sheet
            $sheet->setTitle('Data User');

            // Buat writer untuk format Xlsx
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

            // Buat nama file dengan format "Data User YYYY-MM-DD.xlsx"
            $filename = 'Data User ' . date('Y-m-d') . '.xlsx';

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
            \Log::error('Export failed in UserController', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    public function export_pdf()
    {
        // Ambil data user yang akan diekspor
        $users = UserModel::select('user_id', 'level_id', 'username', 'nama')
            ->with('level')
            ->orderBy('user_id')
            ->get();

        // Gunakan library Barryvdh\DomPDF\Facade\Pdf
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.export_pdf', ['users' => $users]);
        $pdf->setPaper('a4', 'portrait'); // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Set true untuk mengizinkan gambar dari URL
        $pdf->render();

        // Stream PDF ke browser dengan nama file dinamis
        return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }

    public function updateProfilePhoto(Request $request)
    {
        // Validasi file yang diunggah
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
        ]);

        try {
            $user = Auth::user();

            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Simpan foto baru
            $file = $request->file('foto');
            $filename = 'profile_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            // Update kolom foto di database
            $user->foto = $path;
            $user->save();

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Gagal mengunggah foto profil: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunggah foto profil: ' . $e->getMessage());
        }
    }
}
