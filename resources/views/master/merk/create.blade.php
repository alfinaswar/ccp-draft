@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('merk.index') }}">Master Merk</a></li>
                    <li class="active">Tambah</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-tags"></i>
                    Tambah Merk
                </h3>
                <p class="ph-subtitle">
                    Silakan isi data merk baru di bawah ini.
                </p>
            </div>
            <!-- KANAN: Icon -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-tags"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Tambah Merk</h4>
                    <p class="card-text mb-0">
                        Silakan isi data merk baru di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('merk.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="Nama" class="form-label"><strong>Nama</strong></label>
                                <input type="text" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                    placeholder="Nama Merk" value="{{ old('Nama') }}">
                                @error('Nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('merk.index') }}" class="btn btn-secondary me-2">
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
