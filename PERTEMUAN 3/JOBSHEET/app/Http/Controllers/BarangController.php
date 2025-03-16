<?php

namespace App\Http\Controllers;

// Import the necessary models and classes
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    // This method displays the list of all barang (products).
    public function index()
    {
        // Create a breadcrumb object containing title and navigation list
        $breadcrumb = (object)[
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        // Create a page object with a title used in the view header
        $page = (object)[
            'title' => 'Daftar Barang yang terdaftar dalam sistem'
        ];

        // Define the active menu to highlight the "barang" section in the UI
        $activeMenu = 'barang';

        // Retrieve all categories from the KategoriModel; this is used for filtering
        $kategori = KategoriModel::all();

        // Retrieve all barang (products) from the BarangModel
        $barang = BarangModel::all();

        // Return the 'barang.index' view and pass the breadcrumb, page, barang data, kategori data, and activeMenu variable.
        return view('barang.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,  // Note: this variable holds the list of products.
            'kategori' => $kategori,  // This variable holds the list of categories.
            'activeMenu' => $activeMenu
        ]);
    }

    // This method returns JSON data for DataTables.
    public function list(Request $request)
    {
        // Eager-load the 'kategori' relationship so that related category data is available in the JSON response.
        $barangQuery = BarangModel::with('kategori')
            ->select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id');

        // Optionally filter the query by barang_kode if provided in the request.
        if ($request->barang_kode) {
            $barangQuery->where('barang_kode', $request->barang_kode);
        }

        // Use DataTables to format the query results into a JSON response.
        return DataTables::of($barangQuery)
            ->addIndexColumn()  // Adds a sequential index column for row numbering.
            ->addColumn('aksi', function ($barang) {
                // Create HTML for action buttons: Detail, Edit, and Delete.
                $btn  = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/barang/' . $barang->barang_id) . '">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi'])  // Instruct DataTables that the 'aksi' column contains HTML.
            ->make(true);
    }

    // This method displays the form to create a new barang.
    public function create()
    {
        // Define breadcrumb for the create page.
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        // Define page title.
        $page = (object) [
            'title' => 'Tambah Barang Baru'
        ];

        // Retrieve all categories to populate a dropdown in the form.
        $kategori = KategoriModel::all();

        // Set the active menu.
        $activeMenu = 'barang';

        // Return the view 'barang.create' with the required data.
        return view('barang.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    // This method stores a new barang record in the database.
    public function store(Request $request)
    {
        // Validate the incoming request data.
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode',  // Must be unique in m_barang table.
            'barang_nama' => 'required|string|max:100', // Must be a string up to 100 characters.
            'harga_beli' => 'required|numeric', // Must be numeric.
            'harga_jual' => 'required|numeric', // Must be numeric.
            'kategori_id' => 'required|exists:m_kategori,kategori_id', // Must exist in m_kategori table.
        ]);

        // Create a new record in the m_barang table with the provided input.
        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        // Redirect to the barang index page with a success message.
        return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
    }

    // This method shows the details of a specific barang.
    public function show(string $id)
    {
        // Find the barang record by its id.
        $barang = BarangModel::find($id);

        // If not found, redirect with an error message.
        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        // Define breadcrumb and page title for the detail view.
        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Barang'
        ];

        $activeMenu = 'barang';

        // Return the view 'barang.show' with the barang record.
        return view('barang.show', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,
            'activeMenu' => $activeMenu
        ]);
    }

    // This method shows the form to edit an existing barang.
    public function edit(string $id)
    {
        // Find the barang record by its id.
        $barang = BarangModel::find($id);

        // If the record is not found, redirect back with an error message.
        if (!$barang) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        // Retrieve all categories to populate the dropdown in the edit form.
        $kategori = KategoriModel::all();

        // Define breadcrumb and page title for the edit view.
        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Barang'
        ];

        $activeMenu = 'barang';

        // Return the view 'barang.edit' with the current barang record and kategori list.
        return view('barang.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang, // Passing the found barang record as $barang.
            'kategori' => $kategori,
            'activeMenu' => $activeMenu
        ]);
    }

    // This method updates an existing barang record.
    public function update(Request $request, string $id)
    {
        // Validate the incoming request. The unique rule excludes the current record.
        $request->validate([
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,' . $id . ',barang_id',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
        ]);

        // Find the barang record by id and update it with the validated data.
        BarangModel::find($id)->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        // Redirect to the barang index page with a success message.
        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    // This method deletes a barang record.
    public function destroy(string $id)
    {
        // Find the barang record to delete.
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            // Attempt to delete the barang record.
            BarangModel::destroy($id);
            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // If deletion fails (e.g., due to foreign key constraints), redirect with an error.
            return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
