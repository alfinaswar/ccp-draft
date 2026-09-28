@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('departemen.index') }}">Master Departemen</a></li>
                    <li class="active">Tambah Departemen</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-sitemap"></i>
                    Master Departemen / Divisi
                </h3>
                <p class="ph-subtitle">
                    Tambahkan departemen atau divisi baru ke dalam sistem.
                </p>
            </div>
            <!-- KANAN: Icon -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-building"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formu Tambah Departemen / Divisi</h4>
                    <p class="card-text mb-0">
                        Silakan isi data departemen / divisi baru di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('departemen.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="Nama" class="form-label"><strong>Nama Departemen / Divisi</strong></label>
                                <input type="text" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                    placeholder="Nama Departemen / Divisi" value="{{ old('Nama') }}">
                                @error('Nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('departemen.index') }}" class="btn btn-secondary me-2">
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
