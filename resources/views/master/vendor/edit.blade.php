@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Vendor</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendor.index') }}">Master Vendor</a></li>
                    <li class="breadcrumb-item active">Edit Vendor</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Edit Vendor</h4>
                    <p class="card-text mb-0">
                        Silakan ubah data vendor di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.update', $vendor->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="jenis_vendor" class="form-label"><strong>Jenis</strong></label>
                                <select name="Jenis" class="form-control @error('Jenis') is-invalid @enderror"
                                    id="jenis_vendor">
                                    <option value="">Pilih Jenis Vendor</option>
                                    <option value="Medis" {{ old('Jenis', $vendor->Jenis) == 'Medis' ? 'selected' : '' }}>
                                        Medis</option>
                                    <option value="Umum" {{ old('Jenis', $vendor->Jenis) == 'Umum' ? 'selected' : '' }}>
                                        Umum</option>
                                </select>
                                @error('Jenis')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nama_vendor" class="form-label"><strong>Nama Vendor</strong></label>
                                <input type="text" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" id="nama_vendor"
                                    placeholder="Nama Vendor" value="{{ old('Nama', $vendor->Nama) }}">
                                @error('Nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="alamat_vendor" class="form-label"><strong>Alamat</strong></label>
                                <input type="text" name="Alamat"
                                    class="form-control @error('Alamat') is-invalid @enderror" id="alamat_vendor"
                                    placeholder="Alamat Vendor" value="{{ old('Alamat', $vendor->Alamat) }}">
                                @error('Alamat')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nohp_vendor" class="form-label"><strong>No. HP Vendor</strong></label>
                                <input type="text" name="NoHp"
                                    class="form-control @error('NoHp') is-invalid @enderror" id="nohp_vendor"
                                    placeholder="No. HP Vendor" value="{{ old('NoHp', $vendor->NoHp) }}">
                                @error('NoHp')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email_vendor" class="form-label"><strong>Email Vendor</strong></label>
                                <input type="email" name="Email"
                                    class="form-control @error('Email') is-invalid @enderror" id="email_vendor"
                                    placeholder="Email Vendor" value="{{ old('Email', $vendor->Email) }}">
                                @error('Email')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nama_pic" class="form-label"><strong>Nama PIC</strong></label>
                                <input type="text" name="NamaPic"
                                    class="form-control @error('NamaPic') is-invalid @enderror" id="nama_pic"
                                    placeholder="Nama PIC" value="{{ old('NamaPic', $vendor->NamaPic) }}">
                                @error('NamaPic')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nohp_pic" class="form-label"><strong>No. HP PIC</strong></label>
                                <input type="text" name="NoHpPic"
                                    class="form-control @error('NoHpPic') is-invalid @enderror" id="nohp_pic"
                                    placeholder="No. HP PIC" value="{{ old('NoHpPic', $vendor->NoHpPic) }}">
                                @error('NoHpPic')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status_vendor" class="form-label"><strong>Status</strong></label>
                                <select name="Status" id="status_vendor"
                                    class="form-select @error('Status') is-invalid @enderror">
                                    <option value="Y" {{ old('Status', $vendor->Status) == 'Y' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="N" {{ old('Status', $vendor->Status) == 'N' ? 'selected' : '' }}>
                                        Tidak Aktif</option>
                                </select>
                                @error('Status')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="npwp" class="form-label"><strong>NPWP <span class="text-muted"
                                                style="font-weight:normal;font-size:90%;">(Jika
                                                ada)</span></strong></label>
                                    <input type="text" name="Npwp"
                                        class="form-control @error('Npwp') is-invalid @enderror" id="npwp"
                                        placeholder="NPWP Vendor" value="{{ old('Npwp') }}">
                                    @error('Npwp')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nib" class="form-label"><strong>NIB <span class="text-muted"
                                                style="font-weight:normal;font-size:90%;">(Jika
                                                ada)</span></strong></label>
                                    <input type="text" name="Nib"
                                        class="form-control @error('Nib') is-invalid @enderror" id="nib"
                                        placeholder="NIB Vendor" value="{{ old('Nib') }}">
                                    @error('Nib')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('vendor.index') }}" class="btn btn-secondary me-2">
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
