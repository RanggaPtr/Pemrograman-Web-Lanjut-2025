@extends('layouts.template')

@section('content')

<a href="{{ url('/barang/export_excel') }}" class="btn btn-success"><i class="fa fa-file-excel"></i> Export Data Barang</a>
<a href="{{ url('/barang/export_pdf') }}" class="btn btn-danger "><i class="fa fa-file-pdf"></i> Export Data Barang</a>
<button onclick="modalAction('{{ url('/barang/import') }}')" class="btn btn-sm btn-info mt-1">Import Data Barang</button>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('barang/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('/barang/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
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
                        <select class="form-control" id="kategori_id" name="kategori_id">
                            <option value="">- Semua -</option>
                            @foreach($kategori as $item)
                            <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_barang">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Barang Kode</th>
                    <th>Barang Nama</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
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
        $('#myModal').load(url, function(response, status, xhr) {
            if (status === "success") {
                $('#myModal').modal('show');
            } else {
                console.log('Error Response:', response);
                console.log('Status:', status);
                console.log('XHR:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Modal',
                    text: 'Terjadi kesalahan saat memuat data.'
                });
                $('.modal-backdrop').remove();
            }
            console.log(url);
        });
    }

    $(document).ready(function() {
        var dataBarang = $('#table_barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('barang/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.kategori_id = $('#kategori_id').val();
                }
            },
            columns: [{
                    data: "barang_id", // Perubahan: Ganti DT_RowIndex dengan barang_id
                    className: "text-center",
                    orderable: true, // Ubah menjadi true agar bisa diurutkan berdasarkan barang_id
                    searchable: false
                },
                {
                    data: "kategori.kategori_nama",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "barang_kode",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "barang_nama",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "harga_beli",
                    className: "",
                    orderable: true,
                    searchable: true,
                    render: function(data) {
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                },
                {
                    data: "harga_jual",
                    className: "",
                    orderable: true,
                    searchable: true,
                    render: function(data) {
                        return new Intl.NumberFormat('id-ID').format(data);
                    }
                },
                {
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Event untuk filter kategori
        $('#kategori_id').on('change', function() {
            dataBarang.ajax.reload();
        });

        // Event pencarian dengan Enter
        $('#table_barang_filter input').unbind().on('keyup', function(e) {
            if (e.keyCode === 13) {
                dataBarang.search(this.value).draw();
            }
        });
    });
</script>
@endpush