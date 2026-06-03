@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Master Perusahaan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('perusahaan.index') }}">Master Perusahaan</a></li>
                    <li class="breadcrumb-item active">Edit Perusahaan</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Edit Perusahaan</h4>
                    <p class="card-text mb-0">
                        Silakan perbarui data perusahaan di bawah ini.
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('perusahaan.update', encrypt($perusahaan->id)) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="Kode" class="form-label"><strong>Kode</strong></label>
                                <input type="text" name="Kode"
                                    class="form-control @error('Kode') is-invalid @enderror" id="Kode"
                                    placeholder="Contoh : DIH" value="{{ old('Kode', $perusahaan->Kode) }}">
                                @error('Kode')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="Nama" class="form-label"><strong>Nama</strong></label>
                                <input type="text" name="Nama"
                                    class="form-control @error('Nama') is-invalid @enderror" id="Nama"
                                    placeholder="Nama" value="{{ old('Nama', $perusahaan->Nama) }}">
                                @error('Nama')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="NamaLengkap" class="form-label"><strong>Nama Lengkap</strong></label>
                                <input type="text" name="NamaLengkap"
                                    class="form-control @error('NamaLengkap') is-invalid @enderror" id="NamaLengkap"
                                    placeholder="Nama Lengkap" value="{{ old('NamaLengkap', $perusahaan->NamaLengkap) }}">
                                @error('NamaLengkap')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="Kategori" class="form-label"><strong>Kategori</strong></label>
                                <select name="Kategori" id="Kategori"
                                    class="form-control @error('Kategori') is-invalid @enderror">
                                    <option value="">Pilih Kategori</option>
                                    <option value="ABGROUP"
                                        {{ old('Kategori', $perusahaan->Kategori) == 'ABGROUP' ? 'selected' : '' }}>ABGROUP
                                    </option>
                                    <option value="CISCO"
                                        {{ old('Kategori', $perusahaan->Kategori) == 'CISCO' ? 'selected' : '' }}>CISCO
                                    </option>
                                </select>
                                @error('Kategori')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>


                            <div class="col-md-6">
                                <label for="NominalRkap" class="form-label"><strong>Nominal RKAP</strong></label>
                                <input type="text" name="NominalRkap"
                                    class="form-control rupiah-format @error('NominalRkap') is-invalid @enderror"
                                    id="NominalRkap" placeholder="Masukkan Nominal RKAP" value="{{ old('NominalRkap') }}">
                                @error('NominalRkap')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                {{-- Sisa RKAP bisa ditambah sesuai kebutuhan relevan --}}
                            </div>

                            <div class="col-12">
                                <label for="Deskripsi" class="form-label"><strong>Deskripsi</strong></label>
                                <textarea name="Deskripsi" class="form-control @error('Deskripsi') is-invalid @enderror" id="Deskripsi"
                                    placeholder="Deskripsi">{{ old('Deskripsi', $perusahaan->Deskripsi) }}</textarea>
                                @error('Deskripsi')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12 text-end mt-3">
                                <a href="{{ route('perusahaan.index') }}" class="btn btn-secondary me-2">
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
@push('js')
    <script>
        function formatRupiah(angka, prefix = 'Rp ') {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }

        $(document).ready(function() {
            $('.rupiah-format').on('input', function() {
                var value = $(this).val();
                // simpan kursor sebelum mengatur ulang value
                let caret = this.selectionStart;
                $(this).val(formatRupiah(value));
                // restore kursor ke akhir
                this.setSelectionRange($(this).val().length, $(this).val().length);
            });

            // Saat halaman selesai dimuat, formatkan jika sudah ada nilai (old value)
            $('.rupiah-format').each(function() {
                var value = $(this).val();
                if (value) {
                    $(this).val(formatRupiah(value));
                }
            });
        });
    </script>
@endpush
