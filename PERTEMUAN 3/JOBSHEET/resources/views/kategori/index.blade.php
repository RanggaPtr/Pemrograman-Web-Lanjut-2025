@extends('layouts.template')

@section('content')
<a href="{{ url('/kategori/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Data Kategori</a> 
<button onclick="modalAction('{{ url('/kategori/import') }}')" class="btn btn-sm btn-info mt-1">Import Data Kategori</button>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('kategori/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>

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
                        <select class="form-control" id="kategori_id" name="kategori_id" required>
                            <option value="">- Semua -</option>
                            @foreach($kategori as $item) <!-- Corrected $level to $kategori -->
                            <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori">
            <thead>
                <tr>
                    <th>Kategori ID</th>
                    <th>Kategori Kode</th>
                    <th>Kategori Nama</th>
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

    var dataKategori;
    $(document).ready(function() {
        dataKategori = $('#table_kategori').DataTable({
            serverSide: true,
            ajax: {
                url: "{{ url('kategori/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.kategori_id = $('#kategori_id').val(); // Corrected filter variable
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "kategori_kode",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "kategori_nama",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Reload DataTable when filter is changed
        $('#kategori_id').on('change', function() {
            dataKategori.ajax.reload();
        });
    });
</script>
@endpush