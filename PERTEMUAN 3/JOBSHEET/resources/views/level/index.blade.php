@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('user/create') }}">Tambah</a>
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
                        <select class="form-control" id="level_kode" name="level_kode" required>
                            <option value="">- Semua -</option>
                            @foreach($level as $item)
                            <option value="{{ $item->level_kode }}">{{ $item->level_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
            <thead>
                <tr>
                    <th>Level ID</th>
                    <th>Level Kode</th>
                    <th>Level Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection


        @push('css')
        @endpush

        @push('js')
        <script>
            $(document).ready(function() {
                var dataLevel = $('#table_level').DataTable({
                    // serverSide: true, jika ingin menggunakan server side processing 
                    serverSide: true,
                    ajax: {
                        "url": "{{ url('level/list') }}",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d) {
                            d.level_kode = $('#level_kode').val();
                        }
                    },
                    columns: [{ // nomor urut dari laravel datatable addIndexColumn() 
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }, {
                        data: "level_kode",
                        className: "",
                        // orderable: true, jika ingin kolom ini bisa diurutkan  
                        orderable: true,
                        // searchable: true, jika ingin kolom ini bisa dicari 
                        searchable: true
                    }, {
                        data: "level_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    }, {
                        data: "aksi",
                        className: "",
                        orderable: false,
                        searchable: false
                    }]
                });

                $('#level_kode').on('change', function() {
                    dataLevel.ajax.reload();
                });

            });
        </script>
        @endpush
