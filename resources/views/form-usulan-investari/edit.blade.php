@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Edit Usulan Investasi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Usulan Investasi</a></li>
                    <li class="breadcrumb-item active">Edit Usulan Investasi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Edit Usulan Investasi</h4>
                </div>
                <form action="{{ route('usulan-investasi.update', ['usulan_investasi' => $usulan ? $usulan->id : 0]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{ $IdPengajuan }}" name="IdPengajuan">
                    <input type="hidden" value="{{ $PengajuanItemId }}" name="PengajuanItemId">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-1">Departemen Peminta</h5>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Tanggal</label>
                                        <input type="date" class="form-control" name="Tanggal"
                                            value="{{ old('Tanggal', $usulan?->Tanggal) }}">
                                        @error('Tanggal')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Departemen</label>
                                        <select class="form-select select2" name="Divisi">
                                            <option value="">-- Pilih Departemen --</option>
                                            @foreach ($departemen as $d)
                                                <option value="{{ $d->id }}"
                                                    @if (
                                                        (!is_null(old('Divisi')) && old('Divisi') == $d->id) ||
                                                            (is_null(old('Divisi')) && isset($usulan) && $usulan->Divisi == $d->id)) selected @endif>
                                                    {{ $d->Nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Divisi')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Nama Kepala Divisi</label>
                                        <select class="form-select select2" name="NamaKadiv">
                                            <option value="">-- Pilih Kepala Divisi --</option>
                                            @foreach ($user as $u)
                                                <option value="{{ $u->id }}"
                                                    @if (
                                                        (!is_null(old('NamaKadiv')) && old('NamaKadiv') == $u->id) ||
                                                            (is_null(old('NamaKadiv')) && isset($usulan) && $usulan->NamaKadiv == $u->id)) selected @endif>
                                                    {{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('NamaKadiv')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <select name="Kategori" class="form-select">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Pembelian Baru"
                                                @if (old('Kategori', $usulan?->Kategori) == 'Pembelian Baru') selected @endif>
                                                Pembelian Baru</option>
                                            <option value="Penggantian" @if (old('Kategori', $usulan?->Kategori) == 'Penggantian') selected @endif>
                                                Penggantian</option>
                                            <option value="Perbaikan" @if (old('Kategori', $usulan?->Kategori) == 'Perbaikan') selected @endif>
                                                Perbaikan</option>
                                        </select>
                                        @error('Kategori')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-1">Departemen Pembelian</h5>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Tanggal</label>
                                        <input type="date" class="form-control" name="Tanggal2"
                                            value="{{ old('Tanggal2', $usulan?->Tanggal2) }}">
                                        @error('Tanggal2')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Departemen</label>
                                        <select class="form-select select2" name="Divisi2">
                                            <option value="">-- Pilih Departemen --</option>
                                            @foreach ($departemen as $d)
                                                <option value="{{ $d->id }}"
                                                    @if (
                                                        (!is_null(old('Divisi2')) && old('Divisi2') == $d->id) ||
                                                            (is_null(old('Divisi2')) && isset($usulan) && $usulan->Divisi2 == $d->id)) selected @endif>
                                                    {{ $d->Nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Divisi2')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Nama Kepala Divisi</label>
                                        <select class="form-select select2" name="NamaKadiv2">
                                            <option value="">-- Pilih Kepala Divisi --</option>
                                            @foreach ($user as $u)
                                                <option value="{{ $u->id }}"
                                                    @if (
                                                        (!is_null(old('NamaKadiv2')) && old('NamaKadiv2') == $u->id) ||
                                                            (is_null(old('NamaKadiv2')) && isset($usulan) && $usulan->NamaKadiv2 == $u->id)) selected @endif>
                                                    {{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('NamaKadiv2')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <select name="Kategori2" class="form-select">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Pembelian Baru"
                                                @if (old('Kategori2', $usulan?->Kategori2) == 'Pembelian Baru') selected @endif>
                                                Pembelian Baru</option>
                                            <option value="Penggantian" @if (old('Kategori2', $usulan?->Kategori2) == 'Penggantian') selected @endif>
                                                Penggantian</option>
                                            <option value="Perbaikan" @if (old('Kategori2', $usulan?->Kategori2) == 'Perbaikan') selected @endif>
                                                Perbaikan</option>
                                        </select>
                                        @error('Kategori2')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="Keterangan" class="form-label fw-bold">Dengan ini kami ajukan permohonan untuk
                                pengadaan barang / jasa dengan alasan sebagai berikut :</label>
                            <textarea class="form-control" name="Alasan" id="Keterangan" rows="3"
                                placeholder="Masukkan keterangan tambahan di sini...">{{ old('Alasan', $usulan?->Alasan) }}</textarea>
                            @error('Alasan')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Daftar Item Vendor --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">List Item dari Vendor yang di-ACC</label>
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th>Nama Barang</th>
                                            <th>Vendor</th>
                                            <th width="10%">Jumlah</th>
                                            <th>Harga</th>
                                            <th>Diskon</th>
                                            <th>PPN</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $grandTotal = 0;
                                            function rupiah($angka)
                                            {
                                                return 'Rp ' . number_format($angka, 0, ',', '.');
                                            }
                                            $usulanItems = old('items', $usulan->items ?? []);
                                        @endphp

                                        @forelse ($usulanItems as $key => $rekom)
                                            @php
                                                $isArr = is_array($rekom);
                                                $jumlah = $isArr ? $rekom['Jumlah'] ?? 0 : 0;
                                                $harga = $isArr ? $rekom['Harga'] ?? 0 : 0;
                                                $diskon = $isArr ? $rekom['Diskon'] ?? 0 : 0;
                                                $ppn = $isArr ? $rekom['Ppn'] ?? 0 : 0;

                                                $harga_num = preg_replace('/[^\d]/', '', (string) $harga);
                                                $harga_num = $harga_num !== '' ? (int) $harga_num : 0;

                                                $diskon_num = preg_replace('/[^\d]/', '', (string) $diskon);
                                                $diskon_num = $diskon_num !== '' ? (int) $diskon_num : 0;

                                                $jumlah_num = is_numeric($jumlah) ? $jumlah : 0;
                                                $ppn_num = is_numeric($ppn) ? $ppn : 0;

                                                $subtotal = $jumlah_num * $harga_num - $diskon_num;
                                                $totalPpn = $subtotal * ($ppn_num / 100);
                                                $total = $subtotal + $totalPpn;
                                                $grandTotal += $total;
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <select class="form-select select2"
                                                        name="items[{{ $key }}][NamaBarang]"
                                                        style="width: 100%;" data-placeholder="Pilih barang">
                                                        @foreach ($barang as $b)
                                                            <option value="{{ $b->id }}"
                                                                @if (
                                                                    (isset($rekom['NamaBarang']) && $rekom['NamaBarang'] == $b->id) ||
                                                                        (!isset($rekom['NamaBarang']) && old('items.' . $key . '.NamaBarang') == $b->id)) selected @endif>
                                                                {{ $b->Nama }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('items.' . $key . '.NamaBarang')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <select class="form-select select2"
                                                        name="items[{{ $key }}][Vendor]" style="width: 100%;"
                                                        data-placeholder="Pilih Vendor">
                                                        @if ($vendor ?? false)
                                                            @foreach ($vendor as $v)
                                                                <option value="{{ $v->id }}"
                                                                    @if ((isset($rekom['Vendor']) && $v->id == $rekom['Vendor']) || old('items.' . $key . '.Vendor') == $v->id) selected @endif>
                                                                    {{ $v->Nama }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('items.' . $key . '.Vendor')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $key }}][Jumlah]"
                                                        class="form-control" placeholder="Masukkan jumlah..."
                                                        value="{{ old('items.' . $key . '.Jumlah', $jumlah) }}">
                                                    @error('items.' . $key . '.Jumlah')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="items[{{ $key }}][Harga]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan harga..."
                                                            value="{{ old('items.' . $key . '.Harga', is_numeric($harga) ? number_format($harga, 0, ',', '.') : $harga) }}">
                                                    </div>
                                                    @error('items.' . $key . '.Harga')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="items[{{ $key }}][Diskon]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan diskon..."
                                                            value="{{ old('items.' . $key . '.Diskon', is_numeric($diskon) ? number_format($diskon, 0, ',', '.') : $diskon) }}">
                                                    </div>
                                                    @error('items.' . $key . '.Diskon')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01"
                                                            name="items[{{ $key }}][Ppn]" class="form-control"
                                                            placeholder="Masukkan PPN..."
                                                            value="{{ old('items.' . $key . '.Ppn', $ppn) }}">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="text-muted">{{ rupiah($totalPpn) }}</small>
                                                    @error('items.' . $key . '.Ppn')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="items[{{ $key }}][Total]"
                                                            class="form-control" placeholder="Total otomatis"
                                                            value="{{ number_format($total, 0, ',', '.') }}" readonly>
                                                    </div>
                                                    @error('items.' . $key . '.Total')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Belum ada item.</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            function formatRupiah(angka) {
                                                angka = angka.replace(/[^,\d]/g, '');
                                                var split = angka.split(',');
                                                var sisa = split[0].length % 3,
                                                    rupiah = split[0].substr(0, sisa),
                                                    ribuan = split[0].substr(sisa).match(/\d{3}/g);

                                                if (ribuan) {
                                                    rupiah += (sisa ? '.' : '') + ribuan.join('.');
                                                }
                                                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                                                return rupiah;
                                            }
                                            document.querySelectorAll('.rupiah-input').forEach(function(input) {
                                                input.addEventListener('input', function(e) {
                                                    var value = input.value;
                                                    var cleanValue = value.replace(/[^,\d]/g, '');
                                                    var formatted = formatRupiah(cleanValue);
                                                    input.value = formatted;
                                                });
                                            });
                                        });
                                    </script>
                                </table>
                                @if ($errors->has('items'))
                                    <div class="text-danger mt-1">{{ $errors->first('items') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Rincian Biaya</label>
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th>Biaya / Harga Akhir</th>
                                            <th>Suplier yang dipilih</th>
                                            <th>Harga</th>
                                            <th>Diskon</th>
                                            <th>Ppn</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $hargaSatuan = old('BiayaAkhir', $usulan->BiayaAkhir ?? 0);
                                            $diskon = old('Diskon', $usulan->Diskon ?? 0);
                                            $dpp =
                                                (int) preg_replace('/[^\d]/', '', $hargaSatuan) -
                                                (int) preg_replace('/[^\d]/', '', $diskon);
                                            $ppn = old('Ppn', $usulan->Ppn ?? 0);
                                            $total = old(
                                                'Total',
                                                $usulan->Total ?? $dpp + (is_numeric($ppn) ? $ppn : 0),
                                            );
                                            $vendorDipilihId = $usulan->VendorDipilih ?? null;
                                            $vendorDipilihNama = '';
                                            if ($vendorDipilihId && isset($vendor)) {
                                                foreach ($vendor as $v) {
                                                    if ($v->id == $vendorDipilihId) {
                                                        $vendorDipilihNama = $v->Nama;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="BiayaAkhir" class="form-control rupiah"
                                                        value="{{ is_numeric($hargaSatuan) ? number_format($hargaSatuan, 0, ',', '.') : $hargaSatuan }}">
                                                </div>
                                                @error('BiayaAkhir')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="hidden" name="VendorDipilih"
                                                    value="{{ old('VendorDipilih', $vendorDipilihId) }}">
                                                <span>{{ $vendorDipilihNama }}</span>
                                                @error('VendorDipilih')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="HargaSatuan" class="form-control rupiah"
                                                        value="{{ is_numeric($hargaSatuan) ? number_format($hargaSatuan, 0, ',', '.') : $hargaSatuan }}">
                                                </div>
                                                @error('HargaSatuan')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="Diskon" class="form-control rupiah"
                                                        value="{{ is_numeric($diskon) ? number_format($diskon, 0, ',', '.') : $diskon }}">
                                                </div>
                                                @error('Diskon')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="Ppn" class="form-control rupiah"
                                                        value="{{ is_numeric($ppn) ? number_format($ppn, 0, ',', '.') : $ppn }}">
                                                </div>
                                                @error('Ppn')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" name="Total" class="form-control rupiah"
                                                        value="{{ is_numeric($total) ? number_format($total, 0, ',', '.') : $total }}">
                                                </div>
                                                @error('Total')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- RKAP --}}
                        <div class="mb-4">
                            <div class="row">
                                @php
                                    $disabledDepartemen =
                                        auth()->user() &&
                                        method_exists(auth()->user(), 'hasRole') &&
                                        auth()->user()->hasRole('Keuangan')
                                            ? 'disabled'
                                            : '';
                                @endphp
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 {{ $disabledDepartemen ? 'bg-light' : '' }}">
                                        <h5 class="fw-bold mb-2">Verifikasi RKAP <span
                                                class="fw-normal">(Departemen)</span>
                                        </h5>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap"
                                                        id="rkapYaDepartemen" value="Y"
                                                        @if (old('SudahRkap', $usulan?->SudahRkap) == 'Y') checked @endif
                                                        {{ $disabledDepartemen }}>
                                                    <label class="form-check-label" for="rkapYaDepartemen">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap"
                                                        id="rkapTidakDepartemen" value="N"
                                                        @if (old('SudahRkap', $usulan?->SudahRkap) == 'N') checked @endif
                                                        {{ $disabledDepartemen }}>
                                                    <label class="form-check-label"
                                                        for="rkapTidakDepartemen">Tidak</label>
                                                </div>
                                            </div>
                                            @error('SudahRkap')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold" for="sisaBudgetRKAPDepartemen">Sisa Budget
                                                dari
                                                RKAP untuk tahun ini yang masih dapat dipergunakan:</label>
                                            <input type="text" class="form-control rupiah"
                                                id="sisaBudgetRKAPDepartemen" name="SisaBudget"
                                                placeholder="Masukkan sisa budget RKAP"
                                                value="{{ old('SisaBudget', $usulan?->SisaBudget) }}"
                                                {{ $disabledDepartemen }}>
                                            @error('SisaBudget')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if ($disabledDepartemen)
                                            <small class="text-muted">User dengan role <b>Keuangan</b> tidak dapat mengisi
                                                bagian ini.</small>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $disabled =
                                        auth()->user() &&
                                        method_exists(auth()->user(), 'hasRole') &&
                                        auth()->user()->hasRole('Keuangan')
                                            ? ''
                                            : 'disabled';
                                @endphp
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 {{ $disabled ? 'bg-light' : '' }}">
                                        <h5 class="fw-bold mb-2">Verifikasi RKAP <span class="fw-normal">(Keuangan)</span>
                                        </h5>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap2"
                                                        id="rkapYaKeuangan" value="Y"
                                                        @if (old('SudahRkap2', $usulan?->SudahRkap2) == 'Y') checked @endif
                                                        {{ $disabled }}>
                                                    <label class="form-check-label" for="rkapYaKeuangan">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap2"
                                                        id="rkapTidakKeuangan" value="N"
                                                        @if (old('SudahRkap2', $usulan?->SudahRkap2) == 'N') checked @endif
                                                        {{ $disabled }}>
                                                    <label class="form-check-label" for="rkapTidakKeuangan">Tidak</label>
                                                </div>
                                            </div>
                                            @error('SudahRkap2')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold" for="sisaBudgetRKAPKeuangan">Sisa Budget
                                                dari RKAP untuk tahun ini yang masih dapat dipergunakan:</label>
                                            <input type="text" class="form-control rupiah" id="sisaBudgetRKAPKeuangan"
                                                name="SisaBudget2" placeholder="Masukkan sisa budget RKAP"
                                                value="{{ old('SisaBudget2', $usulan?->SisaBudget2) }}"
                                                {{ $disabled }}>
                                            @error('SisaBudget2')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if ($disabled)
                                            <small class="text-muted">Hanya user dengan role <b>Keuangan</b> yang dapat
                                                mengisi bagian ini.</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('pp.show', encrypt($IdPengajuan)) }}" class="btn btn-secondary me-2">
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
