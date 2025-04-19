@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
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
                        <select class="form-control" id="barang_id" name="barang_id">
                            <option value="">- Semua Barang -</option>
                            @foreach($barangs as $barang)
                            <option value="{{ $barang->barang_id }}">{{ $barang->barang_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-hover table-sm" id="table_stok_total">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Stok Total</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataStokTotal;
    $(document).ready(function() {
        dataStokTotal = $('#table_stok_total').DataTable({
            serverSide: true,
            ajax: {
                url: "{{ url('stok-total/list') }}",
                dataType: "json",
                type: "POST",
                data: function(d) {
                    d.barang_id = $('#barang_id').val();
                },
                error: function(xhr, error, thrown) {
                    console.log('Error:', xhr.responseText);
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "barang_nama", orderable: true, searchable: true },
                { data: "stok_jumlah", orderable: true, searchable: true },
            ]
        });

        $('#barang_id').on('change', function() {
            dataStokTotal.ajax.reload();
        });
    });
</script>
@endpush