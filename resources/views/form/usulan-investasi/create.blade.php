@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Usulan Investasi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('usulan-investasi.index') }}">Usulan Investasi</a></li>
                    <li class="breadcrumb-item active">Buat Usulan Investasi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Usulan Investasi</h4>
                </div>
                <form action="{{ route('usulan-investasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $pengajuan->id }}" name="IdPengajuan">
                    <input type="hidden" value="{{ $pengajuan->getVendor[0]->NamaVendor }}" name="VendorDipilih">
                    <div class="card-body">
                        <!-- Baris atas: Departemen yang Meminta (kiri), Departemen Pembelian (kanan) -->
                        <div class="row mb-4">
                            <!-- Kolom Peminta -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-1">Departemen Peminta</h5>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Tanggal</label>
                                        <input type="date" class="form-control" name="TanggalMeminta"
                                            value="{{ old('TanggalMeminta') }}">
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Divisi</label>
                                        <select class="form-select select2" name="DepartemenMemintaId">
                                            <option value="">-- Pilih Divisi --</option>
                                            @foreach ($divisi as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ old('DepartemenMemintaId') == $d->id ? 'selected' : '' }}>
                                                    {{ $d->NamaDepartemen ?? ($d->nama ?? ($d->Nama ?? 'Divisi ' . $d->id)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Nama Kepala Divisi</label>
                                        <input type="text" class="form-control" name="KepalaDivisiMeminta"
                                            value="{{ old('KepalaDivisiMeminta') }}">
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <select name="KategoriUsulanMeminta" class="form-select">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Baru"
                                                {{ old('KategoriUsulanMeminta') == 'Baru' ? 'selected' : '' }}>Baru
                                            </option>
                                            <option value="Penggantian"
                                                {{ old('KategoriUsulanMeminta') == 'Penggantian' ? 'selected' : '' }}>
                                                Penggantian</option>
                                            <option value="Perbaikan"
                                                {{ old('KategoriUsulanMeminta') == 'Perbaikan' ? 'selected' : '' }}>
                                                Perbaikan
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Kolom Pembelian -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-1">Departemen Pembelian</h5>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Tanggal</label>
                                        <input type="date" class="form-control" name="TanggalPembelian"
                                            value="{{ old('TanggalPembelian') }}">
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Divisi</label>
                                        <select class="form-select select2" name="DepartemenPembelianId">
                                            <option value="">-- Pilih Divisi --</option>
                                            @foreach ($divisi as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ old('DepartemenPembelianId') == $d->id ? 'selected' : '' }}>
                                                    {{ $d->NamaDepartemen ?? ($d->nama ?? ($d->Nama ?? 'Divisi ' . $d->id)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Nama Kepala Divisi</label>
                                        <input type="text" class="form-control" name="KepalaDivisiPembelian"
                                            value="{{ old('KepalaDivisiPembelian') }}">
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <select name="KategoriUsulanPembelian" class="form-select">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Baru"
                                                {{ old('KategoriUsulanPembelian') == 'Baru' ? 'selected' : '' }}>Baru
                                            </option>
                                            <option value="Penggantian"
                                                {{ old('KategoriUsulanPembelian') == 'Penggantian' ? 'selected' : '' }}>
                                                Penggantian</option>
                                            <option value="Perbaikan"
                                                {{ old('KategoriUsulanPembelian') == 'Perbaikan' ? 'selected' : '' }}>
                                                Perbaikan
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- List Item dari Vendor Yang di-ACC -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">List Item dari Vendor yang di-ACC</label>
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th>Nama Barang</th>
                                            <th>Merek</th>
                                            <th>Vendor</th>
                                            <th>Harga Awal</th>
                                            <th>Harga Nego</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                            $hasItems = false;
                                        @endphp
                                        @if (isset($pengajuan) && $pengajuan->getVendor)
                                            @foreach ($pengajuan->getVendor as $vendor)
                                                @if ($vendor->AccVendor === 'Y' && $vendor->getVendorDetail)
                                                    @foreach ($vendor->getVendorDetail as $detail)
                                                        @php $hasItems = true; @endphp
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $detail->getNamaBarang->Nama ?? '-' }}</td>
                                                            <td>{{ $detail->getNamaBarang->getMerk->Nama ?? '-' }}</td>
                                                            <td>{{ $vendor->getNamaVendor->Nama ?? ($vendor->NamaVendor ?? '-') }}
                                                            </td>

                                                            <td>
                                                                @php
                                                                    $harga = isset($detail->HargaSatuan)
                                                                        ? $detail->HargaSatuan
                                                                        : 0;
                                                                @endphp
                                                                Rp {{ number_format($harga, 0, ',', '.') }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $harga = isset($detail->HargaSatuan)
                                                                        ? $detail->HargaSatuan
                                                                        : 0;
                                                                @endphp
                                                                Rp {{ number_format($harga, 0, ',', '.') }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $qty = isset($detail->Qty) ? $detail->Qty : 0;
                                                                    $subtotal = $qty * $harga;
                                                                @endphp
                                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (!$hasItems)
                                            <tr>
                                                <td colspan="8" class="text-center">Belum ada item.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="Keterangan" class="form-label fw-bold">Dengan ini kami ajukan permohonan untuk
                                pengadaan barang / jasa dengan alasan sebagai berikut :</label>
                            <textarea class="form-control" name="Keterangan" id="Keterangan" rows="3"
                                placeholder="Masukkan keterangan tambahan di sini...">{{ old('Keterangan', isset($pengajuan->Keterangan) ? $pengajuan->Keterangan : '') }}</textarea>
                        </div>
                        <div class="mb-4">
                            <div class="row">
                                <!-- Verifikasi RKAP Departemen (Kiri) -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h5 class="fw-bold mb-2">Verifikasi RKAP <span
                                                class="fw-normal">(Departemen)</span>
                                        </h5>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="SudahMasukRKAPDepartemen" id="rkapYaDepartemen"
                                                        value="Ya">
                                                    <label class="form-check-label" for="rkapYaDepartemen">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="SudahMasukRKAPDepartemen" id="rkapTidakDepartemen"
                                                        value="Tidak">
                                                    <label class="form-check-label"
                                                        for="rkapTidakDepartemen">Tidak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold" for="sisaBudgetRKAPDepartemen">Sisa Budget
                                                dari
                                                RKAP untuk tahun ini yang masih dapat dipergunakan:</label>
                                            <input type="text" class="form-control rupiah"
                                                id="sisaBudgetRKAPDepartemen" name="SisaBudgetRKAPDepartemen"
                                                placeholder="Masukkan sisa budget RKAP">
                                        </div>
                                    </div>
                                </div>
                                <!-- Verifikasi RKAP Keuangan (Kanan) -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h5 class="fw-bold mb-2">Verifikasi RKAP <span class="fw-normal">(Keuangan)</span>
                                        </h5>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="SudahMasukRKAPKeuangan" id="rkapYaKeuangan" value="Ya">
                                                    <label class="form-check-label" for="rkapYaKeuangan">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="SudahMasukRKAPKeuangan" id="rkapTidakKeuangan"
                                                        value="Tidak">
                                                    <label class="form-check-label" for="rkapTidakKeuangan">Tidak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold" for="sisaBudgetRKAPKeuangan">Sisa Budget
                                                dari
                                                RKAP untuk tahun ini yang masih dapat dipergunakan:</label>
                                            <input type="text" class="form-control rupiah" id="sisaBudgetRKAPKeuangan"
                                                name="SisaBudgetRKAPKeuangan" placeholder="Masukkan sisa budget RKAP">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tombol kembali & tombol submit -->
                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('usulan-investasi.index') }}" class="btn btn-secondary me-2">
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


@endsection
@push('js')
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ Session::get('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatRupiah(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    var separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix === undefined ? rupiah : (rupiah ? prefix + ' ' + rupiah : '');
            }

            document.querySelectorAll('.rupiah').forEach(function(input) {
                input.addEventListener('input', function(e) {
                    let caret = this.selectionStart;
                    let value = this.value;
                    let oldLength = value.length;
                    let formatted = formatRupiah(value, 'Rp');
                    this.value = formatted;
                    let newLength = formatted.length;
                    this.setSelectionRange(caret + (newLength - oldLength), caret + (newLength -
                        oldLength));
                });
            });
        });
    </script>
@endpush
