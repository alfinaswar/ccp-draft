@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('parameter.index') }}">Master Parameter</a></li>
                    <li class="active">Sesuaikan</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-sliders"></i>
                    Sesuaikan Parameter
                </h3>
                <p class="ph-subtitle">
                    Silakan sesuaikan data parameter di bawah ini.
                </p>
            </div>
            <!-- KANAN: Icon -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-sliders"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Formulir Edit Parameter</h4>
                    <p class="card-text mb-0">
                        Silakan sesuaikan data parameter di bawah ini.
                    </p>
                </div>

                <div class="card-body">
                    <form action="{{ route('parameter.update', $parameter->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="Nama" class="form-label"><strong>Nama</strong></label>
                                <input type="text" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                    placeholder="Nama Parameter" value="{{ old('Nama', $parameter->Nama) }}">
                                @error('Nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('parameter.index') }}" class="btn btn-secondary me-2">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
