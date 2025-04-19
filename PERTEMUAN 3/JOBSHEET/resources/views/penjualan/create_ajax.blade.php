<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>User</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">- Pilih User -</option>
                        @foreach($users as $user)
                        <option value="{{ $user->user_id }}">{{ $user->nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" id="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Kode Penjualan</label>
                    <input type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" required>
                    <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                    <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Detail Barang</label>
                    <table class="table table-bordered" id="detailTable">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addDetailRow()">Tambah Barang</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script>
    var barangs = @json($barangs);

    function addDetailRow() {
        var row = `
            <tr>
                <td>
                    <select class="form-control" name="details[][barang_id]" required>
                        <option value="">- Pilih Barang -</option>
                        ${barangs.map(b => `<option value="${b.barang_id}">${b.barang_nama}</option>`).join('')}
                    </select>
                </td>
                <td><input type="number" class="form-control" name="details[][harga]" required></td>
                <td><input type="number" class="form-control" name="details[][jumlah]" required></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeDetailRow(this)">Hapus</button></td>
            </tr>`;
        $('#detailTable tbody').append(row);
    }

    function removeDetailRow(btn) {
        $(btn).closest('tr').remove();
    }

    $(document).ready(function() {
        addDetailRow();
        $("#form-tambah").validate({
            rules: {
                user_id: { required: true, number: true },
                pembeli: { required: true, maxlength: 50 },
                penjualan_kode: { required: true, maxlength: 20 },
                penjualan_tanggal: { required: true },
                "details[][barang_id]": { required: true, number: true },
                "details[][harga]": { required: true, number: true, min: 1 },
                "details[][jumlah]": { required: true, number: true, min: 1 }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataPenjualan.ajax.reload();
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