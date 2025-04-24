@extends('layouts.template')

@section('content')
<a href="{{ url('/penjualan/export_excel') }}" class="btn btn-success"><i class="fa fa-file-excel"></i> Export Data Penjualan (Excel)</a>
<a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-danger"><i class="fa fa-file-pdf"></i> Export Data Penjualan (PDF)</a>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <!-- <a class="btn btn-sm btn-primary mt-1" href="{{ url('penjualan/create') }}">Tambah</a> -->
            <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
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
                        <select class="form-control" id="user_id" name="user_id">
                            <option value="">- Semua -</option>
                            @foreach($users as $user)
                            <option value="{{ $user->user_id }}">{{ $user->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Penjualan</th>
                    <th>Pembeli</th>
                    <th>Tanggal</th>
                    <th>User</th>
                    <th>Total Harga</th> <!-- Tambah kolom total harga -->
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

    var dataPenjualan;
    $(document).ready(function() {
        dataPenjualan = $('#table_penjualan').DataTable({
            serverSide: true,
            ajax: {
                url: "{{ url('penjualan/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.user_id = $('#user_id').val();
                },
                error: function(xhr, error, thrown) {
                    console.log('Error:', xhr.responseText);
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "penjualan_kode",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "pembeli",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "penjualan_tanggal",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "user.nama",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    data: "total_harga",
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        console.log('total_harga:', data); // Debug nilai
                        return data ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data) : 'Rp 0';
                    }
                },
                {
                    data: "aksi",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#user_id').on('change', function() {
            dataPenjualan.ajax.reload();
        });
    });
</script>
@endpush