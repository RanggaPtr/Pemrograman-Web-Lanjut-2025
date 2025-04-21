@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Halo, apakabar!!!</h3>
        <div class="card-tools"></div>
    </div>
    <div class="card-body">
        Selamat datang semua, ini adalah halaman utama dari aplikasi ini.
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Stok Barang Real Time</h3>
    </div>
    <div class="card-body">
        <div class="row" id="stok_total_container">
            <!-- Card akan dimuat secara dinamis melalui JavaScript -->
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .stok-card {
        transition: transform 0.3s ease;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    .stok-card:hover {
        transform: translateY(-5px);
    }
    .stok-card .card-body {
        text-align: center;
    }
    .stok-card .card-title {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .stok-card .card-text {
        font-size: 1.5rem;
        color: #007bff;
    }
    .stok-card .card-icon {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Fungsi untuk memuat data stok dalam bentuk card
        function loadStokTotal() {
            $.ajax({
                url: "{{ url('dashboard/stok_total_list') }}",
                dataType: "json",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const data = response.data;
                    let html = '';
                    if (data.length === 0) {
                        html = '<div class="col-12 text-center"><p>Tidak ada data stok yang tersedia.</p></div>';
                    } else {
                        data.forEach(item => {
                            html += `
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="card stok-card">
                                        <div class="card-body">
                                            <i class="fas fa-box-open card-icon"></i>
                                            <h5 class="card-title">${item.barang_nama || 'N/A'}</h5>
                                            <p class="card-text">${item.total_stok}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#stok_total_container').html(html);
                },
                error: function(xhr, error, thrown) {
                    console.log('Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal memuat data: ' + thrown
                    });
                }
            });
        }

        // Panggil fungsi untuk memuat data saat halaman dimuat
        loadStokTotal();
    });
</script>
@endpush