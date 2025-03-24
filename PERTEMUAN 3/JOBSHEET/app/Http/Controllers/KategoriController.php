<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
}
