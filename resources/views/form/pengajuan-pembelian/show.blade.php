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
                            <div>
                                @if (empty($data->Status) || $data->Status === 'Draft' || $data->Status === 'Ditolak')
                                    <a href="{{ route('ajukan.edit', encrypt($data->id)) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> Ubah Data Perbandingan Vendor
                                    </a>
                                @else
                                    <span class="badge bg-success">{{ $data->Status }}</span>
                                @endif

                                {{-- Cek ACC Direktur --}}
                                @if (($data->Jenis ?? null) != 1 && isset($data->AccDirektur) && $data->AccDirektur === 'N')
                                    @php
                                        $direkturId = $data->DirekturId ?? (isset($data->DirekturId) ? $data->DirekturId : null);
                                        $approvalLink = '-';
                                        if ($direkturId) {
                                            $approvalLink = route('usulan-investasi.approve-direktur', [$data->KodePengajuan, $direkturId]);
                                        }
                                    @endphp
                                    <button class="btn btn-outline-secondary btn-sm ms-2" type="button" onclick="copyApprovalLinkDirektur()">
                                        <i class="fa fa-copy"></i> Salin Link Approval Direktur
                                    </button>
                                    <input type="hidden" id="approval-link-direktur" value="{{ $approvalLink }}">
                                    @push('js')
                                        <script>
                                            function copyApprovalLinkDirektur() {
                                                var linkInput = document.getElementById('approval-link-direktur');
                                                var tempInput = document.createElement('input');
                                                tempInput.value = linkInput.value;
                                                document.body.appendChild(tempInput);
                                                tempInput.select();
                                                tempInput.setSelectionRange(0, 99999);
                                                document.execCommand('copy');
                                                document.body.removeChild(tempInput);

                                                // SweetAlert success
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Berhasil!',
                                                    text: 'Link berhasil disalin.',
                                                    showConfirmButton: false,
                                                    timer: 1500
                                                });
                                            }
                                        </script>
                                    @endpush
                                @endif

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
                                        $vendorData = $vendorList[$vnIdx] ?? null;
                                        $selectedVendor = null;
                                        if ($vendorData && isset($vendorData->NamaVendor)) {
                                            $selectedVendor = $vendor->firstWhere('id', $vendorData->NamaVendor);
                                        }

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
                                                $jenisDiskon = $barang->JenisDiskon ?? null;

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
                                        $ppn = isset($vendorData->Ppn) ? floatval($vendorData->Ppn) : 0;
                                        $totalPpn = $ppn ? ($totalHargaSetelahDiskonAll * $ppn) / 100 : 0;
                                        $grandTotal = $totalHargaSetelahDiskonAll + $totalPpn;
                                    @endphp
                                    <div class="tab-pane{{ $vnIdx == 0 ? ' active' : '' }}"
                                        id="vendor_tab_{{ $vnIdx }}" role="tabpanel">

                                        {{-- TOMBOL HAPUS VENDOR (Hanya muncul jika status Draft / Ditolak / Kosong) --}}
                                        @if(
                                            (empty($data->Status) || $data->Status === 'Draft' || $data->Status === 'Ditolak')
                                            && isset($vendorList) && count($vendorList) > 1
                                        )
                                            <div class="d-flex justify-content-end mb-3">
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus-vendor" data-vnidx="{{ $vnIdx }}">
                                                    <i class="fa fa-trash-alt"></i> Hapus Vendor {{ $vnIdx + 1 }}
                                                </button>
                                                <form id="form-hapus-vendor-{{ $vnIdx }}" action="{{ route('ajukan.vendor.destroy', [$data->id, $vendorData->id ?? 0]) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        @endif
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
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    {{-- END PERBANDINGAN VENDOR --}}

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

                        if (!$disableAjukan && isset($hariBuka) && count($hariBuka) > 0) {
                            $aturanHariIni = null;
                            foreach ($hariBuka as $aturan) {
                                if (
                                    (isset($aturan->Hari) && $aturan->Hari == $namaHariIni) ||
                                    (isset($aturan->NamaHari) && $aturan->NamaHari == $namaHariIni)
                                ) {
                                    $aturanHariIni = $aturan;
                                    break;
                                }
                            }
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
                                $disableAjukan = true;
                                $alasanTidakBisaAjukan =
                                    'Pengajuan tidak dapat dilakukan karena hari ini tidak termasuk hari operasional pengajuan.';
                            }
                        }

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
    {{-- ============================================================ --}}
{{-- DAFTAR ITEM YANG DIAJUKAN - REDESIGN CARD                    --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="card-title mb-0">
            <i class="fa fa-list-check me-2"></i>Daftar Item yang Diajukan
        </h4>
        @if ($data->getPengajuanItem && count($data->getPengajuanItem))
            @php
                $isFsRequired = ($data->Jenis ?? null) == 1;
                $jmlDokumenPerItem = $isFsRequired ? 4 : 3;

                // Hitung total kelengkapan semua item
                $totalLengkapSemua = 0;
                foreach ($data->getPengajuanItem as $it) {
                    $totalLengkapSemua += ($it->getRekomendasi ? 1 : 0)
                                        + ($it->getHtaGpa ? 1 : 0)
                                        + (($isFsRequired && $it->getFs) ? 1 : 0)
                                        + ($it->getFui ? 1 : 0);
                }
                $totalDokumenSemua = count($data->getPengajuanItem) * $jmlDokumenPerItem;
                $progressSemua = $totalDokumenSemua > 0 ? round(($totalLengkapSemua / $totalDokumenSemua) * 100) : 0;
            @endphp
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary fs-6">{{ count($data->getPengajuanItem) }} Item</span>
                <span class="badge bg-{{ $progressSemua >= 75 ? 'success' : ($progressSemua >= 50 ? 'warning' : 'danger') }} fs-6">
                    Kelengkapan: {{ $progressSemua }}%
                </span>
            </div>
        @endif
    </div>
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
                        $hasHta         = $item->getHtaGpa ? true : false;
                        $adaFs          = $item->getFs ? true : false;
                        $adaFui         = $item->getFui ? true : false;

                        $totalDokumen   = $jmlDokumenPerItem;
                        $lengkapCount   = ($adaRekomendasi ? 1 : 0)
                                        + ($hasHta ? 1 : 0)
                                        + (($isFsRequired && $adaFs) ? 1 : 0)
                                        + ($adaFui ? 1 : 0);
                        $progressPercent = ($lengkapCount / $totalDokumen) * 100;

                        $progressColor = 'danger';
                        if ($progressPercent >= 75) $progressColor = 'success';
                        elseif ($progressPercent >= 50) $progressColor = 'warning';
                        elseif ($progressPercent > 0) $progressColor = 'info';

                        // HTA final check
                        $htaFinal = $hasHta && isset($item->getHtaGpa->Status) && strtolower($item->getHtaGpa->Status) == 'final';

                        // === Timestamp terakhir update ===
                        $rekomendasiUpdate = $adaRekomendasi && isset($item->getRekomendasi->updated_at)
                            ? \Carbon\Carbon::parse($item->getRekomendasi->updated_at)->translatedFormat('d F Y H:i')
                            : null;

                        $htaUpdate = $hasHta && isset($item->getHtaGpa->updated_at)
                            ? \Carbon\Carbon::parse($item->getHtaGpa->updated_at)->translatedFormat('d F Y H:i')
                            : null;

                        $fsUpdate = $adaFs && isset($item->getFs->updated_at)
                            ? \Carbon\Carbon::parse($item->getFs->updated_at)->translatedFormat('d F Y H:i')
                            : null;

                        $fuiUpdate = $adaFui && isset($item->getFui->updated_at)
                            ? \Carbon\Carbon::parse($item->getFui->updated_at)->translatedFormat('d F Y H:i')
                            : null;
                    @endphp

                    <div class="col-12 mb-4">
                        <div class="card border shadow-sm h-100">

                            {{-- ===== CARD HEADER ITEM ===== --}}
                            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                             style="width:42px;height:42px;font-size:1.1rem;">
                                            {{ $i + 1 }}
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $item->getBarang->Nama ?? 'Item Tanpa Nama' }}</h5>
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
                                    <div class="progress-bar bg-{{ $progressColor }}" role="progressbar"
                                         style="width: {{ $progressPercent }}%;"
                                         aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            {{-- ===== CARD BODY - GRID DOKUMEN ===== --}}
                            <div class="card-body">
                                <div class="row g-3">

                                    {{-- ─────────── 1. REKOMENDASI ─────────── --}}
                                    <div class="{{ $colDoc }}">
                                        <div class="card h-100 {{ $adaRekomendasi ? 'border-success' : 'border-warning' }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-semibold">
                                                        <i class="fa fa-file-signature me-1 text-primary"></i> Rekomendasi
                                                    </h6>
                                                    @if ($adaRekomendasi)
                                                        <span class="badge bg-success">Lengkap</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Proses</span>
                                                    @endif
                                                </div>

                                                @if ($adaRekomendasi)
                                                    <div class="d-flex flex-column gap-1">
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ route('rekomendasi.detail-print', [encrypt($data->id), encrypt($item->id)]) }}"
                                                               class="btn btn-info btn-sm flex-fill" target="_blank">
                                                                <i class="fa fa-print"></i> Cetak
                                                            </a>
                                                            <a href="{{ route('rekomendasi.rekap', [encrypt($data->id), encrypt($item->id)]) }}"
                                                               class="btn btn-warning btn-sm flex-fill" target="_blank">
                                                                <i class="fa fa-file-alt"></i> Rekap
                                                            </a>
                                                        </div>
                                                        {{-- @can('rekomendasi-show')
                                                            <a href="{{ route('rekomendasi.detail-view', [encrypt($data->id), encrypt($item->id)]) }}"
                                                               class="btn btn-secondary btn-sm w-100" target="_blank">
                                                                <i class="fa fa-eye"></i> Lihat
                                                            </a>
                                                        @endcan --}}

                                                        @if ($rekomendasiUpdate)
                                                            <div class="mt-2 small text-secondary">
                                                                <i class="fa fa-clock me-1"></i>
                                                                Diperbarui: {{ $rekomendasiUpdate }} WIB
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    @if ($data->Status == 'Draft')
                                                        <div class="alert alert-danger p-2 mb-0 small">
                                                            Tersedia setelah diajukan ke CCP.
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning p-2 mb-0 small">
                                                            Sedang diproses CCP.
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ─────────── 2. HTA / GPA ─────────── --}}
                                    <div class="{{ $colDoc }}">
                                        <div class="card h-100 {{ $hasHta ? 'border-success' : 'border-warning' }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-semibold">
                                                        <i class="fa fa-clipboard-check me-1 text-primary"></i> HTA / GPA
                                                    </h6>
                                                    @if ($hasHta)
                                                        <span class="badge bg-success">Lengkap</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Belum Lengkap</span>
                                                    @endif
                                                </div>

                                                @if (!$hasHta)
                                                    <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                       class="btn btn-warning btn-sm w-100">
                                                        <i class="fa fa-exclamation-circle"></i> Lengkapi HTA
                                                    </a>
                                                @else
                                                    @if (($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak') && !$htaFinal)
                                                        <a href="{{ route('htagpa.form-hta', [$data->id, $item->id]) }}"
                                                           class="btn btn-warning btn-sm mb-2 w-100">
                                                            <i class="fa fa-exclamation-circle"></i> Ubah HTA
                                                        </a>
                                                    @endif
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('htagpa.show', [$data->id, $item->id]) }}"
                                                           class="btn btn-success btn-sm flex-fill">
                                                            <i class="fa fa-check-circle"></i> Lihat
                                                        </a>
                                                        <a href="{{ route('htagpa.print', [$data->id, $item->id]) }}"
                                                           class="btn btn-info btn-sm flex-fill" target="_blank">
                                                            <i class="fa fa-print"></i> Cetak
                                                        </a>
                                                    </div>

                                                    {{-- Timestamp terakhir update HTA --}}
                                                    @if ($htaUpdate)
                                                        <div class="mt-2 small text-secondary">
                                                            <i class="fa fa-clock me-1"></i>
                                                            Diperbarui: {{ $htaUpdate }} WIB
                                                        </div>
                                                    @endif

                                                    @if ($htaFinal)
                                                        <small class="text-success d-block mt-2">
                                                            <i class="fa fa-lock me-1"></i>Final
                                                        </small>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                    {{-- ─────────── 3. FEASIBILITY STUDY (hanya jika Jenis == 1) ─────────── --}}
                                    @if ($isFsRequired)
                                        <div class="{{ $colDoc }}">
                                            <div class="card h-100 {{ $adaFs ? 'border-success' : ($adaRekomendasi ? 'border-warning' : 'border-secondary') }}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0 fw-semibold">
                                                            <i class="fa fa-chart-line me-1 text-primary"></i> Feasibility Study
                                                        </h6>
                                                        @if ($adaFs)
                                                            <span class="badge bg-success">Lengkap</span>
                                                        @elseif($adaRekomendasi)
                                                            <span class="badge bg-warning text-dark">Belum Lengkap</span>
                                                        @else
                                                            <span class="badge bg-secondary">Menunggu</span>
                                                        @endif
                                                    </div>

                                                    @if (!$adaRekomendasi)
                                                        <div class="alert alert-danger p-2 mb-0 small">
                                                            Tersedia setelah Rekomendasi keluar.
                                                        </div>
                                                    @else
                                                        @if ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak')
                                                            @if ($adaFs)
                                                                <div class="d-flex flex-column gap-1">
                                                                    <a href="{{ route('fs.edit', [$data->id, $item->id]) }}"
                                                                       class="btn btn-primary btn-sm">
                                                                        <i class="fa fa-edit"></i> Ubah
                                                                    </a>
                                                                    <div class="d-flex gap-1">
                                                                        <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                           class="btn btn-success btn-sm flex-fill">
                                                                            <i class="fa fa-eye"></i> Lihat
                                                                        </a>
                                                                        <a href="{{ route('fs.cetak', [$data->id, $item->id]) }}"
                                                                           class="btn btn-info btn-sm flex-fill" target="_blank">
                                                                            <i class="fa fa-print"></i> Cetak
                                                                        </a>
                                                                    </div>

                                                                    {{-- Timestamp terakhir update FS --}}
                                                                    @if ($fsUpdate)
                                                                        <div class="mt-2 small text-secondary">
                                                                            <i class="fa fa-clock me-1"></i>
                                                                            Diperbarui: {{ $fsUpdate }} WIB
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                @role(['Keuangan', 'Admin'])
                                                                    <a href="{{ route('fs.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                       class="btn btn-primary btn-sm w-100">
                                                                        <i class="fa fa-edit"></i> Lengkapi
                                                                    </a>
                                                                @else
                                                                    <div class="alert alert-danger p-2 mb-0 small">
                                                                        Dibuat oleh Keuangan/Admin.
                                                                    </div>
                                                                @endrole
                                                            @endif
                                                        @else
                                                            @if ($adaFs)
                                                                <div class="d-flex gap-1">
                                                                    <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                       class="btn btn-success btn-sm flex-fill">
                                                                        <i class="fa fa-eye"></i> Lihat
                                                                    </a>
                                                                    <a href="{{ route('fs.cetak', [$data->id, $item->id]) }}"
                                                                       class="btn btn-info btn-sm flex-fill" target="_blank">
                                                                        <i class="fa fa-print"></i> Cetak
                                                                    </a>
                                                                </div>

                                                                {{-- Timestamp terakhir update FS (status non-draft) --}}
                                                                @if ($fsUpdate)
                                                                    <div class="mt-2 small text-secondary">
                                                                        <i class="fa fa-clock me-1"></i>
                                                                        Diperbarui: {{ $fsUpdate }} WIB
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <small class="text-muted d-block">Sedang diproses.</small>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- ─────────── 4. USULAN INVESTASI ─────────── --}}
                                    <div class="{{ $colDoc }}">
                                        <div class="card h-100 {{ $adaFui ? 'border-success' : ($adaRekomendasi ? 'border-warning' : 'border-secondary') }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-semibold">
                                                        <i class="fa fa-lightbulb me-1 text-primary"></i> Usulan Investasi
                                                    </h6>
                                                    @if ($adaFui)
                                                        <span class="badge bg-success">Lengkap</span>
                                                    @elseif($adaRekomendasi)
                                                        <span class="badge bg-warning text-dark">Belum Lengkap</span>
                                                    @else
                                                        <span class="badge bg-secondary">Menunggu</span>
                                                    @endif
                                                </div>

                                                @if ($adaRekomendasi)
                                                    @if (!$adaFui)
                                                        @if ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak')
                                                            <a href="{{ route('usulan-investasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                               class="btn btn-warning btn-sm w-100">
                                                                <i class="fa fa-lightbulb"></i> Lengkapi
                                                            </a>
                                                        @else
                                                            <small class="text-muted d-block">Sedang diproses.</small>
                                                        @endif
                                                    @else
                                                        @if (optional($item->getFui)->SudahRkap2 === null &&
                                                                ($data->Status == 'Draft' || $data->Status == 'Selesai Review' || $data->Status == 'Ditolak'))
                                                            <a href="{{ route('usulan-investasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                               class="btn btn-warning btn-sm mb-2 w-100">
                                                                <i class="fa fa-edit"></i> Lengkapi
                                                            </a>
                                                        @endif
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ route('usulan-investasi.show', [$data->id, $item->id]) }}"
                                                               class="btn btn-success btn-sm flex-fill">
                                                                <i class="fa fa-eye"></i> Lihat
                                                            </a>
                                                            <a href="{{ route('usulan-investasi.print', [$data->id, $item->id]) }}"
                                                               class="btn btn-info btn-sm flex-fill" target="_blank">
                                                                <i class="fa fa-print"></i> Cetak
                                                            </a>
                                                        </div>

                                                        {{-- Timestamp terakhir update FUI --}}
                                                        @if ($fuiUpdate)
                                                            <div class="mt-2 small text-secondary">
                                                                <i class="fa fa-clock me-1"></i>
                                                                Diperbarui: {{ $fuiUpdate }} WIB
                                                            </div>
                                                        @endif
                                                    @endif
                                                @else
                                                    <div class="alert alert-danger p-2 mb-0 small">
                                                        Tersedia setelah Rekomendasi keluar.
                                                    </div>
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
                <i class="fa fa-inbox fa-4x text-muted mb-3" style="opacity:0.4;"></i>
                <h5 class="text-muted">Data Item Belum Tersedia</h5>
                <p class="text-muted mb-0">Belum ada item yang ditambahkan ke pengajuan ini.</p>
            </div>
        @endif

    </div>
</div>
{{-- ============================================================ --}}
{{-- END DAFTAR ITEM YANG DIAJUKAN                                --}}
{{-- ============================================================ --}}
<!-- End of Selection -->

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

    {{-- Script Tambahan: Konfirmasi Hapus Vendor --}}
    <script>
        document.querySelectorAll('.btn-hapus-vendor').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const vnIdx = this.getAttribute('data-vnidx');
                Swal.fire({
                    title: 'Konfirmasi Hapus Vendor',
                    text: 'Apakah Anda yakin ingin menghapus vendor ini beserta seluruh detail barangnya?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`form-hapus-vendor-${vnIdx}`).submit();
                    }
                });
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
