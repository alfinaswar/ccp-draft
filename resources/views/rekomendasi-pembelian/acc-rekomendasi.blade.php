@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Rekomendasi Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Rekomendasi Pembelian</a></li>
                    <li class="breadcrumb-item active">Form Rekomendasi</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title m-0">
                        Rekomendasi Pembelian untuk Pengajuan: <strong>{{ $data->KodePengajuan }} /
                            {{ $data->getPerusahaan->NamaLengkap }}</strong>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-info" style="font-size:1rem;">
                            Status: {{ $data->Status ?? '-' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="mb-3">Informasi Barang</h5>
                        <table class="table align-middle" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:180px;">Nama Barang</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Merek</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>{{ $data->getPengajuanItem[0]->getBarang->Tipe ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Lanjut Presentasi ?</th>
                                    <td>
                                        <select name="Presentasi" id="lanjutPresentasi" class="form-select" required>
                                            <option value="" disabled
                                                {{ empty($data->getPengajuanItem[0]->getRekomendasi->Presentasi ?? '') ? 'selected' : '' }}>
                                                Pilih</option>
                                            <option value="Y"
                                                {{ ($data->getPengajuanItem[0]->getRekomendasi->Presentasi ?? '') == 'Y' ? 'selected' : '' }}>
                                                Ya</option>
                                            <option value="N"
                                                {{ ($data->getPengajuanItem[0]->getRekomendasi->Presentasi ?? '') == 'N' ? 'selected' : '' }}>
                                                Tidak</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="trTanggalPresentasi"
                                    style="display: {{ old('rekomendasi.0.Presentasi', $data->getPengajuanItem[0]->getRekomendasi->Presentasi ?? '') == 'Y' ? '' : 'none' }};">
                                    <th>Tanggal Presentasi</th>
                                    <td>
                                        <input type="date" name="TanggalPresentasi" class="form-control"
                                            value="{{ old('rekomendasi.0.TanggalPresentasi', $data->getPengajuanItem[0]->getRekomendasi->TanggalPresentasi ?? '') }}">
                                    </td>
                                </tr>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        var select = document.getElementById('lanjutPresentasi');
                                        var tanggalRow = document.getElementById('trTanggalPresentasi');
                                        if (select) {
                                            select.addEventListener('change', function() {
                                                if (this.value === 'Y') {
                                                    tanggalRow.style.display = '';
                                                } else {
                                                    tanggalRow.style.display = 'none';
                                                    tanggalRow.querySelector('input').value = '';
                                                }
                                            });
                                        }
                                    });
                                </script>
                                </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center" role="alert" style="min-height: 70px;">
                        <i class="fa fa-exclamation-circle me-2" style="align-self: center; font-size: 1.6rem;"></i>
                        <div class="d-flex align-items-center" style="min-height: 50px;">
                            <ol class="mb-0 ps-2">
                                <li>Isi rekomendasi pembelian untuk setiap Vendor dengan data yang lengkap. Setelah
                                    rekomendasi untuk Vendor ini diisi, Anda dapat melanjutkan ke Vendor berikutnya.</li>
                                <li>Semua kolom rekomendasi wajib diisi.</li>
                                <li>Jika {{ auth()->user()->name }} sedang sibuk, Anda dapat menyimpan data sebagai draft
                                    dan melanjutkan pengisian di lain waktu.</li>
                            </ol>
                        </div>
                    </div>
                    <form id="formRekomendasi" action="{{ route('rekomendasi.update-rekomendasi') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs tab-style-1 d-sm-flex d-block" role="tablist" id="vendorTabs">
                            @foreach ($data->getVendor as $vIdx => $Vendor)
                                @php
                                    // The tab is enabled if it is the first, or if all previous have nilai fields (HargaAwal & HargaNego) terisi.
                                    $enabled = $vIdx === 0;
                                    if ($vIdx > 0) {
                                        $enabled = true;
                                        for ($i = 0; $i < $vIdx; $i++) {
                                            $prevRek = $data->getVendor[$i]->getRekomendasi ?? null;
                                            if (
                                                empty(optional($prevRek)->HargaAwal) ||
                                                empty(optional($prevRek)->HargaNego)
                                            ) {
                                                $enabled = false;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $vIdx === 0 ? 'active' : '' }} {{ $enabled ? '' : 'disabled' }}"
                                        id="vendor-tab-{{ $vIdx }}" data-bs-toggle="tab"
                                        href="{{ $enabled ? '#vendor-pane-' . $vIdx : 'javascript:void(0)' }}"
                                        role="tab" aria-controls="vendor-pane-{{ $vIdx }}"
                                        aria-selected="{{ $vIdx === 0 ? 'true' : 'false' }}"
                                        @if (!$enabled) tabindex="-1" aria-disabled="true" @endif>
                                        {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="vendorTabPanes">
                            @if ($data->Jenis == 1)
                                @foreach ($data->getVendor as $vIdx => $Vendor)
                                    <div class="tab-pane fade {{ $vIdx === 0 ? 'show active' : '' }}"
                                        id="vendor-pane-{{ $vIdx }}" role="tabpanel"
                                        aria-labelledby="vendor-tab-{{ $vIdx }}">

                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdPengajuan]"
                                            value="{{ $data->id }}">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][PengajuanItemId]"
                                            value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                                        {{-- Assuming IdRekomendasi will be filled in the backend or left empty for create --}}
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdRekomendasi]"
                                            value="">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdVendor]"
                                            value="{{ $Vendor->NamaVendor }}">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][NamaPermintaan]"
                                            value="{{ $data->getPengajuanItem[0]->getBarang->id ?? '' }}">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][KodePerusahaan]"
                                            value="{{ $data->KodePerusahaan ?? '' }}">
                                        @if (!empty($Vendor) && !empty($Vendor->SuratPenawaranVendor))
                                            <div class="mb-4">
                                                <div class="card border-0 shadow-sm bg-light">
                                                    <div class="card-body d-flex align-items-center">
                                                        <span class="me-3" style="font-size: 2rem; color: #dc3545;">
                                                            <i class="fa fa-file-pdf"></i>
                                                        </span>
                                                        <div>
                                                            <div class="fw-bold mb-1">Surat Penawaran Vendor</div>
                                                            <a href="{{ asset('storage/penawaran_vendor/' . $Vendor->SuratPenawaranVendor) }}"
                                                                target="_blank" class="btn btn-sm btn-primary px-3">
                                                                <i class="fa fa-eye"></i> Lihat Surat Penawaran
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <table class="table align-middle nilai-table" style="width:100%;"
                                            data-vidx="{{ $vIdx }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" style="width:5%;">No</th>
                                                    <th class="text-center" style="width:25%;">Parameter</th>
                                                    <th class="text-center" style="width:70%;">Deskripsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td class="fw-bold">Harga Awal</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][HargaAwal]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan Harga Awal"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaAwal) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaAwal : old("rekomendasi.$vIdx.HargaAwal") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td class="fw-bold">Harga Nego</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][HargaNego]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan Harga Nego"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaNego) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaNego : old("rekomendasi.$vIdx.HargaNego") }}">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td class="fw-bold">Spesifikasi</td>
                                                    <td>
                                                        <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Spesifikasi]" rows="15"
                                                            placeholder="Masukkan Spesifikasi">{!! isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Spesifikasi)
                                                                ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Spesifikasi
                                                                : old("rekomendasi.$vIdx.Spesifikasi") !!}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td class="fw-bold">Negara Produksi</td>
                                                    <td>
                                                        <select name="rekomendasi[{{ $vIdx }}][NegaraProduksi]"
                                                            class="form-control select2" data-placeholder="Pilih Negara">
                                                            <option value="">Pilih Negara</option>
                                                            @foreach ($negara as $n)
                                                                <option value="{{ $n->Kode }}"
                                                                    {{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->NegaraProduksi) && $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->NegaraProduksi == $n->Kode ? 'selected' : (old("rekomendasi.$vIdx.NegaraProduksi") == $n->Kode ? 'selected' : '') }}>
                                                                    {{ $n->Nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">5</td>
                                                    <td class="fw-bold">Garansi</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][Garansi]"
                                                            class="form-control" placeholder="Garansi"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Garansi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Garansi : old("rekomendasi.$vIdx.Garansi") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">6</td>
                                                    <td class="fw-bold">Teknisi</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][Teknisi]"
                                                            class="form-control" placeholder="Teknisi"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Teknisi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Teknisi : old("rekomendasi.$vIdx.Teknisi") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">7</td>
                                                    <td class="fw-bold">Bmhp</td>
                                                    <td>
                                                        <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Bmhp]" rows="4"
                                                            placeholder="Bahan Medis Habis Pakai (Bmhp)">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Bmhp) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Bmhp : old("rekomendasi.$vIdx.Bmhp") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">8</td>
                                                    <td class="fw-bold">Populasi</td>
                                                    <td>
                                                        <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Populasi]" placeholder="Populasi">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi : old("rekomendasi.$vIdx.Populasi") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">9</td>
                                                    <td class="fw-bold">Spare Part</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][SparePart]"
                                                            placeholder="Spare Part"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->SparePart) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->SparePart : old("rekomendasi.$vIdx.SparePart") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">10</td>
                                                    <td class="fw-bold">Backup Unit</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][BackupUnit]"
                                                            placeholder="Backup Unit"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->BackupUnit) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->BackupUnit : old("rekomendasi.$vIdx.BackupUnit") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">11</td>
                                                    <td class="fw-bold">TOP (Term of Payment)</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][Top]"
                                                            placeholder="Contoh: 30 hari"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Top) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Top : old("rekomendasi.$vIdx.Top") }}">
                                                    </td>
                                                </tr>
                                                {{-- <tr>
                                                <td class="text-center">11</td>
                                                <td class="fw-bold">Rekomendasi</td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="rekomendasi[{{ $vIdx }}][Rekomendasi]"
                                                        placeholder="Isi Rekomendasi"
                                                        value="{{ old("rekomendasi.$vIdx.Rekomendasi") }}">
                                                </td>
                                            </tr> --}}
                                                <tr>
                                                    <td class="text-center">12</td>
                                                    <td class="fw-bold">Keterangan</td>
                                                    <td>
                                                        <textarea class="form-control" name="rekomendasi[{{ $vIdx }}][Keterangan]" rows="3"
                                                            placeholder="Masukkan Keterangan">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Keterangan) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Keterangan : old("rekomendasi.$vIdx.Keterangan") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">13</td>
                                                    <td class="fw-bold">Rekomendasi Vendor</td>
                                                    <td>
                                                        @php
                                                            $allSelected = [];
                                                            foreach ($data->getVendor as $_idx => $_v) {
                                                                if (
                                                                    isset($_v->getRekomendasi->Rekomendasi) &&
                                                                    $_v->getRekomendasi->Rekomendasi != ''
                                                                ) {
                                                                    $allSelected[$_idx] =
                                                                        $_v->getRekomendasi->Rekomendasi;
                                                                } else {
                                                                    $oldVal = old(
                                                                        "rekomendasi.$_idx.RekomendasiSelect",
                                                                    );
                                                                    if ($oldVal) {
                                                                        $allSelected[$_idx] = $oldVal;
                                                                    }
                                                                }
                                                            }
                                                            $options = [
                                                                1 => 'Rekomendasi 1',
                                                                2 => 'Rekomendasi 2',
                                                                3 => 'Rekomendasi 3',
                                                            ];
                                                        @endphp
                                                        <select class="form-select mb-2 rekomendasi-select"
                                                            name="rekomendasi[{{ $vIdx }}][RekomendasiSelect]"
                                                            data-vendor-idx="{{ $vIdx }}">
                                                            <option value="">Pilih Rekomendasi</option>
                                                            @foreach ($options as $optVal => $optLabel)
                                                                @php
                                                                    $selectedInOtherTab = collect($allSelected)
                                                                        ->except($vIdx)
                                                                        ->contains((string) $optVal);
                                                                    $thisSelected = isset(
                                                                        $data->getRekomendasi[0]->getRekomedasiDetail[
                                                                            $vIdx
                                                                        ]->Rekomendasi,
                                                                    )
                                                                        ? $data->getRekomendasi[0]->getRekomedasiDetail[
                                                                            $vIdx
                                                                        ]->Rekomendasi
                                                                        : old("rekomendasi.$vIdx.RekomendasiSelect");
                                                                @endphp
                                                                <option value="{{ $optVal }}"
                                                                    @if ($thisSelected == $optVal) selected @endif
                                                                    @if ($selectedInOtherTab && $thisSelected != $optVal) style="display:none;" @endif>
                                                                    {{ $optLabel }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            @else
                                @if (optional($data->getRekomendasi->first())->File)
                                    <div class="alert alert-outline-primary mb-4 d-flex align-items-center" role="alert"
                                        style="border-width:2px;">
                                        <i class="bi bi-file-earmark-text me-2" style="font-size: 1.7rem;"></i>
                                        <div class="w-100">
                                            <div class="fw-semibold mb-1">File Rekomendasi</div>
                                            <div class="mb-2 text-secondary" style="font-size: 15px;">
                                                File rekomendasi pembelian telah diunggah.
                                            </div>
                                            <a href="{{ asset('storage/rekomendasi_file/' . $data->getRekomendasi->first()->File) }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i> Lihat / Download File
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                @foreach ($data->getVendor as $vIdx => $Vendor)
                                    <div class="tab-pane fade {{ $vIdx === 0 ? 'show active' : '' }}"
                                        id="vendor-pane-{{ $vIdx }}" role="tabpanel"
                                        aria-labelledby="vendor-tab-{{ $vIdx }}">

                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdPengajuan]"
                                            value="{{ $data->id }}">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][PengajuanItemId]"
                                            value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                                        {{-- Assuming IdRekomendasi will be filled in the backend or left empty for create --}}
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdRekomendasi]"
                                            value="">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdVendor]"
                                            value="{{ $Vendor->NamaVendor }}">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][NamaPermintaan]"
                                            value="{{ $data->getPengajuanItem[0]->getBarang->id ?? '' }}">
                                        <input type="hidden" name="rekomendasi[{{ $vIdx }}][KodePerusahaan]"
                                            value="{{ $data->KodePerusahaan ?? '' }}">
                                        <table class="table align-middle nilai-table" style="width:100%;"
                                            data-vidx="{{ $vIdx }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" style="width:5%;">No</th>
                                                    <th class="text-center" style="width:25%;">Parameter</th>
                                                    <th class="text-center" style="width:70%;">Deskripsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td class="fw-bold">Harga Awal</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][HargaAwal]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan Harga Awal"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaAwal) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaAwal : old("rekomendasi.$vIdx.HargaAwal") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td class="fw-bold">Harga Nego</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][HargaNego]"
                                                            class="form-control rupiah-input"
                                                            placeholder="Masukkan Harga Nego"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaNego) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaNego : old("rekomendasi.$vIdx.HargaNego") }}">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td class="fw-bold">Spesifikasi</td>
                                                    <td>
                                                        <textarea class="ckeditor" id="ckeditor" class="form-control" name="rekomendasi[{{ $vIdx }}][Spesifikasi]"
                                                            rows="10" placeholder="Masukkan Spesifikasi">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Spesifikasi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Spesifikasi : old("rekomendasi.$vIdx.Spesifikasi") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td class="fw-bold">Populasi</td>
                                                    <td>
                                                        <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Populasi]" placeholder="Populasi">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi : old("rekomendasi.$vIdx.Populasi") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">5</td>
                                                    <td class="fw-bold">Garansi</td>
                                                    <td>
                                                        <input type="text"
                                                            name="rekomendasi[{{ $vIdx }}][Garansi]"
                                                            class="form-control" placeholder="Garansi"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Garansi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Garansi : old("rekomendasi.$vIdx.Garansi") }}">
                                                    </td>
                                                </tr>

                                                <!-- Section Penyesuaian Name -->
                                                <tr>
                                                    <td class="text-center">6</td>
                                                    <td class="fw-bold">Time Line Pekerjaan</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][TimeLinePekerjaan]"
                                                            placeholder="Time Line Pekerjaan"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->TimeLinePekerjaan) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->TimeLinePekerjaan : old("rekomendasi.$vIdx.TimeLinePekerjaan") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">7</td>
                                                    <td class="fw-bold">Jumlah Pekerja</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][JumlahPekerja]"
                                                            placeholder="Jumlah Pekerja"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->JumlahPekerja) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->JumlahPekerja : old("rekomendasi.$vIdx.JumlahPekerja") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">8</td>
                                                    <td class="fw-bold">Populasi</td>
                                                    <td>
                                                        <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Populasi]" placeholder="Populasi">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi : old("rekomendasi.$vIdx.Populasi") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">9</td>
                                                    <td class="fw-bold">Luasan</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][Luasan]"
                                                            placeholder="Masukkan Luasan"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Luasan) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Luasan : old("rekomendasi.$vIdx.Luasan") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">10</td>
                                                    <td class="fw-bold">Review Vendor</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][ReviewVendor]"
                                                            placeholder="Masukkan Review Vendor"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->ReviewVendor) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->ReviewVendor : old("rekomendasi.$vIdx.ReviewVendor") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">11</td>
                                                    <td class="fw-bold">TOP (Term of Payment)</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="rekomendasi[{{ $vIdx }}][Top]"
                                                            placeholder="Contoh: 30 hari"
                                                            value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Top) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Top : old("rekomendasi.$vIdx.Top") }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">12</td>
                                                    <td class="fw-bold">Keterangan</td>
                                                    <td>
                                                        <textarea class="form-control" name="rekomendasi[{{ $vIdx }}][Keterangan]" rows="3"
                                                            placeholder="Masukkan Keterangan">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Keterangan) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Keterangan : old("rekomendasi.$vIdx.Keterangan") }}</textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">13</td>
                                                    <td class="fw-bold">Rekomendasi Vendor</td>
                                                    <td>
                                                        @php
                                                            $allSelected = [];
                                                            foreach ($data->getVendor as $_idx => $_v) {
                                                                if (
                                                                    isset($_v->getRekomendasi->Rekomendasi) &&
                                                                    $_v->getRekomendasi->Rekomendasi != ''
                                                                ) {
                                                                    $allSelected[$_idx] =
                                                                        $_v->getRekomendasi->Rekomendasi;
                                                                } else {
                                                                    $oldVal = old(
                                                                        "rekomendasi.$_idx.RekomendasiSelect",
                                                                    );
                                                                    if ($oldVal) {
                                                                        $allSelected[$_idx] = $oldVal;
                                                                    }
                                                                }
                                                            }
                                                            $options = [
                                                                1 => 'Rekomendasi 1',
                                                                2 => 'Rekomendasi 2',
                                                                3 => 'Rekomendasi 3',
                                                            ];
                                                        @endphp
                                                        <select class="form-select mb-2 rekomendasi-select"
                                                            name="rekomendasi[{{ $vIdx }}][RekomendasiSelect]"
                                                            data-vendor-idx="{{ $vIdx }}">
                                                            <option value="">Pilih Rekomendasi</option>
                                                            @foreach ($options as $optVal => $optLabel)
                                                                @php
                                                                    $selectedInOtherTab = collect($allSelected)
                                                                        ->except($vIdx)
                                                                        ->contains((string) $optVal);
                                                                    $thisSelected = isset(
                                                                        $data->getRekomendasi[0]->getRekomedasiDetail[
                                                                            $vIdx
                                                                        ]->Rekomendasi,
                                                                    )
                                                                        ? $data->getRekomendasi[0]->getRekomedasiDetail[
                                                                            $vIdx
                                                                        ]->Rekomendasi
                                                                        : old("rekomendasi.$vIdx.RekomendasiSelect");
                                                                @endphp
                                                                <option value="{{ $optVal }}"
                                                                    @if ($thisSelected == $optVal) selected @endif
                                                                    @if ($selectedInOtherTab && $thisSelected != $optVal) style="display:none;" @endif>
                                                                    {{ $optLabel }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            @endif


                        </div>
                        <table class="table table-borderless" style="max-width:100%; margin: 0 auto;">
                            <colgroup>
                                <col style="width: 50%;">
                                <col style="width: 50%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td class="text-center align-bottom">
                                        Yang Menegosiasi
                                    </td>
                                    <td class="text-center align-bottom">
                                        Disetujui oleh,<br>
                                        Procurement Group
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 70px;" class="text-center">
                                        @php
                                            $qrNego = $rekomendasi->qrCodeNego ?? null;
                                        @endphp
                                        @if ($qrNego)
                                            <img src="data:image/png;base64,{{ $qrNego }}" alt="QR Negosiasi"
                                                style="max-width:120px; max-height:120px;">
                                        @else
                                            @php
                                                $ttdPath =
                                                    $data->DiajukanOleh && !empty($data->getDiajukanOleh->tandatangan)
                                                        ? asset(
                                                            'storage/upload/tandatangan/' .
                                                                $data->getDiajukanOleh->tandatangan,
                                                        )
                                                        : asset('assets/img/ccp/default_approve.png');
                                            @endphp
                                            <img src="{{ $ttdPath }}" alt="TTD"
                                                style="max-width:120px; max-height:120px;">
                                        @endif
                                    </td>
                                    <td style="height: 70px;" class="text-center">
                                        @php
                                            $qrApprove = $rekomendasi->qrCodeApprove ?? null;
                                        @endphp
                                        @if (!empty($qrApprove))
                                            <img src="data:image/png;base64,{{ $qrApprove }}" alt="QR Approval"
                                                style="max-width:120px; max-height:120px;">
                                        @else
                                            @php
                                                $approveTtdPath =
                                                    !empty($data->getProcurementGroup) &&
                                                    !empty($data->getProcurementGroup->tandatangan)
                                                        ? asset(
                                                            'storage/upload/tandatangan/' .
                                                                $data->getProcurementGroup->tandatangan,
                                                        )
                                                        : asset('assets/img/ccp/default_approve.png');
                                            @endphp
                                            <img src="{{ $approveTtdPath }}" alt="TTD"
                                                style="max-width:120px; max-height:120px;">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" style="padding-bottom:0;">
                                        <hr style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                                    </td>
                                    <td class="text-center" style="padding-bottom:0;">
                                        <hr style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center align-top">

                                        <span style="font-weight:600;">
                                            {{ $data->getPengajuanItem[0]->getRekomendasi->getUserNego->name ?? '' }}
                                        </span>
                                    </td>
                                    <td class="text-center align-top">

                                        <span style="font-weight:600;">
                                            {{ $data->getPengajuanItem[0]->getRekomendasi->getDisetujuiOleh->name ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="button" id="btnAccRekomendasi"
                                class="btn btn-warning d-flex align-items-center">
                                <i class="fa fa-save me-2"></i> Simpan Sebagai Draft
                            </button>
                            @if (!empty($Vendor->getRekomendasi) && !is_null($Vendor->getRekomendasi->IdPengajuan))
                                <a href="{{ route('rekomendasi.store-selesai-acc', $Vendor->getRekomendasi->IdPengajuan) }}"
                                    class="btn btn-success btn-md d-flex align-items-center">
                                    <i class="fa fa-check-circle me-2"></i> Selesaikan Review
                                </a>
                            @endif
                            <a href="{{ route('rekomendasi.show', encrypt($data->id)) }}"
                                class="btn btn-secondary d-flex align-items-center">
                                <i class="fa fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>


                    </form>

                    <a href="{{ route('rekomendasi.show', encrypt($data->id)) }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>




    </div>
    </div>
@endsection
@push('js')
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#4BCC1F',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#4BCC1F',
                });
            }, 500);
        </script>
    @endif
    @if (Session::get('error'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#dc3545',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#dc3545',
                });
            }, 500);
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnAccRekomendasi');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin menyimpan dan menyetujui rekomendasi ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan & Setujui',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.form.submit();
                        }
                    });
                });
            }
        });
    </script>

    <script>
        function updateSubtotalsAndGrandTotal(vendorTable) {
            let grandTotal = 0;
            vendorTable.find("tbody tr").each(function() {
                let subtotal = 0;
                $(this).find('.nilai-input').each(function() {
                    let nilai = parseFloat($(this).val());
                    if (isNaN(nilai)) nilai = 0;
                    if (nilai > 5) {
                        $(this).val(5);
                        nilai = 5;
                    }
                    if (nilai < 0) {
                        $(this).val(0);
                        nilai = 0;
                    }
                    subtotal += nilai;
                });
                $(this).find('.subtotal-input').val(subtotal);
                grandTotal += subtotal;
            });
            vendorTable.find('.grandtotal-input').val(grandTotal);
        }

        $(document).ready(function() {
            $('.nilai-table').each(function() {
                let vendorTable = $(this);
                updateSubtotalsAndGrandTotal(vendorTable);

                vendorTable.on('input', '.nilai-input', function() {
                    updateSubtotalsAndGrandTotal(vendorTable);
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatRupiah(angka, prefix = 'Rp ') {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    rupiah += (sisa ? '.' : '') + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix + rupiah;
            }

            document.querySelectorAll('.rupiah-input').forEach(function(input) {
                input.addEventListener('input', function(e) {
                    // Remove any non-numeric but allow comma (,)
                    let value = this.value.replace(/[^,\d]/g, '');
                    this.value = formatRupiah(value);
                });

                // If initial value exists and not already formatted, format it
                if (input.value && !input.value.startsWith('Rp')) {
                    let value = input.value.replace(/[^,\d]/g, '');
                    input.value = formatRupiah(value);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function refreshRekomendasiOptions() {
                // get all select elements
                var selects = document.querySelectorAll('.rekomendasi-select');
                // collect selected values and their indexes
                var selectedVals = [];
                selects.forEach(function(sel, idx) {
                    if (sel.value) {
                        selectedVals.push({
                            index: idx,
                            value: sel.value
                        });
                    }
                });

                selects.forEach(function(sel, idx) {
                    var originalVal = sel.value;
                    for (var i = 0; i < sel.options.length; ++i) {
                        var opt = sel.options[i];
                        if (opt.value === "") continue; // skip placeholder
                        // Is this value already selected in another select?
                        var selectedInAnother = false;
                        for (var s = 0; s < selectedVals.length; ++s) {
                            // Safari strict type, compare as string
                            if (selectedVals[s].index !== idx && String(selectedVals[s].value) === String(
                                    opt.value)) {
                                selectedInAnother = true;
                                break;
                            }
                        }
                        // always allow current selected option, block only for others
                        if (selectedInAnother && String(originalVal) !== String(opt.value)) {
                            opt.hidden = true;
                            // for Safari: also force display:none (for older Safari)
                            opt.style.display = "none";
                        } else {
                            opt.hidden = false;
                            opt.style.display = "";
                        }
                    }
                });
            }

            // re-run on every selection change
            var selects = document.querySelectorAll('.rekomendasi-select');
            selects.forEach(function(sel) {
                sel.addEventListener('change', refreshRekomendasiOptions);
            });
            // for Safari when navigating back/forward or AJAX, run on DOMContentLoaded and after a short delay
            setTimeout(refreshRekomendasiOptions, 100); // to catch initial rendering quirks in Safari
            refreshRekomendasiOptions();
        });
    </script>

@endpush
