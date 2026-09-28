@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">

            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('ajukan.index') }}">Pengajuan Pembelian</a></li>
                    <li class="active">Detail</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-file-invoice"></i>
                    Detail Pengajuan Pembelian
                </h3>
                <p class="ph-subtitle">Lihat informasi lengkap mengenai pengajuan pembelian ini</p>
            </div>

            <!-- KANAN: Icon + Tombol Kembali -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="{{ route('ajukan.index') }}" class="ph-btn">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-s2">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Detail Pengajuan Pembelian -
                        <span style="font-size: 0.92em;">
                            {{ $data->KodePengajuan ?? '-' }}
                        </span>
                    </h4>
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <span class="badge text-dark" style="font-size: 1em;">
                            {{ $data->Status ?? '-' }}
                        </span>
                        @if ($data->Status === 'Ditolak')
                            <button type="button" class="btn btn-danger ms-2" id="show-ccp-note">
                                <i class="fa fa-sticky-note"></i> Lihat Catatan dari CCP
                            </button>
                        @endif
                    </div>

                </div>


                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Tanggal</strong></label>
                            <input type="text" class="form-control"
                                value="{{ isset($data->Tanggal) ? $data->Tanggal : '-' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Jenis</strong></label>
                            <input type="text" class="form-control" value="{{ $data->getJenisPermintaan->Nama ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Permintaan dari Departemen</strong></label>
                            <input type="text" class="form-control"
                                value="{{ isset($data->getDepartemen->Nama) ? $data->getDepartemen->Nama : '-' }}" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Perkiraan Utilisasi Bulanan</strong></label>
                            <input type="text" class="form-control" value="{{ $data->PerkiraanUtilitasiBulanan ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Perkiraan BEP Pada Tahun</strong></label>
                            <input type="text" class="form-control" value="{{ $data->PerkiraanBepPadaTahun ?? '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><strong>RKAP</strong></label>
                            <input type="text" class="form-control" value="{{ $data->Rkap ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><strong>Nominal RKAP</strong></label>
                            <input type="text" class="form-control"
                                value="{{ number_format($data->NominalRkap ?? 0, 0, ',', '.') }}" readonly>

                        </div>

                    </div>

                    @if ($data->Status == 'Siap Presentasi' || $data->Status == 'Selesai')
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body py-2 px-3">
                                    <form id="formTanggalPresentasi"
                                        action="{{ route('rekomendasi.update-tanggal-presentasi', $data->id) }}"
                                        method="POST" class="d-flex align-items-end gap-2">
                                        @csrf
                                        @method('POST')
                                        <div class="w-100">
                                            <label class="form-label mb-1"><strong>Tanggal Presentasi</strong></label>
                                            <input type="date" class="form-control" name="TanggalPresentasi"
                                                id="TanggalPresentasi"
                                                value="{{ $data->TanggalPresentasi ? \Carbon\Carbon::parse($data->TanggalPresentasi)->format('Y-m-d') : '' }}"
                                                required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-md ms-2"
                                            id="btnSubmitTanggalPresentasi">
                                            <i class="fa fa-save"></i> Simpan Tanggal Presentasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- PERBANDINGAN VENDOR --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Perbandingan Vendor
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $vendorCount = isset($data->getVendor) ? count($data->getVendor) : 0;
                            @endphp

                            <ul class="nav nav-tabs d-sm-flex d-block" role="tablist">
                                @for ($vn = 0; $vn < $vendorCount; $vn++)
                                    <li class="nav-item">
                                        <a class="nav-link{{ $vn === 0 ? ' active' : '' }}" data-bs-toggle="tab"
                                            data-bs-target="#vendor_tab_{{ $vn }}"
                                            href="#vendor_tab_{{ $vn }}">
                                            Vendor {{ $vn + 1 }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>

                            <div class="tab-content">
                                @for ($vnIdx = 0; $vnIdx < $vendorCount; $vnIdx++)
                                    @php
                                        $vendorList = $data->getVendor
                                            ? $data->getVendor
                                                ->map(function ($item) {
                                                    return $item;
                                                })
                                                ->values()
                                            : collect();
                                        // dd($vendorList);
                                        $vendorData = $vendorList[$vnIdx] ?? null;
                                        $selectedVendor = null;
                                        if ($vendorData && isset($vendorData->NamaVendor)) {
                                            // Cari nama vendor dengan membandingkan id
                                            $selectedVendor = $vendor->firstWhere('id', $vendorData->NamaVendor);
                                        }

                                        // Hitung total berdasarkan detail barang vendor (urutan tidak diubah)
                                        $totalHargaSebelumDiskonAll = 0;
                                        $totalDiskonAll = 0;
                                        $totalHargaSetelahDiskonAll = 0;

                                        if (
                                            isset($vendorData) &&
                                            isset($vendorData->getVendorDetail) &&
                                            is_iterable($vendorData->getVendorDetail) &&
                                            count($vendorData->getVendorDetail)
                                        ) {
                                            foreach ($vendorData->getVendorDetail as $barang) {
                                                $jumlah = $barang->Jumlah ?? 0;
                                                $hargaSatuan = $barang->HargaSatuan ?? 0;
                                                $diskon = $barang->Diskon ?? 0;
                                                $jenisDiskon = $barang->JenisDiskon ?? null; // "persen" atau "nominal"

                                                $totalBarangHarga = $jumlah * $hargaSatuan;
                                                $nominalDiskon = 0;
                                                if ($diskon && $jenisDiskon) {
                                                    if (strtolower($jenisDiskon) == 'persen') {
                                                        $nominalDiskon = $totalBarangHarga * ($diskon / 100);
                                                    } else {
                                                        $nominalDiskon = $diskon;
                                                    }
                                                }
                                                $totalHargaSebelumDiskonAll += $totalBarangHarga;
                                                $totalDiskonAll += $nominalDiskon;
                                                $totalHargaSetelahDiskonAll += $totalBarangHarga - $nominalDiskon;
                                            }
                                        }
                                        // PPN
                                        $ppn = isset($vendorData->Ppn) ? floatval($vendorData->Ppn) : 0;
                                        $totalPpn = $ppn ? ($totalHargaSetelahDiskonAll * $ppn) / 100 : 0;
                                        $grandTotal = $totalHargaSetelahDiskonAll + $totalPpn;
                                    @endphp
                                    <div class="tab-pane{{ $vnIdx == 0 ? ' active' : '' }}"
                                        id="vendor_tab_{{ $vnIdx }}" role="tabpanel">
                                        <div class="row mb-3">
                                            <div class="col-xl-6">
                                                <div class="card">
                                                    <div class="row g-0">
                                                        <div class="col-md-8">
                                                            <div class="card-header">
                                                                <div class="card-title">
                                                                    <label
                                                                        class="form-label mb-0"><strong>Vendor</strong></label>
                                                                    <div class="form-control-plaintext fw-bold">
                                                                        {{ $selectedVendor ? $selectedVendor->Nama : '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6 class="card-title fw-semibold mb-2">Informasi Vendor
                                                                </h6>
                                                                <table class="table table-bordered mb-0">
                                                                    <tr>
                                                                        <th>Nama PIC</th>
                                                                        <td>{{ $selectedVendor && $selectedVendor->NamaPic ? $selectedVendor->NamaPic : '-' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>No HP PIC</th>
                                                                        <td>{{ $selectedVendor && $selectedVendor->NoHpPic ? $selectedVendor->NoHpPic : '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-4 d-flex align-items-center justify-content-center">
                                                            <img src="{{ asset('assets/img/ccp/vendor.png') }}"
                                                                class="img-fluid rounded-end object-fit-cover"
                                                                alt="...">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">

                                                <div class="col-m2 mt-2">
                                                    <label class="form-label"><strong>Surat Penawaran Vendor
                                                            {{ $vnIdx + 1 }}</strong></label>
                                                    @php
                                                        $penawaranFile = isset($vendorData)
                                                            ? $vendorData->SuratPenawaranVendor ?? null
                                                            : null;
                                                    @endphp
                                                    @if ($penawaranFile)
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/penawaran_vendor/' . $penawaranFile) }}"
                                                                target="_blank" rel="noopener noreferrer"
                                                                class="btn btn-outline-secondary btn-sm">
                                                                <i class="fa fa-external-link-alt"></i> Preview Surat
                                                                Penawaran Vendor {{ $vnIdx + 1 }}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="form-text text-muted">
                                                            Tidak ada file Surat Penawaran Vendor {{ $vnIdx + 1 }} yang
                                                            diupload.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table align-middle"
                                                id="table-detail-pengajuan-show-{{ $vnIdx }}">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Barang</th>
                                                        <th>Merek / Tipe</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga Satuan</th>
                                                        <th>Jenis Diskon</th>
                                                        <th>Diskon</th>
                                                        <th>Total Diskon</th>
                                                        <th>Total Harga</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($vendorData) && isset($vendorData->getVendorDetail) && count($vendorData->getVendorDetail))
                                                        @foreach ($vendorData->getVendorDetail as $key => $barang)
                                                            @php
                                                                $barangMaster = $masterbarang->firstWhere(
                                                                    'id',
                                                                    $barang->NamaBarang,
                                                                );
                                                                $jumlah = $barang->Jumlah ?? 0;
                                                                $hargaSatuan = $barang->HargaSatuan ?? 0;
                                                                $diskon = $barang->Diskon ?? 0;
                                                                $jenisDiskon = $barang->JenisDiskon ?? null;
                                                                $totalBarangHarga = $jumlah * $hargaSatuan;
                                                                $nominalDiskon = 0;
                                                                if ($diskon && $jenisDiskon) {
                                                                    if (strtolower($jenisDiskon) == 'persen') {
                                                                        $nominalDiskon =
                                                                            $totalBarangHarga * ($diskon / 100);
                                                                    } else {
                                                                        $nominalDiskon = $diskon;
                                                                    }
                                                                }
                                                                $totalSetelahDiskon =
                                                                    $totalBarangHarga - $nominalDiskon;
                                                                // dd($barang->id);
                                                            @endphp
                                                            <tr>
                                                                <td width="5">{{ $key + 1 }}</td>
                                                                <td>
                                                                    <span>{{ $barangMaster ? $barangMaster->Nama : '-' }}</span>
                                                                </td>
                                                                <td>
                                                                    <span>
                                                                        {{ optional($barangMaster?->getMerk)->Nama ?? '-' }}
                                                                        /

                                                                        {{ $barangMaster?->Tipe ?? '-' }}

                                                                    </span>
                                                                </td>


                                                                <td>
                                                                    <span>{{ $jumlah }}</span>
                                                                </td>
                                                                <td>
                                                                    <span>Rp
                                                                        {{ number_format($hargaSatuan, 0, ',', '.') }}</span>
                                                                </td>
                                                                <td>
                                                                    <span>{{ $jenisDiskon ?? '-' }}</span>
                                                                </td>
                                                                <td>
                                                                    <span>
                                                                        {{ $diskon !== null ? number_format($diskon, 0, ',', '.') : '-' }}
                                                                    </span>
                                                                </td>

                                                                <td>
                                                                    <span>
                                                                        {{ $nominalDiskon ? number_format($nominalDiskon, 0, ',', '.') : '-' }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span>
                                                                        Rp
                                                                        {{ number_format($totalSetelahDiskon, 0, ',', '.') }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="8" class="text-center">Tidak ada barang pada
                                                                vendor ini.</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="table-responsive mt-3">
                                            <table class="table align-middle">
                                                <tbody>
                                                    <tr>
                                                        <th class="text-end" width="70%">Total Harga Sebelum Diskon:
                                                        </th>
                                                        <td width="10%">
                                                            Rp
                                                            {{ $totalHargaSebelumDiskonAll > 0 ? number_format($totalHargaSebelumDiskonAll, 0, ',', '.') : '-' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Harga Setelah Diskon:</th>
                                                        <td>
                                                            Rp
                                                            {{ $totalHargaSetelahDiskonAll > 0 ? number_format($totalHargaSetelahDiskonAll, 0, ',', '.') : '-' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Total Diskon:</th>
                                                        <td>
                                                            Rp
                                                            {{ $totalDiskonAll > 0 ? number_format($totalDiskonAll, 0, ',', '.') : '-' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">PPN (%) :</th>
                                                        <td>
                                                            {{ $ppn > 0 ? $ppn : '-' }}%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Total PPN (All):</th>
                                                        <td>
                                                            Rp
                                                            {{ $totalPpn > 0 ? number_format($totalPpn, 0, ',', '.') : '-' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-end">Grand Total:</th>
                                                        <td>
                                                            Rp
                                                            {{ $grandTotal > 0 ? number_format($grandTotal, 0, ',', '.') : '-' }}
                                                        </td>
                                                    </tr>
                                                    {{-- <tr>
                                                        <th class="text-end"></th>
                                                        <td>
                                                            {{ terbilang($grandTotal) }}
                                                        </td>
                                                    </tr> --}}
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    {{-- END PERBANDINGAN VENDOR --}}
                    {{-- DAFTAR ITEM YANG DIAJUKAN --}}
                    <div class="card mb-4 shadow-sm border-0">
                        {{-- ===== CARD HEADER UTAMA ===== --}}
                        <div
                            class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                            <div>
                                <h4 class="card-title mb-0">
                                    <i class="fa fa-list-check me-2 text-primary"></i>Daftar Item yang Diajukan
                                </h4>
                                <small class="text-muted">Kelola dan lengkapi dokumen untuk setiap item pengajuan</small>
                            </div>

                            @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                @php
                                    $isFsRequired = ($data->Jenis ?? null) == 1;
                                    $jmlDokumenPerItem = $isFsRequired ? 4 : 3;

                                    // Hitung total kelengkapan semua item
                                    $totalLengkapSemua = 0;
                                    foreach ($data->getPengajuanItem as $it) {
                                        $totalLengkapSemua +=
                                            ($it->getRekomendasi ? 1 : 0) +
                                            ($it->getHtaGpa ? 1 : 0) +
                                            ($isFsRequired && $it->getFs ? 1 : 0) +
                                            ($it->getFui ? 1 : 0);
                                    }
                                    $totalDokumenSemua = count($data->getPengajuanItem) * $jmlDokumenPerItem;
                                    $progressSemua =
                                        $totalDokumenSemua > 0
                                            ? round(($totalLengkapSemua / $totalDokumenSemua) * 100)
                                            : 0;
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        <i class="fa fa-box me-1"></i>{{ count($data->getPengajuanItem) }} Item
                                    </span>
                                    <span
                                        class="badge bg-{{ $progressSemua >= 75 ? 'success' : ($progressSemua >= 50 ? 'warning' : 'danger') }} fs-6 px-3 py-2">
                                        <i class="fa fa-chart-pie me-1"></i>Kelengkapan: {{ $progressSemua }}%
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- ===== CARD BODY ===== --}}
                        <div class="card-body">

                            @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                @php
                                    $isFsRequired = ($data->Jenis ?? null) == 1;
                                    $jmlDokumenPerItem = $isFsRequired ? 4 : 3;
                                    $colDoc = $isFsRequired ? 'col-md-6 col-xl-3' : 'col-md-6 col-xl-4';
                                @endphp

                                <div class="row">
                                    @foreach ($data->getPengajuanItem as $i => $item)
                                        @php
                                            // === Hitung kelengkapan dokumen item ini ===
                                            $adaRekomendasi = $item->getRekomendasi ? true : false;
                                            $hasHta = $item->getHtaGpa ? true : false;
                                            $adaFs = $item->getFs ? true : false;
                                            $adaFui = $item->getFui ? true : false;

                                            $totalDokumen = $jmlDokumenPerItem;
                                            $lengkapCount =
                                                ($adaRekomendasi ? 1 : 0) +
                                                ($hasHta ? 1 : 0) +
                                                ($isFsRequired && $adaFs ? 1 : 0) +
                                                ($adaFui ? 1 : 0);
                                            $progressPercent = ($lengkapCount / $totalDokumen) * 100;

                                            $progressColor = 'danger';
                                            if ($progressPercent >= 75) {
                                                $progressColor = 'success';
                                            } elseif ($progressPercent >= 50) {
                                                $progressColor = 'warning';
                                            } elseif ($progressPercent > 0) {
                                                $progressColor = 'info';
                                            }

                                            // Timestamp terakhir update
                                            $rekomendasiUpdate =
                                                $adaRekomendasi && isset($item->getRekomendasi->updated_at)
                                                    ? \Carbon\Carbon::parse(
                                                        $item->getRekomendasi->updated_at,
                                                    )->translatedFormat('d F Y H:i')
                                                    : null;

                                            $htaUpdate =
                                                $hasHta && isset($item->getHtaGpa->updated_at)
                                                    ? \Carbon\Carbon::parse(
                                                        $item->getHtaGpa->updated_at,
                                                    )->translatedFormat('d F Y H:i')
                                                    : null;

                                            $fsUpdate =
                                                $adaFs && isset($item->getFs->updated_at)
                                                    ? \Carbon\Carbon::parse($item->getFs->updated_at)->translatedFormat(
                                                        'd F Y H:i',
                                                    )
                                                    : null;

                                            $fuiUpdate =
                                                $adaFui && isset($item->getFui->updated_at)
                                                    ? \Carbon\Carbon::parse(
                                                        $item->getFui->updated_at,
                                                    )->translatedFormat('d F Y H:i')
                                                    : null;
                                        @endphp

                                        <div class="col-12 mb-4">
                                            <div class="card border shadow-sm h-100">

                                                {{-- ===== CARD HEADER ITEM ===== --}}
                                                <div
                                                    class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                                style="width:42px;height:42px;font-size:1.1rem;">
                                                                {{ $i + 1 }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-0 fw-bold">
                                                                {{ $item->getBarang->Nama ?? 'Item Tanpa Nama' }}</h5>
                                                            <small class="text-muted">
                                                                {{ $item->getBarang->getMerk->Nama ?? '-' }}
                                                                @if ($item->getBarang->Tipe ?? false)
                                                                    · {{ $item->getBarang->Tipe }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-{{ $progressColor }} fs-6 px-3 py-2">
                                                            {{ $lengkapCount }}/{{ $totalDokumen }} Dokumen Lengkap
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- ===== PROGRESS BAR ===== --}}
                                                <div class="px-3 pt-3">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ $progressColor }}"
                                                            role="progressbar" style="width: {{ $progressPercent }}%;"
                                                            aria-valuenow="{{ $progressPercent }}" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>

                                                {{-- ===== CARD BODY - GRID DOKUMEN ===== --}}
                                                <div class="card-body">
                                                    <div class="row g-3">

                                                        {{-- ─────────── 1. REKOMENDASI ─────────── --}}
                                                        <div class="{{ $colDoc }}">
                                                            <div
                                                                class="card h-100 {{ $adaRekomendasi ? 'border-success' : 'border-warning' }}">
                                                                <div class="card-body p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0 fw-semibold">
                                                                            <i
                                                                                class="fa fa-file-signature me-1 text-primary"></i>
                                                                            Rekomendasi
                                                                        </h6>
                                                                        @if ($adaRekomendasi)
                                                                            <span class="badge bg-success">Lengkap</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-warning text-dark">Proses</span>
                                                                        @endif
                                                                    </div>

                                                                    @if ($adaRekomendasi)
                                                                        <div class="d-flex flex-column gap-1">
                                                                            <div class="row gx-2">
                                                                                <div class="col-6">
                                                                                    <a href="{{ route('rekomendasi.detail-print', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                                        class="btn btn-info btn-sm w-100"
                                                                                        target="_blank"
                                                                                        style="min-width:150px">
                                                                                        <i class="fa fa-print"></i> Cetak
                                                                                    </a>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <a href="{{ route('rekomendasi.rekap', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                                        class="btn btn-warning btn-sm w-100"
                                                                                        target="_blank"
                                                                                        style="min-width:150px">
                                                                                        <i class="fa fa-file-alt"></i>
                                                                                        Rekap
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row gx-2 mt-1">
                                                                                <div class="col-6">
                                                                                    @if ($data->Status == 'Dalam Review' || $data->Status == 'Diajukan')
                                                                                        <a href="{{ route('rekomendasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                                            class="btn btn-primary btn-sm w-100"
                                                                                            style="min-width:150px">
                                                                                            <i class="fa fa-pen"></i> Tulis
                                                                                            Review CCP
                                                                                        </a>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    @can('rekomendasi-show')
                                                                                        <a href="{{ route('rekomendasi.detail-view', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                                            class="btn btn-secondary btn-sm w-100"
                                                                                            style="min-width:150px"
                                                                                            target="_blank">
                                                                                            <i class="fa fa-eye"></i> Rekom
                                                                                            GH
                                                                                        </a>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                            @if ($rekomendasiUpdate)
                                                                                <div class="mt-2 small text-secondary">
                                                                                    <i class="fa fa-clock me-1"></i>
                                                                                    Diperbarui: {{ $rekomendasiUpdate }}
                                                                                    WIB
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <a href="{{ route('rekomendasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                            class="btn btn-primary btn-sm w-100 mb-2">
                                                                            <i class="fa fa-pen"></i> Buat Rekomendasi
                                                                        </a>
                                                                        <div class="alert alert-warning p-2 mb-0 small">
                                                                            <i class="fa fa-info-circle me-1"></i>
                                                                            Dokumen belum dibuat.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- ─────────── 2. HTA / GPA ─────────── --}}
                                                        <div class="{{ $colDoc }}">
                                                            <div
                                                                class="card h-100 {{ $hasHta ? 'border-success' : 'border-secondary' }}">
                                                                <div class="card-body p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0 fw-semibold">
                                                                            <i
                                                                                class="fa fa-clipboard-check me-1 text-primary"></i>
                                                                            HTA / GPA
                                                                        </h6>
                                                                        @if ($hasHta)
                                                                            <span class="badge bg-success">Lengkap</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">Belum
                                                                                Ada</span>
                                                                        @endif
                                                                    </div>

                                                                    @if (!$hasHta)
                                                                        <div class="alert alert-warning p-2 mb-0 small">
                                                                            <i class="fa fa-info-circle me-1"></i>
                                                                            Dokumen HTA/GPA belum tersedia. Akan diisi oleh
                                                                            <strong>Logum / SMI</strong>.
                                                                        </div>
                                                                    @else
                                                                        <a href="{{ route('htagpa.show', [$data->id, $item->id]) }}"
                                                                            class="btn btn-success btn-sm w-100">
                                                                            <i class="fa fa-check-circle"></i> Lihat
                                                                            Dokumen HTA
                                                                        </a>

                                                                        @if ($htaUpdate)
                                                                            <div class="mt-2 small text-secondary">
                                                                                <i class="fa fa-clock me-1"></i>
                                                                                Diperbarui: {{ $htaUpdate }} WIB
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- ─────────── 3. FEASIBILITY STUDY (hanya jika Jenis == 1) ─────────── --}}
                                                        @if ($isFsRequired)
                                                            <div class="{{ $colDoc }}">
                                                                <div
                                                                    class="card h-100 {{ $adaFs ? 'border-success' : ($adaRekomendasi ? 'border-warning' : 'border-secondary') }}">
                                                                    <div class="card-body p-3">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0 fw-semibold">
                                                                                <i
                                                                                    class="fa fa-chart-line me-1 text-primary"></i>
                                                                                Feasibility Study
                                                                            </h6>
                                                                            @if ($adaFs)
                                                                                <span
                                                                                    class="badge bg-success">Lengkap</span>
                                                                            @elseif($adaRekomendasi)
                                                                                <span
                                                                                    class="badge bg-warning text-dark">Belum</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge bg-secondary">Menunggu</span>
                                                                            @endif
                                                                        </div>

                                                                        @if (!$adaRekomendasi)
                                                                            <div class="alert alert-danger p-2 mb-0 small">
                                                                                <i class="fa fa-clock me-1"></i>
                                                                                FS akan dibuat oleh
                                                                                <strong>Keuangan</strong> setelah
                                                                                Rekomendasi keluar.
                                                                            </div>
                                                                        @else
                                                                            @if ($adaFs)
                                                                                <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                                    class="btn btn-success btn-sm w-100">
                                                                                    <i class="fa fa-eye"></i> Lihat FS
                                                                                </a>

                                                                                @if ($fsUpdate)
                                                                                    <div class="mt-2 small text-secondary">
                                                                                        <i class="fa fa-clock me-1"></i>
                                                                                        Diperbarui: {{ $fsUpdate }} WIB
                                                                                    </div>
                                                                                @endif
                                                                            @else
                                                                                <div
                                                                                    class="alert alert-warning p-2 mb-0 small">
                                                                                    <i class="fa fa-info-circle me-1"></i>
                                                                                    FS akan dibuat oleh
                                                                                    <strong>Keuangan</strong>.
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- ─────────── 4. USULAN INVESTASI ─────────── --}}
                                                        <div class="{{ $colDoc }}">
                                                            <div
                                                                class="card h-100 {{ $adaFui ? 'border-success' : ($adaRekomendasi ? 'border-warning' : 'border-secondary') }}">
                                                                <div class="card-body p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0 fw-semibold">
                                                                            <i
                                                                                class="fa fa-lightbulb me-1 text-primary"></i>
                                                                            Usulan Investasi
                                                                        </h6>
                                                                        @if ($adaFui)
                                                                            <span class="badge bg-success">Lengkap</span>
                                                                        @elseif($adaRekomendasi)
                                                                            <span
                                                                                class="badge bg-warning text-dark">Belum</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-secondary">Menunggu</span>
                                                                        @endif
                                                                    </div>

                                                                    @if (!$adaFui)
                                                                        <div class="alert alert-warning p-2 mb-0 small">
                                                                            <i class="fa fa-info-circle me-1"></i>
                                                                            FUI akan diisi oleh <strong>Logum / SMI</strong>
                                                                            setelah rekomendasi diterbitkan.
                                                                        </div>
                                                                    @else
                                                                        <div class="d-flex gap-1">
                                                                            <a href="{{ route('usulan-investasi.show', [$data->id, $item->id]) }}"
                                                                                class="btn btn-success btn-sm flex-fill">
                                                                                <i class="fa fa-eye"></i> Lihat
                                                                            </a>
                                                                            <a href="{{ route('usulan-investasi.print', [$data->id, $item->id]) }}"
                                                                                class="btn btn-info btn-sm flex-fill"
                                                                                target="_blank">
                                                                                <i class="fa fa-print"></i> Cetak
                                                                            </a>
                                                                        </div>

                                                                        @if ($fuiUpdate)
                                                                            <div class="mt-2 small text-secondary">
                                                                                <i class="fa fa-clock me-1"></i>
                                                                                Diperbarui: {{ $fuiUpdate }} WIB
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fa fa-inbox fa-4x text-muted" style="opacity:0.4;"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">Data Item Belum Tersedia</h5>
                                    <p class="text-muted mb-0">Belum ada item yang ditambahkan ke pengajuan ini.</p>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="co2 text-end mt-3">
                        <a href="{{ route('rekomendasi.index') }}" class="btn btn-secondary me-2">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>

                        @if (
                            $data->Status == 'Diajukan' ||
                                $data->Status == 'Menunggu Rekomendasi GH' ||
                                $data->Status == 'Selesai Review' ||
                                $data->Status == 'Dalam Review' ||
                                $data->Status == 'Ditolak CEO')
                            <button type="button" class="btn btn-danger" id="btn-batalkan" data-bs-toggle="modal"
                                data-bs-target="#modal-batalkan">
                                <i class="fa fa-times"></i> Tolak Pengajuan
                            </button>

                            @include('rekomendasi-pembelian.modal-tolak')
                        @elseif($data->Status == 'Draft')
                            <button type="button" class="btn btn-success" id="btn-ajukan">
                                <i class="fa fa-paper-plane"></i> Ajukan Ke CCP
                            </button>
                            <form id="form-ajukan" action="{{ route('ajukan.update-status', $data->id) }}"
                                method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="Status" value="Diajukan">
                            </form>
                            @push('js')
                                <script>
                                    document.getElementById('btn-ajukan').addEventListener('click', function(e) {
                                        e.preventDefault();
                                        Swal.fire({
                                            title: 'Konfirmasi Pengajuan',
                                            text: 'Apakah Anda yakin ingin mengajukan permohonan ini? Pastikan semua dokumen tambahan telah lengkap.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#28a745',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, ajukan!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                document.getElementById('form-ajukan').submit();
                                            }
                                        });
                                    });
                                </script>
                            @endpush
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($data->Status === 'Ditolak')
        <div class="sticky-note" id="ccp-note"
            style="display:none; position: fixed; left: 30px; top: 90px; z-index: 10000; min-width: 500px; max-width: 500px; background: #fffecf; color: #856404; border: 1.5px solid #f7d358; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); padding: 22px 18px 18px 26px; font-family: 'Comic Sans MS', 'Comic Sans', cursive, sans-serif; font-size: 1.05em;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-weight: bold; color: #d9534f; font-size: 1.13em;">
                    🗒️ Catatan dari CCP
                </span>
                <button type="button" class="btn-close" aria-label="Close"
                    onclick="document.getElementById('ccp-note').style.display='none';"
                    style="margin-left: 12px; filter: brightness(0.7);"></button>
            </div>
            <div>
                {!! $data->Keterangan ?? '' !!}
            </div>
        </div>
    @endif
@endsection
@push('js')
    <script>
        document.getElementById('show-ccp-note').addEventListener('click', function() {
            document.getElementById('ccp-note').style.display = 'block';
        });
    </script>
    <script>
        $(document).ready(function() {
            var intervalLoading = null;
            var detik = 0;

            $('#formTanggalPresentasi').on('submit', function(e) {
                e.preventDefault(); // Prevent default submit

                var form = this;
                var tanggal = $('#TanggalPresentasi').val();

                // Validasi tanggal tidak boleh kosong
                if (!tanggal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Tanggal presentasi harus diisi!',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }

                // Format tanggal untuk ditampilkan (Indonesia)
                var tanggalFormatted = new Date(tanggal).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Tampilkan SweetAlert2 Konfirmasi
                Swal.fire({
                    title: 'Konfirmasi Presentasi',
                    text: `Apakah Anda yakin ingin melakukan presentasi pada ${tanggalFormatted}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-paper-plane"></i> Ya, Simpan dan Kirim!',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        popup: 'swal-wide'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        detik = 0;
                        Swal.fire({
                            title: 'Sedang Mengirim Email',
                            html: `
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="mb-3">Mohon tunggu sebentar...</h5>
                            <div class="alert alert-info" style="font-size: 14px;">
                                <i class="fa fa-info-circle"></i>
                                <strong>Jangan refresh atau tutup halaman ini!</strong>
                            </div>
                            <p class="mt-3 mb-0">
                                Email sedang dikirim ke <strong>Direktur Rumah Sakit terkait</strong>
                            </p>
                            <p class="mt-2 mb-0">
                                Waktu berlalu: <span id="detik-timer-presentasi" style="font-weight: bold; color: #0d6efd; font-size: 20px;">0</span> detik
                            </p>
                        </div>
                    `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                if (intervalLoading) clearInterval(intervalLoading);

                                intervalLoading = setInterval(function() {
                                    detik++;
                                    $('#detik-timer-presentasi').text(detik);
                                }, 1000);
                                form.submit();
                            }
                        });
                    }

                });
            });
            $(window).on('beforeunload', function() {
                if (intervalLoading) {
                    clearInterval(intervalLoading);
                }
            });
        });
    </script>
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
                    icon: 'error',
                    title: 'Gagal!',
                    html: `{!! Session::get('error') !!}`,
                    iconColor: '#d33',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#d33',
                });
            }, 500);
        </script>
    @endif
    @if (Session::get('warning'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: '{{ Session::get('warning') }}',
                    iconColor: '#ffc107',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#ffc107',
                });
            }, 500);
        </script>
    @endif
@endpush
