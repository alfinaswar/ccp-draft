@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('jenis-pengajuan.index') }}">Master Jenis Pengajuan</a></li>
                    <li class="active">Sesuaikan</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-list-alt"></i>
                    Sesuaikan Jenis Pengajuan
                </h3>
                <p class="ph-subtitle">
                    Silakan sesuaikan data jenis pengajuan di bawah ini.
                </p>
            </div>
            <!-- KANAN: Icon -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-list-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="card-title mb-0">Formulir Edit Jenis Pengajuan</h4>
                    <p class="card-text mb-0">
                        Silakan sesuaikan data jenis pengajuan di bawah ini.
                    </p>
                </div>

                <div class="card-body">
                    <form action="{{ route('jenis-pengajuan.update', $jenisPengajuan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="Nama" class="form-label"><strong>Nama Jenis Pengajuan</strong></label>
                                <input type="text" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                    placeholder="Nama Jenis Pengajuan" value="{{ old('Nama', $jenisPengajuan->Nama) }}">
                                @error('Nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="Form" class="form-label"><strong>Pilih Form yang Digunakan</strong></label>
                                <select class="form-select @error('Form') is-invalid @enderror" id="Form"
                                    name="Form">
                                    <option value="">-- Pilih Form --</option>
                                    @php
                                        $daftarForm = \App\Models\MasterForm::all();
                                    @endphp
                                    @foreach ($daftarForm as $form)
                                        <option value="{{ $form->id }}"
                                            {{ old('Form', $jenisPengajuan->Form) == $form->id ? 'selected' : '' }}>
                                            {{ $form->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Form')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('jenis-pengajuan.index') }}" class="btn btn-secondary me-2">
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
