@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('stok/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter:</label>
                    <div class="col-3">
                        <select class="form-control" id="supplier_id" name="supplier_id">
                            <option value="">- Semua Supplier -</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <select class="form-control" id="barang_id" name="barang_id">
                            <option value="">- Semua Barang -</option>
                            @foreach($barangs as $barang)
                            <option value="{{ $barang->barang_id }}">{{ $barang->barang_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <select class="form-control" id="user_id" name="user_id">
                            <option value="">- Semua User -</option>
                            @foreach($users as $user)
                            <option value="{{ $user->user_id }}">{{ $user->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Supplier</th>
                    <th>Barang</th>
                    <th>User</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    var dataStok;
    $(document).ready(function() {
        dataStok = $('#table_stok').DataTable({
            serverSide: true,
            ajax: {
                url: "{{ url('stok/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.supplier_id = $('#supplier_id').val();
                    d.barang_id = $('#barang_id').val();
                    d.user_id = $('#user_id').val();
                },
                error: function(xhr, error, thrown) {
                    console.log('Error:', xhr.responseText);
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { 
                    data: "supplier.supplier_nama", 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                { 
                    data: "barang.barang_nama", 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                { 
                    data: "user.nama", 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                { data: "stock_tanggal", orderable: true, searchable: true },
                { data: "stok_jumlah", orderable: true, searchable: true },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });

        $('#supplier_id, #barang_id, #user_id').on('change', function() {
            dataStok.ajax.reload();
        });
    });
</script>
@endpush