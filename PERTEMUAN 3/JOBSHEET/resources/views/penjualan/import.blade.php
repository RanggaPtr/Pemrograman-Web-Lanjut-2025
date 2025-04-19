@extends('layouts.template')

@section('title', 'Import Penjualan')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Import Penjualan</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form id="import-form">
                        <div class="form-group">
                            <label for="file">Pilih File Excel</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="importAjax()">Import</button>
                        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@section('scripts')
<script>
function importAjax() {
    let formData = new FormData($('#import-form')[0]);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: '{{ route('penjualan.import_ajax') }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                alert(response.message);
                window.location.href = '{{ route('penjualan.index') }}';
            }
        },
        error: function(xhr) {
            alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
        }
    });
}
</script>
@endsection
@endsection