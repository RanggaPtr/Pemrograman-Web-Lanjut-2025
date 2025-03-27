@extends('layouts.template')

@section('content')
<a href="{{ url('/supplier/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Data Supplier</a> 
<button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-sm btn-info mt-1">Import Data Supplier</button>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <!-- Button to add a new supplier -->
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('supplier/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('/supplier/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>

        </div>
    </div>
    <div class="card-body">
        <!-- Display success or error messages from the session -->
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter:</label>
                    <div class="col-3">
                        <!-- Renamed 'level_id' to 'supplier_id' for consistency with the DataTables code -->
                        <select class="form-control" id="supplier_id" name="supplier_id" required>
                            <option value="">- Semua -</option>
                            @foreach($supplier as $item)
                            <!-- 'level' now holds all supplier records (from your controller).
                                     Replace level_id/level_nama with the actual supplier fields. -->
                            <option value="{{ $item->supplier_id }}">{{ $item->supplier_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable for Supplier -->
        <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
            <thead>
                <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Kode</th>
                    <th>Supplier Nama</th>
                    <th>Supplier Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>

@endsection

@push('css')
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
        console.log(url);
        // return redirect('/level');
    }

    var dataSupplier;
    $(document).ready(function() {
        dataSupplier = $('#table_supplier').DataTable({
            // Enable server-side processing if your controller returns server-side data
            serverSide: true,
            ajax: {
                url: "{{ url('supplier/list') }}", // The route that returns JSON data
                dataType: "json",
                type: "POST",
                data: function(d) {
                    // Pass the selected supplier_id from the dropdown as a filter
                    d.supplier_id = $('#supplier_id').val();
                }
            },
            columns: [{
                    // Display the supplier_id field
                    data: "supplier_id",
                    className: "text-center",
                    orderable: true,
                    searchable: true
                },
                {
                    // Display the supplier_kode field
                    data: "supplier_kode",
                    orderable: true,
                    searchable: true
                },
                {
                    // Display the supplier_nama field
                    data: "supplier_nama",
                    orderable: true,
                    searchable: true
                },
                {
                    // Display the supplier_alamat field
                    data: "supplier_alamat",
                    orderable: true,
                    searchable: true
                },
                {
                    // Display action buttons (Detail, Edit, Delete) returned by your controller
                    data: "aksi",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Reload DataTable whenever the dropdown changes
        $('#supplier_id').on('change', function() {
            dataSupplier.ajax.reload();
        });
    });
</script>
@endpush