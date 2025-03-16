<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
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
        $level = SupplierModel::all();

        return view('supplier.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'level' => $level,  // Passing supplier data as $level (per your view)
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
                // Create HTML for Detail, Edit, and Delete actions.
                $btn  = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->supplier_id) . '">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
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
        $level = SupplierModel::all();
        $activeMenu = 'supplier';

        return view('supplier.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'level' => $level,
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
        $level = SupplierModel::all();

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
            'level' => $level,
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
}
