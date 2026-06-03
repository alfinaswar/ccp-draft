@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Satuan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('satuan.index') }}">Master Satuan</a></li>
                    <li class="breadcrumb-item active">Tambah Satuan</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Tambah Satuan</h4>
                    <p class="card-text mb-0">
                        Silakan isi data satuan baru di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('satuan.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-12">
                                <label for="nama_satuan" class="form-label"><strong>Nama Satuan</strong></label>
                                <input type="text" name="NamaSatuan"
                                    class="form-control @error('NamaSatuan') is-invalid @enderror" id="nama_satuan"
                                    placeholder="Nama Satuan" value="{{ old('NamaSatuan') }}">
                                @error('NamaSatuan')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('satuan.index') }}" class="btn btn-secondary me-2">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
