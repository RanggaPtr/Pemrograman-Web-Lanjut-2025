@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools"></div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('level') }}" class="form-horizontal">
            @csrf
            <div class="form-group row">
                <label class="col-1 control-label col-form-label">Level Kode</label>
                <div class="col-11">
                    <input type="text" class="form-control" id="level_kode" name="level_kode" value="{{ old('level_kode') }}" required readonly>
                    @error('level_kode')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-1 control-label col-form-label">Level Nama</label>
                <div class="col-11">
                    <input type="text" class="form-control" id="level_nama" name="level_nama" value="{{ old('level_nama') }}" required onkeyup="generateLevelCode()">
                    @error('level_nama')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-1 control-label col-form-label">Password</label>
                <div class="col-11">
                    <input type="password" class="form-control" id="password" name="password" required>
                    @error('password')
                    <small class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group row">
                <label class="col-1 control-label col-form-label"></label>
                <div class="col-11">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    <a class="btn btn-sm btn-default ml-1" href="{{ url('level') }}">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    // Function to generate level code automatically based on level name
    function generateLevelCode() {
        var levelName = document.getElementById('level_nama').value;
        var levelCode = levelName.substring(0, 3).toUpperCase(); // Get the first 3 letters and convert to uppercase
        document.getElementById('level_kode').value = levelCode;
    }
</script>
@endpush
