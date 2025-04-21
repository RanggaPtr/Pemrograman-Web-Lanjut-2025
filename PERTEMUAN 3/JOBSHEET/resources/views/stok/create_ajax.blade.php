<form action="{{ route('stok.store_ajax') }}" method="POST" id="form-create">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                @if($suppliers->isEmpty())
                <div class="alert alert-warning">Data supplier tidak ditemukan. Silakan tambahkan supplier terlebih dahulu.</div>
                @endif
                @if($barangs->isEmpty())
                <div class="alert alert-warning">Data barang tidak ditemukan. Silakan tambahkan barang terlebih dahulu.</div>
                @endif
                @if(!$suppliers->isEmpty() && !$barangs->isEmpty())
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">- Pilih Supplier -</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-supplier_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Barang</label>
                    <select name="barang_id" id="barang_id" class="form-control" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach($barangs as $barang)
                        <option value="{{ $barang->barang_id }}">{{ $barang->barang_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-barang_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" required>
                    <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                @if(!$suppliers->isEmpty() && !$barangs->isEmpty())
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endif
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#form-create").validate({
            rules: {
                supplier_id: { required: true, number: true },
                barang_id: { required: true, number: true },
                stok_jumlah: { required: true, number: true, min: 1 }
            },
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
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
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
                            text: 'Gagal menyimpan data: ' + (xhr.responseJSON?.message || 'Silakan coba lagi.')
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