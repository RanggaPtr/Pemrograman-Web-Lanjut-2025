@empty($stok)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ route('stok.delete_ajax', ['id' => $stok->stok_id]) }}" method="POST" id="form-delete">
    @csrf
    @method('DELETE')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Konfirmasi !!!</h5>
                    Apakah Anda ingin menghapus data seperti di bawah ini?
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">Supplier :</th>
                        <td class="col-9">{{ $stok->supplier->supplier_nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Barang :</th>
                        <td class="col-9">{{ $stok->barang->barang_nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">User :</th>
                        <td class="col-9">{{ $stok->user->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Tanggal :</th>
                        <td class="col-9">{{ $stok->stock_tanggal }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jumlah :</th>
                        <td class="col-9">{{ $stok->stok_jumlah }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#form-delete").validate({
            rules: {},
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            // Pastikan modal ditutup
                            if ($('#myModal').hasClass('show')) {
                                $('#myModal').modal('hide');
                            }

                            // Add a slight delay before removing backdrop elements
                            setTimeout(function() {
                                // Make sure backdrop is removed
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                                // Clear modal content
                                $('#myModal').html('');
                            }, 300);

                            // Then show success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                // Refresh table after SweetAlert is closed
                                dataStok.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menghapus data: ' + (xhr.responseJSON?.message || 'Silakan coba lagi.')
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endempty