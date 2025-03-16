@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <!-- Display the page title from the $page object -->
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools"></div>
    </div>
    <div class="card-body">
        @empty($supplier)
        <!-- If the supplier variable is empty, show an error alert -->
        <div class="alert alert-danger alert-dismissible">
            <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
            Data yang Anda cari tidak ditemukan.
        </div>
        @else
        <!-- Otherwise, display supplier details in a table -->
        <table class="table table-bordered table-striped table-hover table-sm">
            <tr>
                <th>Supplier ID</th>
                <td>{{ $supplier->supplier_id }}</td>
            </tr>
            <tr>
                <th>Supplier Kode</th>
                <td>{{ $supplier->supplier_kode }}</td>
            </tr>
            <tr>
                <th>Supplier Nama</th>
                <td>{{ $supplier->supplier_nama }}</td>
            </tr>
            <tr>
                <th>Supplier Alamat</th>
                <td>{{ $supplier->supplier_alamat }}</td>
            </tr>
        </table>
        @endempty
        <!-- Back button to return to the supplier index page -->
        <a href="{{ url('supplier') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
    </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
