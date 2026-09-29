@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('satuan.index') }}">Master Satuan</a></li>
                    <li class="active">Edit</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-balance-scale"></i>
                    Edit Satuan
                </h3>
                <p class="ph-subtitle">
                    Silakan ubah data satuan di bawah ini.
                </p>
            </div>
            <!-- KANAN: Icon -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Formulir Edit Satuan</h4>
                    <p class="card-text mb-0">
                        Silakan ubah data satuan di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('satuan.update', $satuan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">

                            <div class="col-md-12">
                                <label for="nama_satuan" class="form-label"><strong>Nama Satuan</strong></label>
                                <input type="text" name="NamaSatuan"
                                    class="form-control @error('NamaSatuan') is-invalid @enderror" id="nama_satuan"
                                    placeholder="Nama Satuan" value="{{ old('NamaSatuan', $satuan->NamaSatuan) }}">
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
                                    <i class="fa fa-save"></i> Update
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
