@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Form</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('nama-form.index') }}">Master Form</a></li>
                    <li class="breadcrumb-item active">Tambah Form</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Tambah Form</h4>
                    <p class="card-text mb-0">
                        Silakan isi data form baru di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('nama-form.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="NamaForm" class="form-label"><strong>Nama Form</strong></label>
                                <input type="text" name="NamaForm"
                                    class="form-control @error('NamaForm') is-invalid @enderror" id="NamaForm"
                                    placeholder="Nama Form" value="{{ old('NamaForm') }}">
                                @error('NamaForm')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="Parameter" class="form-label"><strong>Pilih Parameter yang
                                        digunakan</strong></label>

                                <div class="mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkAllParam">
                                        <label class="form-check-label" for="checkAllParam"><strong>Pilih Semua
                                                Parameter</strong></label>
                                    </div>
                                </div>

                                <div class="border rounded p-3" style="max-height:300px; overflow-y:auto;"
                                    id="checkboxContainer">
                                    @foreach ($parameterList ?? [] as $parameter)
                                        <div class="form-check">
                                            <input class="form-check-input param-checkbox" type="checkbox"
                                                name="parameter_id[]" value="{{ $parameter->id }}"
                                                id="param_{{ $parameter->id }}"
                                                {{ is_array(old('parameter_id')) && in_array($parameter->id, old('parameter_id')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="param_{{ $parameter->id }}">
                                                {{ $parameter->Nama }}
                                            </label>
                                        </div>
                                    @endforeach

                                    @if ($errors->has('parameter_id'))
                                        <div class="text-danger mt-1">
                                            {{ $errors->first('parameter_id') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('nama-form.index') }}" class="btn btn-secondary me-2">
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

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkAll = document.getElementById('checkAllParam');
            const checkboxes = document.querySelectorAll('.param-checkbox');

            checkAll?.addEventListener('change', function() {
                const checked = this.checked;
                checkboxes.forEach(cb => cb.checked = checked);
            });

            // Jika semua sudah dicek, centang "Pilih Semua Parameter"
            // Jika ada yang tidak dicek, hilangkan centang "Pilih Semua Parameter"
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (Array.from(checkboxes).every(c => c.checked)) {
                        checkAll.checked = true;
                    } else {
                        checkAll.checked = false;
                    }
                });
            });

            // Inisialisasi ke "centang semua" jika seluruh checkbox sudah dicek saat reload (ex: saat ada validasi gagal)
            if (checkboxes.length > 0 && Array.from(checkboxes).every(c => c.checked)) {
                checkAll.checked = true;
            }
        });
    </script>
@endpush
