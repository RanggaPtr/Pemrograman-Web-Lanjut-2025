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
                    <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->user_id }}">
                    <input type="text" class="form-control" value="{{ auth()->user()->nama }}" readonly>
                    <small id="error-user_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" id="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Kode Penjualan</label>
                    <input type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" value="{{ $penjualan_kode ?? 'PJ001' }}" readonly>
                    <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" readonly>
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
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addDetailRow()">Tambah Barang</button>
                </div>
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" id="total_harga" class="form-control" readonly>
                    <input type="hidden" name="total_harga" id="total_harga_hidden">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<style>
    .autocomplete-suggestions {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .autocomplete-suggestion {
        padding: 8px 12px;
        cursor: pointer;
    }

    .autocomplete-suggestion:hover {
        background-color: #f8f9fa;
    }
</style>
<script>
    var barangs = @json($barangs);

    function addDetailRow() {
        var row = `
            <tr>
                <td>
                    <div class="position-relative">
                        <input type="text" class="form-control barang-search" placeholder="Ketik untuk mencari barang..." required>
                        <input type="hidden" class="barang-id" name="details[][barang_id]">
                        <div class="autocomplete-suggestions d-none"></div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control harga" name="details[][harga]" readonly onfocus="this.blur()">
                    <input type="hidden" class="harga-hidden" name="details[][harga]">
                </td>
                <td><input type="number" class="form-control jumlah" name="details[][jumlah]" oninput="updateTotal(this)" required min="1"></td>
                <td><input type="text" class="form-control total" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeDetailRow(this)">Hapus</button></td>
            </tr>`;
        $('#detailTable tbody').append(row);

        // Tambahkan event listener untuk pencarian barang
        $('.barang-search').last().on('input', function() {
            var query = $(this).val().toLowerCase();
            var suggestionsContainer = $(this).siblings('.autocomplete-suggestions');
            suggestionsContainer.empty().addClass('d-none');

            if (query.length > 0) {
                var filteredBarangs = barangs.filter(b => b.barang_nama.toLowerCase().includes(query));
                if (filteredBarangs.length > 0) {
                    filteredBarangs.forEach(b => {
                        suggestionsContainer.append(
                            `<div class="autocomplete-suggestion" data-barang-id="${b.barang_id}" data-harga_jual="${b.harga_jual}" data-nama="${b.barang_nama}">
                                ${b.barang_nama}
                            </div>`
                        );
                    });
                    suggestionsContainer.removeClass('d-none');
                }
            }
        });

        // Event saat memilih saran
        $('.barang-search').last().siblings('.autocomplete-suggestions').on('click', '.autocomplete-suggestion', function() {
            var barangId = $(this).data('barang-id');
            var harga = parseFloat($(this).data('harga_jual')) || 0;
            var nama = $(this).data('nama');
            var row = $(this).closest('tr');

            row.find('.barang-search').val(nama);
            row.find('.barang-id').val(barangId);
            row.find('.harga').val(harga);
            row.find('.harga-hidden').val(harga); // Simpan nilai di input tersembunyi
            row.find('.autocomplete-suggestions').empty().addClass('d-none');

            updateTotal(row.find('.jumlah')[0]);
        });

        // Sembunyikan saran saat klik di luar
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.barang-search, .autocomplete-suggestions').length) {
                $('.autocomplete-suggestions').empty().addClass('d-none');
            }
        });
    }

    function updateTotal(input) {
        var row = $(input).closest('tr');
        var harga = parseFloat(row.find('.harga').val()) || 0;
        var jumlah = parseInt(row.find('.jumlah').val()) || 0;
        var total = harga * jumlah;
        row.find('.total').val(total.toLocaleString('id-ID'));

        // Update input tersembunyi untuk harga
        row.find('.harga-hidden').val(harga);

        // Hitung total keseluruhan
        var totalHarga = 0;
        $('#detailTable tbody tr').each(function() {
            var rowTotal = parseFloat($(this).find('.total').val().replace(/\./g, '')) || 0;
            totalHarga += rowTotal;
        });
        $('#total_harga').val('Rp ' + totalHarga.toLocaleString('id-ID'));
        $('#total_harga_hidden').val(totalHarga);
    }

    function removeDetailRow(btn) {
        $(btn).closest('tr').remove();
        // Perbarui total harga setelah hapus
        var firstJumlah = $('.jumlah')[0];
        if (firstJumlah) {
            updateTotal(firstJumlah);
        } else {
            $('#total_harga').val('Rp 0');
            $('#total_harga_hidden').val(0);
        }
    }

    $(document).ready(function() {
        // Set tanggal otomatis ke waktu saat ini
        var now = new Date();
        var formattedDate = now.toISOString().slice(0, 16);
        $('#penjualan_tanggal').val(formattedDate);

        // Tambah baris pertama secara otomatis
        addDetailRow();

        // Saat modal dibuka, tambahkan inert pada elemen di luar modal
        $('#modal-master').on('shown.bs.modal', function() {
            $('body > :not(#modal-master)').attr('inert', '');
        });

        // Saat modal ditutup, hapus inert
        $('#modal-master').on('hidden.bs.modal', function() {
            $('body > :not(#modal-master)').removeAttr('inert');
        });

        // Validasi form
        $("#form-tambah").validate({
            rules: {
                user_id: {
                    required: true,
                    number: true
                },
                pembeli: {
                    required: true,
                    maxlength: 50
                },
                penjualan_kode: {
                    required: true,
                    maxlength: 20
                },
                penjualan_tanggal: {
                    required: true
                },
                "details[][barang_id]": {
                    required: true,
                    number: true
                },
                "details[][harga]": {
                    required: true,
                    number: true,
                    min: 1
                },
                "details[][jumlah]": {
                    required: true,
                    number: true,
                    min: 1
                }
            },
            submitHandler: function(form) {
                // Cek apakah ada barang_id yang kosong
                var barangIdKosong = false;
                $('.barang-id').each(function() {
                    if (!$(this).val()) {
                        barangIdKosong = true;
                        $(this).closest('tr').find('.barang-search').addClass('is-invalid');
                    }
                });

                if (barangIdKosong) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Silakan pilih barang untuk semua baris.'
                    });
                    return false;
                }

                // Pastikan semua harga dan jumlah terupdate sebelum submit
                $('#detailTable tbody tr').each(function() {
                    var row = $(this);
                    var harga = parseFloat(row.find('.harga').val()) || 0;
                    var jumlah = parseInt(row.find('.jumlah').val()) || 0;
                    row.find('.harga-hidden').val(harga);
                    row.find('.jumlah').val(jumlah);
                });

                console.log('Data yang dikirim:', $(form).serializeArray());
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        console.log('Respons dari server:', response);
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
                                if (prefix.startsWith('details')) {
                                    var index = prefix.match(/details\.(\d+)\./)[1];
                                    var field = prefix.split('.').pop();
                                    var row = $('#detailTable tbody tr').eq(index);
                                    if (field === 'barang_id') {
                                        row.find('.barang-search').addClass('is-invalid');
                                        row.find('.barang-search').after(`<small class="error-text form-text text-danger">${val[0]}</small>`);
                                    } else if (field === 'harga') {
                                        row.find('.harga').addClass('is-invalid');
                                        row.find('.harga').after(`<small class="error-text form-text text-danger">${val[0]}</small>`);
                                    } else if (field === 'jumlah') {
                                        row.find('.jumlah').addClass('is-invalid');
                                        row.find('.jumlah').after(`<small class="error-text form-text text-danger">${val[0]}</small>`);
                                    }
                                } else {
                                    $('#error-' + prefix).text(val[0]);
                                }
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('Error AJAX:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menghubungi server. Silakan coba lagi.'
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