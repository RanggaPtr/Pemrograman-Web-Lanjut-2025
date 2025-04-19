@extends('layouts.template')

@section('title', 'Edit Penjualan')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Penjualan</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('penjualan.update', $penjualan->penjualan_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="pembeli">Nama Pembeli</label>
                            <input type="text" name="pembeli" id="pembeli" class="form-control" value="{{ old('pembeli', $penjualan->pembeli) }}" required>
                        </div>

                        <div id="barang-container">
                            @foreach ($penjualan->details as $index => $detail)
                                <div class="barang-item mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="barang_id">Pilih Barang</label>
                                            <select name="barang[{{$index}}][barang_id]" class="form-control barang-select" required>
                                                <option value="">Pilih Barang</option>
                                                @foreach ($barang as $item)
                                                    <option value="{{ $item->barang_id }}" data-stok="{{ $item->stokTotal ? $item->stokTotal->stok_total : 0 }}" {{ $item->barang_id == $detail->barang_id ? 'selected' : '' }}>
                                                        {{ $item->nama_barang }} (Stok: {{ $item->stokTotal ? $item->stokTotal->stok_total : 0 }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="jumlah">Jumlah</label>
                                            <input type="number" name="barang[{{$index}}][jumlah]" class="form-control jumlah-input" value="{{ $detail->jumlah }}" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-remove-barang mt-4" {{ $index == 0 ? 'style="display:none;"' : '' }}>Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-success mb-3" id="add-barang">Tambah Barang</button>
                        <br>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@section('scripts')
<script>
    let barangIndex = {{ count($penjualan->details) }};

    $('#add-barang').click(function() {
        let newItem = `
            <div class="barang-item mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <label for="barang_id">Pilih Barang</label>
                        <select name="barang[${barangIndex}][barang_id]" class="form-control barang-select" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->barang_id }}" data-stok="{{ $item->stokTotal ? $item->stokTotal->stok_total : 0 }}">
                                    {{ $item->nama_barang }} (Stok: {{ $item->stokTotal ? $item->stokTotal->stok_total : 0 }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" name="barang[${barangIndex}][jumlah]" class="form-control jumlah-input" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-remove-barang mt-4">Hapus</button>
                    </div>
                </div>
            </div>`;
        $('#barang-container').append(newItem);
        barangIndex++;
    });

    $(document).on('click', '.btn-remove-barang', function() {
        $(this).closest('.barang-item').remove();
    });

    $(document).on('change', '.barang-select', function() {
        let stok = $(this).find('option:selected').data('stok');
        let jumlahInput = $(this).closest('.barang-item').find('.jumlah-input');
        jumlahInput.attr('max', stok);
        jumlahInput.val('');
    });

    $(document).on('input', '.jumlah-input', function() {
        let stok = $(this).closest('.barang-item').find('.barang-select option:selected').data('stok');
        let jumlah = parseInt($(this).val());
        if (jumlah > stok) {
            alert('Jumlah melebihi stok yang tersedia!');
            $(this).val(stok);
        }
    });
</script>
@endsection
@endsection