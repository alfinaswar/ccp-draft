@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Pengaturan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Pengaturan</a></li>
                    <li class="breadcrumb-item active">Pengajuan</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Hari Pengajuan</h4>
                    {{-- <p class="card-text mb-0">

                    </p> --}}
                </div>
                <div class="card-body">
                    <div class="row social-authent-settings">
                        <div class="col-xxl-12 col-xl-6 col-lg-12 col-md-6 d-flex">
                            <div class="connected-app-card d-flex w-100">
                                <ul class="w-100">
                                    <li class="flex-column align-items-start">
                                        <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                            <div class="security-type d-flex align-items-center">
                                                <span>
                                                    <strong>Tanggal Tutup</strong>
                                                </span>
                                            </div>
                                            <div class="connect-btn">
                                                <div class="form-check form-switch" style="transform: scale(1.5);">
                                                    <input class="form-check-input" type="checkbox" id="isAktifToggle"
                                                        name="isAktif" value="Y" style="width: 2.5em; height: 1.2em;"
                                                        {{ isset($tutup) && $tutup->isAktif == 'Y' ? 'checked' : '' }}>
                                                </div>

                                            </div>
                                        </div>
                                        <form action="{{ route('pengaturan.store-tanggal') }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label for="Nama" class="form-label"><strong>Nama</strong></label>
                                                    <input type="text" name="Nama" id="Nama"
                                                        class="form-control @error('Nama') is-invalid @enderror"
                                                        value="{{ old('Nama', $tutup->Nama ?? null) }}"
                                                        placeholder="Nama Tutup Pengajuan">
                                                    @error('Nama')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="TanggalMulai" class="form-label"><strong>Tanggal
                                                            Mulai</strong></label>
                                                    <input type="datetime-local" name="TanggalMulai" id="TanggalMulai"
                                                        class="form-control @error('TanggalMulai') is-invalid @enderror"
                                                        value="{{ old('TanggalMulai', isset($tutup->TanggalMulai) ? \Carbon\Carbon::parse($tutup->TanggalMulai)->format('Y-m-d\TH:i') : null) }}">
                                                    @error('TanggalMulai')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="TanggalSelesai" class="form-label"><strong>Tanggal
                                                            Selesai</strong></label>
                                                    <input type="datetime-local" name="TanggalSelesai" id="TanggalSelesai"
                                                        class="form-control @error('TanggalSelesai') is-invalid @enderror"
                                                        value="{{ old('TanggalSelesai', isset($tutup->TanggalSelesai) ? \Carbon\Carbon::parse($tutup->TanggalSelesai)->format('Y-m-d\TH:i') : null) }}">
                                                    @error('TanggalSelesai')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label for="Keterangan"
                                                        class="form-label"><strong>Keterangan</strong></label>
                                                    <textarea name="Keterangan" id="Keterangan" class="form-control @error('Keterangan') is-invalid @enderror"
                                                        placeholder="Isi keterangan...">{{ old('Keterangan', $tutup->Keterangan ?? null) }}</textarea>
                                                    @error('Keterangan')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12 mt-3 text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-6 d-flex">
                            <div class="connected-app-card d-flex w-100">
                                <ul class="w-100">
                                    <li class="flex-column align-items-start">
                                        <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                            <div class="security-type d-flex align-items-center">
                                                <span>
                                                    <strong>Hari Pengajuan</strong>
                                                </span>
                                            </div>
                                        </div>
                                        <form action="{{ route('pengaturan.store-hari') }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                @foreach ($hariBuka as $idx => $item)
                                                    <div class="col-12 border rounded p-3 mb-3">
                                                        <div class="row g-3 align-items-center" style="min-height: 70px;">
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="w-100">
                                                                    <label
                                                                        class="form-label mb-1"><strong>{{ $item->NamaHari }}</strong></label>
                                                                    <input type="hidden"
                                                                        name="hari[{{ $idx }}][NamaHari]"
                                                                        value="{{ $item->NamaHari }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="w-100">
                                                                    <label for="JamMulai_{{ $item->NamaHari }}"
                                                                        class="form-label mb-1">
                                                                        <strong>Jam Buka </strong>
                                                                    </label>
                                                                    <input type="time"
                                                                        name="hari[{{ $idx }}][JamMulai]"
                                                                        id="JamMulai_{{ $item->NamaHari }}"
                                                                        class="form-control"
                                                                        value="{{ old("hari.$idx.JamMulai", $item->JamMulai) }}">
                                                                    @error("hari.$idx.JamMulai")
                                                                        <div class="text-danger mt-1">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="w-100">
                                                                    <label for="JamSelesai_{{ $item->NamaHari }}"
                                                                        class="form-label mb-1">
                                                                        <strong>Jam Selesai </strong>
                                                                    </label>
                                                                    <input type="time"
                                                                        name="hari[{{ $idx }}][JamSelesai]"
                                                                        id="JamSelesai_{{ $item->NamaHari }}"
                                                                        class="form-control"
                                                                        value="{{ old("hari.$idx.JamSelesai", $item->JamSelesai) }}">
                                                                    @error("hari.$idx.JamSelesai")
                                                                        <div class="text-danger mt-1">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="d-flex align-items-center w-100">
                                                                    <div class="form-check form-switch me-2"
                                                                        style="transform: scale(1.25);">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="isAktif_{{ $item->NamaHari }}"
                                                                            name="hari[{{ $idx }}][isAktif]"
                                                                            value="Y"
                                                                            {{ old("hari.$idx.isAktif", $item->isAktif) == 'Y' ? 'checked' : '' }}>
                                                                    </div>
                                                                    <label for="isAktif_{{ $item->NamaHari }}"
                                                                        class="form-label mb-0">
                                                                        <strong>Aktif</strong>
                                                                    </label>
                                                                </div>
                                                                @error("hari.$idx.isAktif")
                                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="col-12 mt-3 text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xxl-6 col-xl-6 col-lg-12 col-md-6 d-flex">
                            <div class="connected-app-card d-flex w-100">
                                <ul class="w-100">
                                    <li class="flex-column align-items-start">
                                        <div class="d-flex align-items-center justify-content-between w-100 mb-2">
                                            <div class="security-type d-flex align-items-center">
                                                <span>
                                                    <strong>Jadwal Pengajuan Siap Presentasi</strong>
                                                </span>
                                            </div>
                                        </div>
                                        <form action="{{ route('pengaturan-presentasi.store-hari-presentasi') }}"
                                            method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                @foreach ($hariPresentasi ?? [] as $idx => $item)
                                                    <div class="col-12 border rounded p-3 mb-3">
                                                        <div class="row g-3 align-items-center" style="min-height: 70px;">
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="w-100">
                                                                    <label class="form-label mb-1">
                                                                        <strong>{{ $item->NamaHari }}</strong>
                                                                    </label>
                                                                    <input type="hidden"
                                                                        name="hari[{{ $idx }}][NamaHari]"
                                                                        value="{{ $item->NamaHari }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="w-100">
                                                                    <label for="JamMulai_{{ $item->NamaHari }}"
                                                                        class="form-label mb-1">
                                                                        <strong>Jam Mulai</strong>
                                                                    </label>
                                                                    <input type="time"
                                                                        name="hari[{{ $idx }}][JamMulai]"
                                                                        id="JamMulai_{{ $item->NamaHari }}"
                                                                        class="form-control"
                                                                        value="{{ old("hari.$idx.JamMulai", $item->JamMulai) }}">
                                                                    @error("hari.$idx.JamMulai")
                                                                        <div class="text-danger mt-1">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="w-100">
                                                                    <label for="JamSelesai_{{ $item->NamaHari }}"
                                                                        class="form-label mb-1">
                                                                        <strong>Jam Selesai</strong>
                                                                    </label>
                                                                    <input type="time"
                                                                        name="hari[{{ $idx }}][JamSelesai]"
                                                                        id="JamSelesai_{{ $item->NamaHari }}"
                                                                        class="form-control"
                                                                        value="{{ old("hari.$idx.JamSelesai", $item->JamSelesai) }}">
                                                                    @error("hari.$idx.JamSelesai")
                                                                        <div class="text-danger mt-1">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-center"
                                                                style="height:100%;">
                                                                <div class="d-flex align-items-center w-100">
                                                                    <div class="form-check form-switch me-2"
                                                                        style="transform: scale(1.25);">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="isAktif_{{ $item->NamaHari }}"
                                                                            name="hari[{{ $idx }}][isAktif]"
                                                                            value="Y"
                                                                            {{ old("hari.$idx.isAktif", $item->isAktif) == 'Y' ? 'checked' : '' }}>
                                                                    </div>
                                                                    <label for="isAktif_{{ $item->NamaHari }}"
                                                                        class="form-label mb-0">
                                                                        <strong>Aktif</strong>
                                                                    </label>
                                                                </div>
                                                                @error("hari.$idx.isAktif")
                                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="col-12 mt-3 text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endsection
    @push('js')
        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: "{{ session('success') }}",
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif
        <script>
            $(document).ready(function() {
                $('#isAktifToggle').change(function() {
                    var isAktif = $(this).is(':checked') ? 'Y' : 'N';
                    $.ajax({
                        url: "{{ isset($tutup) ? route('pengaturan.update', $tutup->id) : '' }}",
                        type: 'PUT',
                        data: {
                            isAktif: isAktif,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Status aktif Berhasil diperbarui.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal memperbarui status aktif.'
                            });
                            // Revert toggle jika gagal
                            $('#isAktifToggle').prop('checked', !$(this).is(':checked'));
                        }
                    });
                });
            });
        </script>
        <script>
            document.querySelectorAll('input[type="time"]').forEach(el => {
                el.addEventListener('change', function() {
                    let val = this.value;
                    if (val) {
                        let [h, m] = val.split(':');
                        this.value = `${h.padStart(2,'0')}:${m}`;
                    }
                });
            });
            flatpickr("input[type=time]", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        </script>
    @endpush
