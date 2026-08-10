@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Usulan Investasi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Usulan Investasi</a></li>
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
                <form id="formUsulanInvestasi" action="{{ route('usulan-investasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $data->id }}" name="IdPengajuan">
                    <input type="hidden" value="{{ $PengajuanItemId }}" name="PengajuanItemId">
                    <input type="hidden" id="hiddenIdDirektur" name="DirekturId" value="">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-1">Departemen Peminta</h5>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold">Tanggal</label>
                                        <input type="date" class="form-control" name="Tanggal"
                                            value="{{ isset($usulan) && $usulan->Tanggal ? $usulan->Tanggal : old('Tanggal') }}">
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
                                                        (isset($usulan) && $usulan->Divisi == $d->id) ||
                                                            old('Divisi') == $d->id ||
                                                            (!old('Divisi') && isset($data->DepartemenId) && $data->DepartemenId == $d->id)) selected @endif>
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
                                        <select class="form-select select2" name="NamaKadiv" required>
                                            <option value="">-- Pilih Kepala Divisi --</option>
                                            @foreach ($user as $u)
                                                <option value="{{ $u->id }}"
                                                    @if (isset($usulan) && $usulan->NamaKadiv == $u->id) selected
                                                    @elseif(old('NamaKadiv') == $u->id)
                                                        selected @endif>
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
                                                @if (isset($usulan) && $usulan->Kategori == 'Pembelian Baru') selected
                                                @elseif(old('Kategori2', $data->Tujuan ?? '') == 'Pembelian Baru')
                                                    selected @endif>
                                                Pembelian Baru</option>
                                            <option value="Penggantian"
                                                @if (isset($usulan) && $usulan->Kategori == 'Penggantian') selected
                                                @elseif(old('Kategori2', $data->Tujuan ?? '') == 'Penggantian')
                                                    selected @endif>
                                                Penggantian</option>
                                            <option value="Perbaikan"
                                                @if (isset($usulan) && $usulan->Kategori == 'Perbaikan') selected
                                                @elseif(old('Kategori2', $data->Tujuan ?? '') == 'Perbaikan')
                                                    selected @endif>
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
                                            value="{{ isset($usulan) && $usulan->Tanggal2 ? $usulan->Tanggal2 : old('Tanggal2') }}">
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
                                                        (isset($usulan) && $usulan->Divisi2 == $d->id) ||
                                                            old('Divisi2') == $d->id ||
                                                            (!old('Divisi2') && isset($data->DepartemenId) && $data->DepartemenId == $d->id)) selected @endif>
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
                                        <select class="form-select select2" name="NamaKadiv2" required>
                                            <option value="" disabled
                                                {{ !isset($usulan) && !old('NamaKadiv2') ? 'selected' : '' }}>-- Pilih
                                                Kepala Divisi --</option>
                                            @foreach ($user as $u)
                                                <option value="{{ $u->id }}"
                                                    @if (isset($usulan) && $usulan->NamaKadiv2 == $u->id) @elseif(old('NamaKadiv2') == $u->id) selected @endif>
                                                    {{ $u->name }}
                                                </option>
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
                                                @if (isset($usulan) && $usulan->Kategori2 == 'Pembelian Baru') selected
                                                @elseif(old('Kategori2', $data->Tujuan ?? '') == 'Pembelian Baru')
                                                    selected @endif>
                                                Pembelian Baru</option>
                                            <option value="Penggantian"
                                                @if (isset($usulan) && $usulan->Kategori2 == 'Penggantian') selected
                                                @elseif(old('Kategori2', $data->Tujuan ?? '') == 'Penggantian')
                                                    selected @endif>
                                                Penggantian</option>
                                            <option value="Perbaikan"
                                                @if (isset($usulan) && $usulan->Kategori2 == 'Perbaikan') selected
                                                @elseif(old('Kategori2', $data->Tujuan ?? '') == 'Perbaikan')
                                                    selected @endif>
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
                                placeholder="Masukkan keterangan tambahan di sini...">{{ isset($usulan) && $usulan->Alasan ? $usulan->Alasan : old('Alasan') }}</textarea>
                            @error('Alasan')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Daftar Item Vendor --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">List Barang dan Vendor Yang Diajukan</label>
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:2%">No</th>
                                            <th style="width:20%">Nama Barang / Jasa</th>
                                            <th>Vendor</th>
                                            <th>Harga Awal</th>
                                            <th>Harga Nego</th>
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

                                        @endphp
                                        @forelse ($dataRekom->getRekomedasiDetail as $key => $rekomDetail)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <select class="form-select"
                                                        name="items[{{ $key }}][NamaBarang]"
                                                        style="width: 100%; pointer-events: none; background-color: #e9ecef;"
                                                        data-placeholder="Pilih barang" tabindex="-1">
                                                        @foreach ($barang as $b)
                                                            <option value="{{ $b->id }}"
                                                                @if (old('items.' . $key . '.NamaBarang', $rekomDetail->NamaPermintaan ?? '') == $b->id) selected @endif>
                                                                {{ $b->Nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $key }}][NamaBarang]"
                                                        value="{{ old('items.' . $key . '.NamaBarang', $rekomDetail->NamaPermintaan ?? '') }}">
                                                    @error('items.' . $key . '.NamaBarang')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                {{-- <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01"
                                                            name="items[{{ $key }}][Ppn]" class="form-control"
                                                            placeholder="Masukkan PPN..." value="{{ $ppn }}"
                                                            readonly>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="text-muted">{{ rupiah($totalPpn) }}</small>
                                                    @error('items.' . $key . '.Ppn')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td> --}}
                                                <td>
                                                    <select class="form-select select2"
                                                        name="items[{{ $key }}][Vendor]" style="width: 100%;"
                                                        data-placeholder="Pilih Vendor" readonly>
                                                        <option value="{{ $rekomDetail->id }}"
                                                            {{ old('items.' . $key . '.Vendor', $rekomDetail->IdVendor) == $rekomDetail->IdVendor ? 'selected' : '' }}>
                                                            {{ $rekomDetail->getNamaVendor->Nama }}
                                                        </option>
                                                    </select>
                                                    @error('items.' . $key . '.Vendor')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="items[{{ $key }}][Harga]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan harga..."
                                                            value="{{ old('items.' . $key . '.Harga', isset($rekomDetail->HargaAwal) ? number_format((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaAwal), 0, ',', '.') : 0) }}"
                                                            readonly>
                                                    </div>
                                                    @error('items.' . $key . '.Harga')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text"
                                                            name="items[{{ $key }}][HargaNego]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan harga nego..."
                                                            value="{{ old('items.' . $key . '.HargaNego', isset($rekomDetail->HargaNego) ? number_format((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego), 0, ',', '.') : 0) }}"
                                                            readonly>
                                                    </div>
                                                    @error('items.' . $key . '.HargaNego')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="items[{{ $key }}][Total]"
                                                            class="form-control" placeholder="Total otomatis"
                                                            value="{{ number_format($rekomDetail->HargaNego, 0, ',', '.') }}"
                                                            readonly>
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

                                </table>
                                @if ($errors->has('items'))
                                    <div class="text-danger mt-1">{{ $errors->first('items') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Rincian Biaya Disetujui</label>
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:2%">No</th>
                                            <th style="width:20%">Nama Barang / Jasa</th>
                                            <th>Vendor</th>
                                            <th>Harga Awal</th>
                                            <th>Harga Nego</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $itemsAcc = $dataRekom->getRekomedasiDetail
                                                ->where('Rekomendasi', 1)
                                                ->values();
                                        @endphp
                                        @forelse ($itemsAcc as $key => $rekomDetail)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <select class="form-select"
                                                        name="itemAcc[{{ $key }}][NamaBarang]"
                                                        style="width: 100%; pointer-events: none; background-color: #e9ecef;"
                                                        data-placeholder="Pilih barang" tabindex="-1">
                                                        @foreach ($barang as $b)
                                                            <option value="{{ $b->id }}"
                                                                @if (old('itemAcc.' . $key . '.NamaBarang', $rekomDetail->NamaPermintaan ?? '') == $b->id) selected @endif>
                                                                {{ $b->Nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="itemAcc[{{ $key }}][NamaBarang]"
                                                        value="{{ old('itemAcc.' . $key . '.NamaBarang', $rekomDetail->NamaPermintaan ?? '') }}">
                                                    @error('itemAcc.' . $key . '.NamaBarang')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <select class="form-select select2"
                                                        name="itemAcc[{{ $key }}][Vendor]" style="width: 100%;"
                                                        data-placeholder="Pilih Vendor" readonly>
                                                        <option value="{{ $rekomDetail->id }}"
                                                            {{ old('itemAcc.' . $key . '.Vendor', $rekomDetail->IdVendor) == $rekomDetail->IdVendor ? 'selected' : '' }}>
                                                            {{ $rekomDetail->getNamaVendor->Nama }}
                                                        </option>
                                                    </select>
                                                    @error('itemAcc.' . $key . '.Vendor')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="itemAcc[{{ $key }}][Harga]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan harga..."
                                                            value="{{ old('itemAcc.' . $key . '.Harga', isset($rekomDetail->HargaAwal) ? number_format((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaAwal), 0, ',', '.') : 0) }}"
                                                            readonly>
                                                    </div>
                                                    @error('itemAcc.' . $key . '.Harga')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text"
                                                            name="itemAcc[{{ $key }}][HargaNego]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan harga nego..."
                                                            value="{{ old('itemAcc.' . $key . '.HargaNego', isset($rekomDetail->HargaNego) ? number_format((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego), 0, ',', '.') : 0) }}"
                                                            readonly>
                                                    </div>
                                                    @error('itemAcc.' . $key . '.HargaNego')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="itemAcc[{{ $key }}][Total]"
                                                            class="form-control" placeholder="Total otomatis"
                                                            value="{{ number_format($rekomDetail->HargaNego, 0, ',', '.') }}"
                                                            readonly>
                                                    </div>
                                                    @error('itemAcc.' . $key . '.Total')
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

                                </table>
                            </div>
                        </div>
                        {{-- RKAP --}}
                        <div class="mb-4">
                            <div class="row">
                                @php
                                    $deptDisabled =
                                        auth()->user() && auth()->user()->hasRole('Keuangan') ? 'readonly' : '';
                                @endphp
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 {{ $deptDisabled ? 'bg-light' : '' }}">
                                        <h5 class="fw-bold mb-2">Verifikasi RKAP <span
                                                class="fw-normal">(Departemen)</span>
                                        </h5>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap"
                                                        id="rkapYaDepartemen" value="Y"
                                                        @if ((isset($usulan) && $usulan->SudahRkap == 'Y') || old('SudahRkap') == 'Y') checked @endif
                                                        {{ $deptDisabled }}>
                                                    <label class="form-check-label" for="rkapYaDepartemen">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap"
                                                        id="rkapTidakDepartemen" value="N"
                                                        @if ((isset($usulan) && $usulan->SudahRkap == 'N') || old('SudahRkap') == 'N') checked @endif
                                                        {{ $deptDisabled }}>
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
                                                value="{{ isset($usulan) && $usulan->SisaBudget ? $usulan->SisaBudget : old('SisaBudget') }}"
                                                {{ $deptDisabled }}>
                                            @error('SisaBudget')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if ($deptDisabled)
                                            <small class="text-muted">Hanya user <b>bukan</b> Keuangan yang dapat mengisi
                                                bagian ini.</small>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $disabled = auth()->user() && auth()->user()->hasRole('Keuangan') ? '' : 'disabled';
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
                                                        @if ((isset($usulan) && $usulan->SudahRkap2 == 'Y') || old('SudahRkap2') == 'Y') checked @endif
                                                        {{ $disabled }}>
                                                    <label class="form-check-label" for="rkapYaKeuangan">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="SudahRkap2"
                                                        id="rkapTidakKeuangan" value="N"
                                                        @if ((isset($usulan) && $usulan->SudahRkap2 == 'N') || old('SudahRkap2') == 'N') checked @endif
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
                                                value="{{ isset($usulan) && $usulan->SisaBudget2 ? $usulan->SisaBudget2 : old('SisaBudget2') }}"
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
                        <div class="mb-4">

                        </div>
                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" id="btnSimpanUsulan" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>


    </div>
@endsection
@include('form-usulan-investari.modal-pilih-direktur')
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

    @if (Session::get('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: '{{ Session::get('error') }}',
                iconColor: '#FFC107',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#FFC107',
            });
        </script>
    @endif
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil nilai Jenis dari backend (pastikan variabel $data tersedia)
            const jenisPengajuan = {{ $data->Jenis ?? 1 }};

            const form = document.getElementById('formUsulanInvestasi');
            const btnSimpan = document.getElementById('btnSimpanUsulan');
            const hiddenIdDirektur = document.getElementById('hiddenIdDirektur');

            let isSubmitting = false;

            // --- FUNGSI DISABLE/ENABLE INPUT (Tetap sama seperti sebelumnya) ---
            function disableInputActions() {
                window.onkeydown = function(e) { if (!e || (e.key && e.key === "F5")) return true; e.preventDefault(); return false; };
                window.onkeypress = function(e) { e.preventDefault(); return false; };
                window.onkeyup = function(e) { e.preventDefault(); return false; };
                window.onmousedown = function(e) { e.preventDefault(); return false; };
                window.onmouseup = function(e) { e.preventDefault(); return false; };
                window.onclick = function(e) { e.preventDefault(); return false; };
                window.oncontextmenu = function(e) { e.preventDefault(); return false; };
                document.body.style.pointerEvents = "none";
                setTimeout(function() {
                    var swal = document.querySelector('.swal2-container');
                    if (swal) swal.style.pointerEvents = 'auto';
                }, 100);
            }

            function enableInputActions() {
                window.onkeydown = null; window.onkeypress = null; window.onkeyup = null;
                window.onmousedown = null; window.onmouseup = null; window.onclick = null;
                window.oncontextmenu = null; document.body.style.pointerEvents = "";
            }

            // --- FUNGSI PROSES FINAL SUBMIT (SweetAlert + Submit) ---
            function processFinalSubmission() {
                if (isSubmitting) return;

                Swal.fire({
                    title: "Konfirmasi Simpan",
                    html: "Apakah anda yakin ingin menyimpan Usulan Investasi ini?<br><small>Notifikasi akan dikirim ke email terkait.</small>",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Simpan",
                    cancelButtonText: "Batal",
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                }).then(function(result) {
                    if (result.isConfirmed) {
                        let detik = 0;
                        let interval;
                        Swal.fire({
                            title: 'Mengirim notifikasi email...',
                            html: `<b>Harap tunggu <span id="timerSimpan">0</span> detik.</b><br>
                                <small>Usulan dalam proses penyimpanan dan mengirim notifikasi ke email terkait.<br>Silakan tunggu sampai proses selesai.<br><b>Selama proses berjalan, keyboard & mouse dinonaktifkan</b></small>
                                <br><br><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                var el = document.getElementById('timerSimpan');
                                detik = 0;
                                interval = setInterval(function() {
                                    detik++;
                                    if (el) el.textContent = detik;
                                }, 1000);
                                disableInputActions();
                            },
                            willClose: () => {
                                clearInterval(interval);
                                enableInputActions();
                            }
                        });
                        isSubmitting = true;
                        setTimeout(function() {
                            form.submit();
                        }, 800);
                    }
                });
            }

            // --- EVENT LISTENER UTAMA FORM SUBMIT ---
            if (form && btnSimpan) {
                form.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        e.preventDefault();
                        return;
                    }

                    // CEK KONDISI: Jika Jenis BUKAN 1, tampilkan modal dulu
                    if (jenisPengajuan != 1) {
                        e.preventDefault(); // Cegah submit langsung
                        const modalDirektur = new bootstrap.Modal(document.getElementById('modalDirektur'));
                        modalDirektur.show();
                        return; // Stop eksekusi di sini, tunggu aksi user di modal
                    }

                    // Jika Jenis == 1, langsung lanjut ke proses final
                    e.preventDefault();
                    processFinalSubmission();
                });
            }

            // --- LOGIKA DI DALAM MODAL DIREKTUR ---
            const btnLanjutSimpan = document.getElementById('btnLanjutSimpan');
            const selectDirektur = document.getElementById('selectDirektur');
            const errorDirektur = document.getElementById('errorDirektur');

            if (btnLanjutSimpan) {
                btnLanjutSimpan.addEventListener('click', function() {
                    const selectedValue = selectDirektur.value;

                    // Validasi: Harus dipilih
                    if (!selectedValue) {
                        selectDirektur.classList.add('is-invalid');
                        errorDirektur.style.display = 'block';
                        return;
                    }

                    // Jika valid, masukkan nilai ke hidden input form utama
                    if (hiddenIdDirektur) {
                        hiddenIdDirektur.value = selectedValue;
                    }

                    // Tutup modal
                    const modalEl = document.getElementById('modalDirektur');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    // Lanjutkan ke proses submit utama (SweetAlert + Submit)
                    processFinalSubmission();
                });
            }

            // Hapus error styling saat user mulai memilih
            if (selectDirektur) {
                selectDirektur.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');
                        errorDirektur.style.display = 'none';
                    }
                });
            }

            // --- FORMAT RUPIAH (Tetap sama seperti sebelumnya) ---
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
                    this.setSelectionRange(caret + (newLength - oldLength), caret + (newLength - oldLength));
                });
            });
        });
    </script>
@endpush
