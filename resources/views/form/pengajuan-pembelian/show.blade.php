@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Pengajuan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ajukan.index') }}">Pengajuan Pembelian</a></li>
                    <li class="breadcrumb-item active">Detail Pengajuan Pembelian</li>
                </ul>
            </div>

        </div>
        <div class="col text-end">
            <a href="{{ route('ajukan.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-s2">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Detail Pengajuan Pembelian / Status Saat ini - {{ $data->Status }}</h4>
                    @if ($data->Status === 'Ditolak')
                        <button type="button" class="btn btn-danger" id="show-ccp-note">
                            <i class="fa fa-sticky-note"></i> Lihat Catatan dari CCP
                        </button>
                    @endif

                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Tanggal</strong></label>
                            <input type="text" class="form-control"
                                value="{{ isset($data->Tanggal) ? $data->Tanggal : '-' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Permintaan dari Departemen</strong></label>
                            <input type="text" class="form-control"
                                value="{{ isset($data->getDepartemen->Nama) ? $data->getDepartemen->Nama : '-' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Jenis</strong></label>
                            <input type="text" class="form-control" value="{{ $data->getJenisPermintaan->Nama ?? '-' }}"
                                readonly>
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
                    {{-- PERBANDINGAN VENDOR --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                Perbandingan Vendor
                            </div>
                            @if (empty($data->Status) || $data->Status === 'Draft' || $data->Status === 'Ditolak')
                                <a href="{{ route('ajukan.edit', encrypt($data->id)) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Ubah Data Perbandingan Vendor
                                </a>
                            @else
                                <span class="badge bg-success">Sudah diajukan</span>
                            @endif

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
                                                                <table class="table table-bordered mb-0"
                                                                    style="width:100%; table-layout: fixed;">
                                                                    <colgroup>
                                                                        <col style="width: 30%;">
                                                                        <col style="width: 70%;">
                                                                    </colgroup>
                                                                    <tr>
                                                                        <th><strong>Nama PIC</strong></th>
                                                                        <td>
                                                                            <span class="form-control-plaintext">
                                                                                <strong>
                                                                                    {{ isset($vendorData->NamaPic)
                                                                                        ? $vendorData->NamaPic
                                                                                        : (isset($permintaan->getPengajuanPembelian->getVendor[$vnIdx]->NamaPic)
                                                                                            ? $permintaan->getPengajuanPembelian->getVendor[$vnIdx]->NamaPic
                                                                                            : '-') }}
                                                                                </strong>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th><strong>No HP PIC</strong></th>
                                                                        <td>
                                                                            @php
                                                                                $noHpPic = isset($vendorData->KontakPic)
                                                                                    ? $vendorData->KontakPic
                                                                                    : (isset(
                                                                                        $permintaan
                                                                                            ->getPengajuanPembelian
                                                                                            ->getVendor[$vnIdx]
                                                                                            ->KontakPic,
                                                                                    )
                                                                                        ? $permintaan
                                                                                            ->getPengajuanPembelian
                                                                                            ->getVendor[$vnIdx]
                                                                                            ->KontakPic
                                                                                        : '-');
                                                                                $noWaLinkRaw = preg_replace(
                                                                                    '/[^0-9+]/',
                                                                                    '',
                                                                                    $noHpPic,
                                                                                );
                                                                                $noWaLink = $noWaLinkRaw;
                                                                                if (
                                                                                    preg_match('/^0\d+$/', $noWaLinkRaw)
                                                                                ) {
                                                                                    $noWaLink =
                                                                                        '62' . substr($noWaLinkRaw, 1);
                                                                                } elseif (
                                                                                    preg_match(
                                                                                        '/^\+62\d+$/',
                                                                                        $noWaLinkRaw,
                                                                                    )
                                                                                ) {
                                                                                    $noWaLink = preg_replace(
                                                                                        '/^\+/',
                                                                                        '',
                                                                                        $noWaLinkRaw,
                                                                                    );
                                                                                }
                                                                            @endphp
                                                                            <span class="fw-bold">
                                                                                <strong>
                                                                                    @if ($noHpPic != '-')
                                                                                        <a href="https://wa.me/{{ $noWaLink }}"
                                                                                            target="_blank"
                                                                                            rel="noopener noreferrer"
                                                                                            style="color: #007bff; text-decoration: none;">
                                                                                            {{ $noHpPic }}
                                                                                            <i
                                                                                                class="fab fa-whatsapp text-success"></i>
                                                                                        </a>
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </strong>
                                                                            </span>
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
                                                                    <span>{{ $barangMaster ? $barangMaster->Nama : '-' }} / {{ $barangMaster ? $barangMaster->Tipe : '-' }}</span>

                                                                </td>

                                                                <td>
                                                                    <span>
                                                                        {{ optional($barangMaster?->getMerk)->Nama ?? '-' }} /

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
                                                                        {{ $diskon !== null ? ($jenisDiskon === 'Rp' ? number_format($diskon, 0, ',', '.') : $diskon) : '-' }}
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
                    {{-- <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Daftar Item yang Diajukan</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:40px;">No</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center">HTA / GPA</th>
                                            <th class="text-center">FUI</th>
                                            <th class="text-center">Rekomendasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td>
                                                        {{ $item->getBarang->Nama ?? '-' }}
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $hasHta = $item->getHtaGpa ? true : false;
                                                        @endphp
                                                        @if (!$hasHta)
                                                            <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                                class="btn btn-warning">
                                                                <i class="fa fa-exclamation-circle"></i>
                                                                Lengkapi HTA
                                                            </a>
                                                        @else
                                                            <a href="{{ route('htagpa.show', [$data->id, $item->id]) }}"
                                                                class="btn btn-success">
                                                                <i class="fa fa-check-circle"></i>
                                                                Lihat HTA
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $item->Satuan ?? '-' }}</td>
                                                    <td class="text-center">{{ $item->Satuan ?? '-' }}</td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data item.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> --}}
                    @php
                        $today = \Carbon\Carbon::now();
                        $disableAjukan = false;
                        $disableAjukanPresentasi = false;
                        $alasanTidakBisaAjukan = '';
                        $alasanTidakBisaAjukanPresentasi = '';

                        if ($tutup && $tutup->isAktif == 'Y') {
                            $mulai = \Carbon\Carbon::parse($tutup->TanggalMulai);
                            $selesai = \Carbon\Carbon::parse($tutup->TanggalSelesai);
                            if ($today->between($mulai, $selesai)) {
                                $disableAjukan = true;
                                $alasanTidakBisaAjukan = 'Pengajuan sedang ditutup pada periode ini.';
                                $disableAjukanPresentasi = true;
                                $alasanTidakBisaAjukanPresentasi =
                                    'Pengajuan Presentasi sedang ditutup pada periode ini.';
                            }
                        }

                        // Data mapping hari
                        $daftarHari = [
                            'Monday' => 'Senin',
                            'Tuesday' => 'Selasa',
                            'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis',
                            'Friday' => 'Jumat',
                            'Saturday' => 'Sabtu',
                            'Sunday' => 'Minggu',
                        ];
                        $namaHariIni = $daftarHari[$today->format('l')] ?? $today->format('l');

                        // --- Cek hari & jam operasional pengajuan ---
                        if (!$disableAjukan && isset($hariBuka) && count($hariBuka) > 0) {
                            $aturanHariIni = null;
                            // Cari aturan hari yang sesuai
                            foreach ($hariBuka as $aturan) {
                                if (
                                    (isset($aturan->Hari) && $aturan->Hari == $namaHariIni) ||
                                    (isset($aturan->NamaHari) && $aturan->NamaHari == $namaHariIni)
                                ) {
                                    $aturanHariIni = $aturan;
                                    break;
                                }
                            }
                            // Cek izin atau tutup berdasarkan isAktif
                            if ($aturanHariIni) {
                                if (isset($aturanHariIni->isAktif) && $aturanHariIni->isAktif == 'Y') {
                                    $jamMulai = !empty($aturanHariIni->JamMulai) ? $aturanHariIni->JamMulai : '00:00';
                                    $jamSelesai = !empty($aturanHariIni->JamSelesai)
                                        ? $aturanHariIni->JamSelesai
                                        : '23:59';
                                    $waktuSekarang = $today->format('H:i');
                                    if ($waktuSekarang < $jamMulai || $waktuSekarang > $jamSelesai) {
                                        $disableAjukan = true;
                                        $alasanTidakBisaAjukan =
                                            'Pengajuan tidak bisa dilakukan di luar jam operasional (' .
                                            $jamMulai .
                                            ' - ' .
                                            $jamSelesai .
                                            ') pada hari ini.';
                                    }
                                } else {
                                    $disableAjukan = true;
                                    $alasanTidakBisaAjukan = 'Pengajuan hari ini ditutup.';
                                }
                            } else {
                                // Tidak ada aturan hari ini
                                $disableAjukan = true;
                                $alasanTidakBisaAjukan =
                                    'Pengajuan tidak dapat dilakukan karena hari ini tidak termasuk hari operasional pengajuan.';
                            }
                        }

                        // --- Cek hari & jam operasional AJUKAN PRESENTASI ---
                        if (isset($hariBukaPresentasi) && count($hariBukaPresentasi) > 0) {
                            $aturanHariPresentasiIni = null;
                            foreach ($hariBukaPresentasi as $aturan) {
                                if (
                                    (isset($aturan->Hari) && $aturan->Hari == $namaHariIni) ||
                                    (isset($aturan->NamaHari) && $aturan->NamaHari == $namaHariIni)
                                ) {
                                    $aturanHariPresentasiIni = $aturan;
                                    break;
                                }
                            }
                            if ($aturanHariPresentasiIni) {
                                if (
                                    isset($aturanHariPresentasiIni->isAktif) &&
                                    $aturanHariPresentasiIni->isAktif == 'Y'
                                ) {
                                    $jamMulai = !empty($aturanHariPresentasiIni->JamMulai)
                                        ? $aturanHariPresentasiIni->JamMulai
                                        : '00:00';
                                    $jamSelesai = !empty($aturanHariPresentasiIni->JamSelesai)
                                        ? $aturanHariPresentasiIni->JamSelesai
                                        : '23:59';
                                    $waktuSekarang = $today->format('H:i');
                                    if ($waktuSekarang < $jamMulai || $waktuSekarang > $jamSelesai) {
                                        $disableAjukanPresentasi = true;
                                        $alasanTidakBisaAjukanPresentasi =
                                            'Permintaan Presentasi tidak bisa dilakukan di luar jam operasional (' .
                                            $jamMulai .
                                            ' - ' .
                                            $jamSelesai .
                                            ') pada hari ini.';
                                    }
                                } else {
                                    $disableAjukanPresentasi = true;
                                    $alasanTidakBisaAjukanPresentasi = 'Permintaan Presentasi hari ini ditutup.';
                                }
                            } else {
                                $disableAjukanPresentasi = true;
                                $alasanTidakBisaAjukanPresentasi =
                                    'Permintaan Presentasi tidak dapat dilakukan karena hari ini tidak termasuk hari operasional Presentasi.';
                            }
                        }
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Daftar Item yang Diajukan</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:40px;">No</th>
                                            <th>Nama Barang</th>
                                            <th class="text-center">Rekomendasi</th>
                                            <th class="text-center">HTA / GPA</th>
                                            <th class="text-center">Feasibility Study</th>
                                            <th class="text-center">Usulan Investasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td>
                                                        {{ $item->getBarang->Nama ?? '-' }}
                                                    </td>
                                                    <!-- Rekomendasi -->
                                                    <td class="text-center">

                                                        @php
                                                            $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                        @endphp
                                                        @if ($adaRekomendasi)
                                                            <a href="{{ route('rekomendasi.detail-print', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                class="btn btn-info ms-2" target="_blank">
                                                                <i class="fa fa-print"></i> Cetak
                                                            </a>
                                                            <a href="{{ route('rekomendasi.rekap', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                class="btn btn-warning ms-2" target="_blank">
                                                                <i class="fa fa-file-alt"></i> Rekap
                                                            </a>
                                                            @can('rekomendasi-show')
                                                                <a href="{{ route('rekomendasi.detail-view', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                    class="btn btn-secondary ms-2" target="_blank">
                                                                    <i class="fa fa-eye"></i> Lihat
                                                                </a>
                                                            @endcan
                                                        @else
                                                            @if ($data->Status == 'Draft')
                                                                <span
                                                                    style="color: #721c24; background: #f8d7da; padding: 6px 12px; border-radius: 5px; display: inline-block; font-weight: bold;">
                                                                    Rekomendasi belum dapat dilihat sebelum pengajuan
                                                                    diajukan ke CCP.
                                                                </span>
                                                            @else
                                                                <span
                                                                    style="color: #856404; background: #fff3cd; padding: 6px 12px; border-radius: 5px; display: inline-block;">
                                                                    Rekomendasi sedang diproses oleh CCP.
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <!-- HTA / GPA -->
                                                    <td class="text-center">
                                                        @php
                                                            $hasHta = $item->getHtaGpa ? true : false;
                                                        @endphp
                                                        @if (!$hasHta)
                                                            <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                                class="btn btn-warning">
                                                                <i class="fa fa-exclamation-circle"></i>
                                                                Lengkapi HTA
                                                            </a>
                                                        @else
                                                            @if ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak')
                                                                <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                                    class="btn btn-warning">
                                                                    <i class="fa fa-exclamation-circle"></i>
                                                                    Ubah HTA
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('htagpa.show', [$data->id, $item->id]) }}"
                                                                class="btn btn-success">
                                                                <i class="fa fa-check-circle"></i>
                                                                Lihat
                                                            </a>
                                                            <a href="{{ route('htagpa.print', [$data->id, $item->id]) }}"
                                                                class="btn btn-info" target="_blank">
                                                                <i class="fa fa-print"></i>
                                                                Cetak
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <!-- Feasibility Study -->
                                                    <td class="text-center">
                                                        @php
                                                            $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                            $adaFs = $item->getFs ? true : false;
                                                        @endphp
                                                        @if (!$adaRekomendasi)
                                                            <div class="alert alert-danger p-2 m-0"
                                                                style="font-size: 90%;">
                                                                Form Fisibility Study Akan Dibuat Oleh SMI setelah
                                                                Rekomendasi Dikeluarkan
                                                            </div>
                                                        @else
                                                            @if ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak')
                                                                @if ($adaFs)
                                                                    <a href="{{ route('fs.edit', [$data->id, $item->id]) }}"
                                                                        class="btn btn-primary">
                                                                        <i class="fa fa-edit"></i>
                                                                        Ubah
                                                                    </a>
                                                                    <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                        class="btn btn-success">
                                                                        <i class="fa fa-eye"></i>
                                                                        Lihat
                                                                    </a>
                                                                    <a href="{{ route('fs.cetak', [$data->id, $item->id]) }}"
                                                                        class="btn btn-info" target="_blank">
                                                                        <i class="fa fa-print"></i>
                                                                        Cetak
                                                                    </a>
                                                                @else
                                                                    @role(['Keuangan', 'Admin'])
                                                                        <a href="{{ route('fs.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                            class="btn btn-primary">
                                                                            <i class="fa fa-edit"></i>
                                                                            Lengkapi
                                                                        </a>
                                                                    @else
                                                                        <div class="alert alert-danger mt-1" role="alert">
                                                                            FS, Dibuat oleh Keuangan atau Admin
                                                                        </div>
                                                                    @endrole
                                                                @endif
                                                            @else
                                                                @if ($adaFs)
                                                                    <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                        class="btn btn-success">
                                                                        <i class="fa fa-eye"></i>
                                                                        Lihat
                                                                    </a>
                                                                    <a href="{{ route('fs.cetak', [$data->id, $item->id]) }}"
                                                                        class="btn btn-info" target="_blank">
                                                                        <i class="fa fa-print"></i>
                                                                        Cetak
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <!-- Usulan Investasi -->
                                                    <td class="text-center">
                                                        @php
                                                            $adaFui = $item->getFui ? true : false;
                                                            $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                        @endphp
                                                        @if ($adaRekomendasi)
                                                            @if (!$adaFui)
                                                                @if ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak')
                                                                    <a href="{{ route('usulan-investasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-warning">
                                                                        <i class="fa fa-lightbulb"></i> Lengkapi
                                                                    </a>
                                                                @endif
                                                            @else
                                                                @if (optional($item->getFui)->SudahRkap2 === null &&
                                                                        ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak'))
                                                                    <a href="{{ route('usulan-investasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-warning">
                                                                        <i class="fa fa-edit"></i> Lengkapi
                                                                    </a>
                                                                @endif
                                                                <a href="{{ route('usulan-investasi.show', [$data->id, $item->id]) }}"
                                                                    class="btn btn-success">
                                                                    <i class="fa fa-eye"></i>
                                                                    Lihat
                                                                </a>
                                                                <a href="{{ route('usulan-investasi.print', [$data->id, $item->id]) }}"
                                                                    class="btn btn-info" target="_blank">
                                                                    <i class="fa fa-print"></i>
                                                                    Cetak
                                                                </a>
                                                            @endif
                                                        @else
                                                            <div class="alert alert-danger mt-1" role="alert">
                                                                Mohon maaf, Formulir Usulan Investasi dapat diisi<br>
                                                                setelah rekomendasi dikeluarkan oleh CCP.
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">Data item belum tersedia.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                    {{-- <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Detail Pengajuan
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs tab-style-2 nav-justified mb-3 d-sm-flex d-block" id="myTab1"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="rekomendasi-tab" data-bs-toggle="tab"
                                        data-bs-target="#rekomendasi-tab-pane" type="button" role="tab"
                                        aria-controls="rekomendasi-tab-pane" aria-selected="true">
                                        <i class="fa fa-thumbs-up me-1 align-middle"></i>Rekomendasi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="hta-gpa-tab" data-bs-toggle="tab"
                                        data-bs-target="#hta-gpa-tab-pane" type="button" role="tab"
                                        aria-controls="hta-gpa-tab-pane" aria-selected="false">
                                        <i class="fa fa-clipboard-list me-1 align-middle"></i>HTA / GPA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="usulan-investasi-tab" data-bs-toggle="tab"
                                        data-bs-target="#usulan-investasi-tab-pane" type="button" role="tab"
                                        aria-controls="usulan-investasi-tab-pane" aria-selected="false" tabindex="-1"
                                        aria-disabled="true">
                                        <i class="fa fa-file-alt me-1 align-middle"></i>Form Usulan Investasi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="lembar-disposisi-tab" data-bs-toggle="tab"
                                        data-bs-target="#lembar-disposisi-tab-pane" type="button" role="tab"
                                        aria-controls="lembar-disposisi-tab-pane" aria-selected="false">
                                        <i class="fa fa-layer-group me-1 align-middle"></i>Lembar Disposisi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="feasibility-study-tab" data-bs-toggle="tab"
                                        data-bs-target="#feasibility-study-tab-pane" type="button" role="tab"
                                        aria-controls="feasibility-study-tab-pane" aria-selected="false">
                                        <i class="fa fa-search-dollar me-1 align-middle"></i>Feasibility Study
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <!-- Tab Rekomendasi -->
                                <div class="tab-pane fade show active text-muted" id="rekomendasi-tab-pane"
                                    role="tabpanel" aria-labelledby="rekomendasi-tab" tabindex="0">
                                    @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                        <div class="row g-3">
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <div class="col-md-4">
                                                    <div class="card mb-3">
                                                        <div class="card-body d-flex align-items-center">
                                                            <div class="w-100 d-flex justify-content-center align-items-center"
                                                                style="">

                                                                @php
                                                                    $adaRekomendasi = $item->getRekomendasi
                                                                        ? true
                                                                        : false;
                                                                @endphp
                                                                @if ($adaRekomendasi)
                                                                    <a href="{{ route('rekomendasi.detail-print', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-info me-2 mb-2" target="_blank">
                                                                        <i class="fa fa-print"></i> Cetak
                                                                    </a>
                                                                    <a href="{{ route('rekomendasi.rekap', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-warning me-2 mb-2" target="_blank">
                                                                        <i class="fa fa-file-alt"></i> Rekap
                                                                    </a>
                                                                    @can('rekomendasi-show')
                                                                        <a href="{{ route('rekomendasi.detail-view', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                            class="btn btn-secondary me-2 mb-2"
                                                                            target="_blank">
                                                                            <i class="fa fa-eye"></i> Lihat
                                                                        </a>
                                                                    @endcan
                                                                @else
                                                                    @if ($data->Status == 'Draft')
                                                                        <span
                                                                            style="color: #721c24; background: #f8d7da; padding: 6px 12px; border-radius: 5px; display: inline-block; font-weight: bold;">
                                                                            Rekomendasi belum dapat dilihat sebelum
                                                                            pengajuan diajukan ke CCP.
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            style="color: #856404; background: #fff3cd; padding: 6px 12px; border-radius: 5px; display: inline-block;">
                                                                            Rekomendasi sedang diproses oleh CCP.
                                                                        </span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <!-- Kolom kanan dikosongkan sesuai instruksi -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <!-- Tab HTA/GPA -->
                                <div class="tab-pane fade text-muted" id="hta-gpa-tab-pane" role="tabpanel"
                                    aria-labelledby="hta-gpa-tab" tabindex="0">
                                    @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                        <div class="row g-3">
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @php
                                                                $hasHta = $item->getHtaGpa ? true : false;
                                                            @endphp
                                                            @if (!$hasHta)
                                                                <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                                    class="btn btn-warning mb-2">
                                                                    <i class="fa fa-exclamation-circle"></i>
                                                                    Lengkapi HTA
                                                                </a>
                                                            @else
                                                                @if ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak')
                                                                    <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                                        class="btn btn-warning mb-2">
                                                                        <i class="fa fa-exclamation-circle"></i>
                                                                        Ubah HTA
                                                                    </a>
                                                                @endif
                                                                <a href="{{ route('htagpa.show', [$data->id, $item->id]) }}"
                                                                    class="btn btn-success mb-2">
                                                                    <i class="fa fa-check-circle"></i>
                                                                    Lihat
                                                                </a>
                                                                <a href="{{ route('htagpa.print', [$data->id, $item->id]) }}"
                                                                    class="btn btn-info mb-2" target="_blank">
                                                                    <i class="fa fa-print"></i>
                                                                    Cetak
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <!-- Kolom kanan dikosongkan sesuai instruksi -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="ps-3 mb-0">
                                            <li>Data item belum tersedia.</li>
                                        </ul>
                                    @endif
                                </div>
                                <!-- Tab Usulan Investasi -->
                                <div class="tab-pane fade text-muted" id="usulan-investasi-tab-pane" role="tabpanel"
                                    aria-labelledby="usulan-investasi-tab" tabindex="0">

                                    @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                        <div class="row g-3">
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @php
                                                                $adaFui = $item->getFui ? true : false;
                                                                $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                            @endphp
                                                            @if ($adaRekomendasi)
                                                                @if (!$adaFui)
                                                                    <a href="{{ route('usulan-investasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-warning mb-2">
                                                                        <i class="fa fa-lightbulb"></i> Lengkapi
                                                                    </a>
                                                                @else
                                                                    @if (optional($item->getFui)->SudahRkap2 === null)
                                                                        <a href="{{ route('usulan-investasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                            class="btn btn-warning mb-2">
                                                                            <i class="fa fa-edit"></i> Lengkapi
                                                                        </a>
                                                                    @endif
                                                                    <a href="{{ route('usulan-investasi.show', [$data->id, $item->id]) }}"
                                                                        class="btn btn-success mb-2">
                                                                        <i class="fa fa-eye"></i>
                                                                        Lihat
                                                                    </a>
                                                                    <a href="{{ route('usulan-investasi.print', [$data->id, $item->id]) }}"
                                                                        class="btn btn-info mb-2" target="_blank">
                                                                        <i class="fa fa-print"></i>
                                                                        Cetak
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <div class="alert alert-danger mt-1 mb-0" role="alert">
                                                                    Mohon maaf, Formulir Usulan Investasi dapat diisi<br>
                                                                    setelah rekomendasi dikeluarkan oleh CCP.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <!-- Kolom kanan dikosongkan sesuai instruksi -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="ps-3 mb-0">
                                            <li>Data item belum tersedia.</li>
                                        </ul>
                                    @endif
                                </div>
                                <!-- Tab Lembar Disposisi -->
                                <div class="tab-pane fade text-muted" id="lembar-disposisi-tab-pane" role="tabpanel"
                                    aria-labelledby="lembar-disposisi-tab" tabindex="0">
                                    @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                        <div class="row g-3">
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @php
                                                                $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                                $adaLembarDisposisi = $item->getDisposisi
                                                                    ? true
                                                                    : false;
                                                            @endphp
                                                            @if (!$adaRekomendasi)
                                                                <div class="alert alert-danger p-2 m-0"
                                                                    style="font-size: 90%;">
                                                                    Lembar Disposisi dapat dibuat setelah rekomendasi
                                                                    dikeluarkan oleh CCP.
                                                                </div>
                                                            @else
                                                                @if ($adaLembarDisposisi)
                                                                    <a href="{{ route('lembar-disposisi.edit', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-primary mb-2">
                                                                        <i class="fa fa-edit"></i>
                                                                        Ubah
                                                                    </a>
                                                                    <a href="{{ route('lembar-disposisi.print', [$data->id, $item->id]) }}"
                                                                        class="btn btn-info mb-2" target="_blank">
                                                                        <i class="fa fa-print"></i> Cetak
                                                                    </a>
                                                                    <a href="{{ route('lembar-disposisi.show', [$data->id, $item->id]) }}"
                                                                        class="btn btn-success mb-2">
                                                                        <i class="fa fa-eye"></i>
                                                                        Lihat
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('lembar-disposisi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                        class="btn btn-primary mb-2">
                                                                        <i class="fa fa-edit"></i>
                                                                        Isi Lembar Disposisi
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <!-- Kolom kanan dikosongkan sesuai instruksi -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="ps-3 mb-0">
                                            <li>Data item belum tersedia.</li>
                                        </ul>
                                    @endif
                                </div>
                                <!-- Tab Feasibility Study -->
                                <div class="tab-pane fade text-muted" id="feasibility-study-tab-pane" role="tabpanel"
                                    aria-labelledby="feasibility-study-tab" tabindex="0">
                                    @if ($data->getPengajuanItem && count($data->getPengajuanItem))
                                        <div class="row g-3">
                                            @foreach ($data->getPengajuanItem as $i => $item)
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @php
                                                                $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                                $adaFs = $item->getFs ? true : false;
                                                            @endphp
                                                            @if (!$adaRekomendasi)
                                                                <div class="alert alert-danger p-2 m-0"
                                                                    style="font-size: 90%;">
                                                                    Form Fisibility Study Akan Dibuat Oleh SMI setelah
                                                                    Rekomendasi Dikeluarkan
                                                                </div>
                                                            @else
                                                                @if ($adaFs)
                                                                    <a href="{{ route('fs.edit', [$data->id, $item->id]) }}"
                                                                        class="btn btn-primary mb-2">
                                                                        <i class="fa fa-edit"></i>
                                                                        Ubah
                                                                    </a>
                                                                    <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                        class="btn btn-success mb-2">
                                                                        <i class="fa fa-eye"></i>
                                                                        Lihat
                                                                    </a>
                                                                    <a href="{{ route('fs.cetak', [$data->id, $item->id]) }}"
                                                                        class="btn btn-info mb-2" target="_blank">
                                                                        <i class="fa fa-print"></i>
                                                                        Cetak
                                                                    </a>
                                                                @else
                                                                    @role('Keuangan')
                                                                        <a href="{{ route('fs.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                            class="btn btn-primary mb-2">
                                                                            <i class="fa fa-edit"></i>
                                                                            Lengkapi
                                                                        </a>
                                                                    @else
                                                                        <div class="alert alert-danger mt-1 mb-0"
                                                                            role="alert">
                                                                            FS, Di buat oleh Keuangan
                                                                        </div>
                                                                    @endrole
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <!-- Kolom kanan dikosongkan sesuai instruksi -->
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="ps-3 mb-0">
                                            <li>Data item belum tersedia.</li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div> --}}

                    <div class="co2 text-end mt-3">
                        <a href="{{ route('ajukan.index') }}" class="btn btn-secondary me-2">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>

                        @if ($data->Status == 'Diajukan' || $data->Status == 'Selesai')
                            <button type="button" class="btn btn-danger" id="btn-batalkan">
                                <i class="fa fa-times"></i> Batalkan Pengajuan
                            </button>
                            <form id="form-batalkan" action="{{ route('ajukan.update-status', $data->id) }}"
                                method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="Status" value="Draft">
                            </form>
                        @elseif($data->Status == 'Draft' || $data->Status == 'Ditolak')
                            <button type="button" class="btn btn-success" id="btn-ajukan"
                                {{ $disableAjukan ? 'disabled' : '' }}>
                                <i class="fa fa-paper-plane"></i> Ajukan Ke CCP
                            </button>
                            @if ($disableAjukan)
                                <div class="alert alert-danger mt-2">
                                    {{ $alasanTidakBisaAjukan }}
                                    @if (isset($tutup) && $tutup->isAktif == 'Y' && !empty($tutup->Keterangan))
                                        <br>
                                        <strong>Keterangan:</strong>
                                        {{ $tutup->Keterangan }}
                                    @endif
                                    @if (isset($tutup->TanggalMulai) && isset($tutup->TanggalSelesai) && $tutup->isAktif == 'Y')
                                        <br>
                                        <small>
                                            ({{ \Carbon\Carbon::parse($tutup->TanggalMulai)->format('d M Y') }}
                                            &ndash;
                                            {{ \Carbon\Carbon::parse($tutup->TanggalSelesai)->format('d M Y') }})
                                        </small>
                                    @endif
                                </div>
                            @endif
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

                        @if ($data->Status == 'Selesai Review' && !auth()->user()->hasRole('Group Head'))
                            {{-- <a href="{{ route('ajukan.minta-ttd-dir-group', encrypt($data->id)) }}"
                                class="btn btn-primary">
                                <span class="me-1"><i class="fa fa-user-secret"></i></span>
                                Ajukan Permintaan Persetujuan Direktur Group
                            </a> --}}
                            <button type="button" class="btn btn-success" id="btn-selesaikan"
                                {{ $disableAjukanPresentasi ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> Selesaikan Pengajuan
                            </button>
                            @if ($disableAjukanPresentasi)
                                <div class="mt-2"
                                    style="border:2px solid #dc3545; background:#fff; color:#dc3545; border-radius: 0.375rem; padding: 1rem;">
                                    {{ $alasanTidakBisaAjukan }}
                                    @if (isset($hariBukaPresentasi) && count($hariBukaPresentasi) > 0)
                                        <br>
                                        <strong>Jadwal Pengajuan Presentasi :</strong>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($hariBukaPresentasi as $hb)
                                                <li class="mb-1">
                                                    <span class="fw-semibold">
                                                        {{ $hb->NamaHari ?? ($hb->Hari ?? '-') }}
                                                    </span>
                                                    :
                                                    @if (isset($hb->isAktif) && $hb->isAktif == 'Y')
                                                        <span>
                                                            {{ !empty($hb->JamMulai) ? \Carbon\Carbon::createFromFormat('H:i:s', $hb->JamMulai)->format('H:i') : '00:00' }}
                                                            -
                                                            {{ !empty($hb->JamSelesai) ? \Carbon\Carbon::createFromFormat('H:i:s', $hb->JamSelesai)->format('H:i') : '23:59' }}
                                                        </span>
                                                        <span class="badge bg-success ms-2">Buka</span>
                                                    @else
                                                        <span class="text-muted ms-2">Tutup</span>
                                                        <span class="badge bg-danger ms-2">Tutup</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                            @endif


                            {{-- <button type="button" class="btn btn-success" id="btn-selesaikan">
                                <i class="fa fa-check"></i> Selesaikan Pengajuan
                            </button> --}}
                            <form id="form-selesaikan" action="{{ route('ajukan.update-status', $data->id) }}"
                                method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="Status" value="Siap Presentasi">
                            </form>
                            @push('js')
                                <script>
                                    document.getElementById('btn-selesaikan').addEventListener('click', function(e) {
                                        e.preventDefault();
                                        Swal.fire({
                                            title: 'Permintaan Presentasi',
                                            text: 'Apakah Anda ingin meminta presentasi untuk pengajuan ini?',
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonColor: '#28a745',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, minta presentasi!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                document.getElementById('form-selesaikan').submit();
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

    <script>
        document.getElementById('btn-batalkan').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Pembatalan',
                text: 'Apakah Anda yakin ingin membatalkan pengajuan ini? Tindakan ini dapat dikembalikan ke draft.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-batalkan').submit();
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
@endpush
