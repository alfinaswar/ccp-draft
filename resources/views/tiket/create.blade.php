@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Buat Tiket</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ticket.index') }}">Daftar Tiket</a></li>
                    <li class="breadcrumb-item active">Buat Tiket</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Buat Tiket</h4>
                    <p class="card-text mb-0">
                        Silakan isi detail tiket baru di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('ticket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="Nama" class="form-label"><strong>Nama</strong></label>
                                    <input type="text" name="Nama"
                                        class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                        placeholder="Masukkan nama Anda" value="{{ old('Nama') }}">
                                    @error('Nama')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="NoHp" class="form-label"><strong>No HP</strong>
                                        <span class="text-muted">(Opsional, akan digunakan jika kami perlu follow up)</span>
                                    </label>
                                    <input type="text" name="NoHp"
                                        class="form-control @error('NoHp') is-invalid @enderror" id="NoHp"
                                        placeholder="Masukkan nomor HP" value="{{ old('NoHp') }}">
                                    @error('NoHp')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="Prioritas" class="form-label"><strong>Prioritas</strong></label>
                                <select name="Prioritas" id="Prioritas" class="form-control @error('Prioritas') is-invalid @enderror">
                                    <option value="">-- Pilih Prioritas --</option>
                                    <option value="Rendah" {{ old('Prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                    <option value="Sedang" {{ old('Prioritas') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Tinggi" {{ old('Prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="Darurat" {{ old('Prioritas') == 'Darurat' ? 'selected' : '' }}>Darurat</option>
                                </select>
                                @error('Prioritas')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="Judul" class="form-label"><strong>Judul Tiket</strong></label>
                                <input type="text" name="Judul"
                                    class="form-control @error('Judul') is-invalid @enderror" id="Judul"
                                    placeholder="Masukkan judul tiket" value="{{ old('Judul') }}">
                                @error('Judul')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="Deskripsi" class="form-label"><strong>Deskripsi</strong></label>
                                <textarea name="Deskripsi" class="ckeditor @error('Deskripsi') is-invalid @enderror" id="Deskripsi" placeholder="Masukkan deskripsi tiket" rows="4">{{ old('Deskripsi') }}</textarea>
                                @error('Deskripsi')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="FilePendukung" class="form-label"><strong>File Pendukung</strong> <span class="text-muted">(Opsional)</span></label>
                                <input type="file" name="FilePendukung" class="form-control @error('FilePendukung') is-invalid @enderror" id="FilePendukung" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                @error('FilePendukung')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">Maximum file size 5MB. Format: jpg, jpeg, png, pdf, doc, docx.</small>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('ticket.index') }}" class="btn btn-secondary me-2">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan Tiket
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
