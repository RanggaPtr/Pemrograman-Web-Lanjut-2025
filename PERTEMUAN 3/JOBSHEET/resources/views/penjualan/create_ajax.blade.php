<!-- resources/views/penjualan/create_ajax.blade.php -->

<!-- Meta CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<form action="{{ url('/penjualan/store_ajax') }}" method="POST" id="form-tambah">
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
                    <input type="text" name="pembeli" id="pembeli" class="form-control" required value="Budi Santoso">
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
                    <input type="text" id="total_harga" class="form-control" readonly value="Rp 0">
                    <input type="hidden" name="total_harga" id="total_harga_hidden" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary" disabled>Simpan</button>
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
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .error-text {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }
</style>

<script>
    var barangs = @json($barangs);

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
                }
            },
            messages: {
                user_id: {
                    required: "User ID harus diisi.",
                    number: "User ID harus berupa angka."
                },
                pembeli: {
                    required: "Nama pembeli harus diisi.",
                    maxlength: "Nama pembeli maksimal 50 karakter."
                },
                penjualan_kode: {
                    required: "Kode penjualan harus diisi.",
                    maxlength: "Kode penjualan maksimal 20 karakter."
                },
                penjualan_tanggal: {
                    required: "Tanggal penjualan harus diisi."
                }
            },
            submitHandler: function(form) {
                $('.error-text').text('');
                $('.is-invalid').removeClass('is-invalid');

                // Validasi baris di tabel detail
                let validDetails = [];
                let hasInvalidRow = false;

                $('#detailTable tbody tr').each(function(index) {
                    var row = $(this);
                    var barangId = row.find('.barang-id').val();
                    var harga = parseFloat(row.find('.harga').val()) || 0;
                    var jumlah = parseInt(row.find('.jumlah').val()) || 0;

                    // Jika baris tidak valid, tandai sebagai invalid
                    if (!barangId || harga <= 0 || jumlah <= 0) {
                        hasInvalidRow = true;
                        if (!barangId) {
                            row.find('.barang-search').addClass('is-invalid');
                            row.find('.barang-search').siblings('.error-text').text('Pilih barang terlebih dahulu.');
                        }
                        if (harga <= 0) {
                            row.find('.harga').addClass('is-invalid');
                            row.find('.harga').siblings('.error-text').text('Harga harus lebih dari 0.');
                        }
                        if (jumlah <= 0) {
                            row.find('.jumlah').addClass('is-invalid');
                            row.find('.jumlah').siblings('.error-text').text('Jumlah harus lebih dari 0.');
                        }
                    } else {
                        validDetails.push({
                            barang_id: barangId,
                            harga: harga,
                            jumlah: jumlah
                        });
                    }
                });

                // Jika tidak ada baris valid, tampilkan pesan error
                if (validDetails.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: 'Harap tambahkan setidaknya satu barang yang valid sebelum menyimpan.'
                    });
                    return;
                }

                // Jika ada baris yang tidak valid, jangan lanjutkan
                if (hasInvalidRow) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: 'Harap lengkapi semua data barang dengan benar.'
                    });
                    return;
                }

                // Prepare form data
                var formData = new FormData(form);
                formData.delete('details');

                // Tambahkan data details ke formData
                validDetails.forEach((detail, index) => {
                    formData.append(`details[${index}][barang_id]`, detail.barang_id);
                    formData.append(`details[${index}][harga]`, detail.harga);
                    formData.append(`details[${index}][jumlah]`, detail.jumlah);
                });

                // Send AJAX request
                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response) {
                        console.log('Respons dari server:', response);
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            if (typeof dataPenjualan !== 'undefined') {
                                dataPenjualan.ajax.reload();
                            }
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                if (prefix.startsWith('details')) {
                                    var index = prefix.match(/details\.(\d+)\./)[1];
                                    var field = prefix.split('.').pop();
                                    var row = $('#detailTable tbody tr').eq(index);
                                    if (field === 'barang_id') {
                                        row.find('.barang-search').addClass('is-invalid');
                                        row.find('.barang-search').siblings('.error-text').text(val[0]);
                                    } else if (field === 'harga') {
                                        row.find('.harga').addClass('is-invalid');
                                        row.find('.harga').siblings('.error-text').text(val[0]);
                                    } else if (field === 'jumlah') {
                                        row.find('.jumlah').addClass('is-invalid');
                                        row.find('.jumlah').siblings('.error-text').text(val[0]);
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

    function addDetailRow() {
        var row = `
            <tr>
                <td>
                    <div class="position-relative">
                        <input type="text" class="form-control barang-search" placeholder="Ketik untuk mencari barang..." required>
                        <input type="hidden" class="barang-id" name="details[][barang_id]">
                        <div class="autocomplete-suggestions d-none"></div>
                        <small class="error-text form-text text-danger"></small>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control harga" name="details[][harga]" readonly onfocus="this.blur()">
                    <input type="hidden" class="harga-hidden" name="details[][harga]">
                    <small class="error-text form-text text-danger"></small>
                </td>
                <td>
                    <input type="number" class="form-control jumlah" name="details[][jumlah]" oninput="updateTotal(this)" required min="1">
                    <small class="error-text form-text text-danger"></small>
                </td>
                <td>
                    <input type="text" class="form-control total" readonly value="0">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeDetailRow(this)">Hapus</button>
                </td>
            </tr>`;
        $('#detailTable tbody').append(row);
        checkFormValidity();

        // Tambahkan event listener untuk pencarian barang
        $('.barang-search').last().on('input', function() {
            var query = $(this).val().toLowerCase();
            var suggestionsContainer = $(this).siblings('.autocomplete-suggestions');
            suggestionsContainer.empty().addClass('d-none');

            // Bersihkan barang-id dan harga jika pengguna mengetik tanpa memilih
            $(this).siblings('.barang-id').val('');
            $(this).closest('tr').find('.harga').val('');
            $(this).closest('tr').find('.harga-hidden').val('');
            updateTotal($(this).closest('tr').find('.jumlah')[0]);
            checkFormValidity();

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
            row.find('.harga-hidden').val(harga);
            row.find('.autocomplete-suggestions').empty().addClass('d-none');

            updateTotal(row.find('.jumlah')[0]);
            checkFormValidity();
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
        checkFormValidity();
    }

    // Fungsi untuk memeriksa validitas form dan mengaktifkan/menonaktifkan tombol Simpan
    function checkFormValidity() {
        let isValid = true;
        $('#detailTable tbody tr').each(function() {
            var barangId = $(this).find('.barang-id').val();
            var harga = parseFloat($(this).find('.harga').val()) || 0;
            var jumlah = parseInt($(this).find('.jumlah').val()) || 0;
            if (!barangId || harga <= 0 || jumlah <= 0) {
                isValid = false;
                return false; // Keluar dari loop
            }
        });
        // Aktifkan/nonaktifkan tombol Simpan
        $('button[type="submit"]').prop('disabled', !isValid || $('#detailTable tbody tr').length === 0);
    }

    // Panggil checkFormValidity setiap kali ada perubahan pada tabel
    $(document).on('input', '.barang-search, .harga, .jumlah', function() {
        checkFormValidity();
    });
</script>