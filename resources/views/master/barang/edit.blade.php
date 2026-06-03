@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Barang</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Master Barang</a></li>
                    <li class="breadcrumb-item active">Edit Barang</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Edit Barang</h4>
                    <p class="card-text mb-0">
                        Silakan ubah data barang di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('barang.update', $barang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Nama" class="form-label"><strong>Nama</strong></label>
                                    <input type="text" name="Nama"
                                        class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                        placeholder="Nama Barang" value="{{ old('Nama', $barang->Nama) }}">
                                    @error('Nama')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="Satuan" class="form-label"><strong>Satuan</strong></label>
                                    <select name="Satuan" id="Satuan"
                                        class="form-select select2 @error('Satuan') is-invalid @enderror">
                                        <option value="">-- Pilih Satuan --</option>
                                        @foreach ($satuan ?? [] as $s)
                                            <option value="{{ $s->id }}"
                                                {{ old('Satuan', $barang->Satuan) == $s->id ? 'selected' : '' }}>
                                                {{ $s->NamaSatuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Satuan')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="Tipe" class="form-label"><strong>Tipe</strong></label>
                                    <input type="text" name="Tipe" id="Tipe"
                                        class="form-control @error('Tipe') is-invalid @enderror"
                                        value="{{ old('Tipe', $barang->Tipe) }}" placeholder="Masukkan tipe barang">
                                    @error('Tipe')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Jenis" class="form-label"><strong>Jenis</strong></label>
                                    <select name="Jenis" id="Jenis"
                                        class="form-select @error('Jenis') is-invalid @enderror">
                                        <option value="">-- Pilih Jenis --</option>
                                        @foreach ($jenis ?? [] as $j)
                                            <option value="{{ $j->id }}"
                                                {{ old('Jenis', $barang->Jenis) == $j->id ? 'selected' : '' }}>
                                                {{ $j->Nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Jenis')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="Merek" class="form-label"><strong>Merek</strong></label>
                                    <select name="Merek" id="Merek"
                                        class="form-select select2 @error('Merek') is-invalid @enderror">
                                        <option value="">-- Pilih Merek --</option>
                                        @foreach ($merekList ?? [] as $merek)
                                            <option value="{{ $merek->id }}"
                                                {{ old('Merek', $barang->Merek) == $merek->id ? 'selected' : '' }}>
                                                {{ $merek->Nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Merek')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('barang.index') }}" class="btn btn-secondary me-2">
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
@push('js')
@endpush
