@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Review Pengajuan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ajukan.index') }}">Review Pengajuan Pembelian</a></li>
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
                    <h4 class="card-title mb-0">
                        Detail Pengajuan Pembelian -
                        <span style="font-size: 0.92em;">
                            {{ $data->KodePengajuan ?? '-' }}
                        </span>
                    </h4>
                    <div>
                        <span class="badge text-dark" style="font-size: 1em;">
                            {{ $data->Status ?? '-' }}
                        </span>
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
                                                        <th>Merek</th>
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
                                                                    <span>{{ optional($barangMaster?->getMerk)->Nama ?? '-' }}</span>
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
                                                    <td class="text-center">
                                                        <a href="{{ route('rekomendasi.create', [encrypt($data->id), encrypt($item->id)]) }}"
                                                            class="btn btn-primary">
                                                            <i class="fa fa-pen"></i> Buat Rekomendasi
                                                        </a>
                                                        @php
                                                            $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                        @endphp
                                                        @if ($adaRekomendasi)
                                                            <a href="{{ route('rekomendasi.detail-print', [encrypt($data->id), encrypt($item->id)]) }}"
                                                                class="btn btn-info ms-2" target="_blank">
                                                                <i class="fa fa-print"></i> Print
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
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $hasHta = $item->getHtaGpa ? true : false;
                                                        @endphp
                                                        @if (!$hasHta)
                                                            <div class="mb-0 p-2 text-center"
                                                                style="font-size: 0.95rem; border: 1px solid #ffc107; border-radius: 0.25rem;">
                                                                <i class="fa fa-info-circle me-1"
                                                                    style="color: #ffc107;"></i>
                                                                <span class="fw-semibold">Dokumen HTA/GPA belum tersedia.
                                                                    Dokumen akan diisi Logum atau SMI.</span>
                                                            </div>
                                                        @else
                                                            <a href="{{ route('htagpa.show', [$data->id, $item->id]) }}"
                                                                class="btn btn-success">
                                                                <i class="fa fa-check-circle"></i>
                                                                Lihat Dokumen HTA
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $adaRekomendasi = $item->getRekomendasi ? true : false;
                                                            $adaFs = $item->getFs ? true : false;
                                                        @endphp
                                                        @if (!$adaRekomendasi)
                                                            <div class="alert alert-danger p-2 m-0"
                                                                style="font-size: 90%;">
                                                                Form Fisibility Study Akan Dibuat Oleh Keuangan
                                                                Rekomendasi Dikeluarkan
                                                            </div>
                                                        @else
                                                            @if ($adaFs)
                                                                <a href="{{ route('fs.show', [$data->id, $item->id]) }}"
                                                                    class="btn btn-success">
                                                                    <i class="fa fa-eye"></i>
                                                                    Lihat FS
                                                                </a>
                                                            @else
                                                                <div class="alert alert-danger p-2 m-0"
                                                                    style="font-size: 90%;">
                                                                    Form Fisibility Study Akan Dibuat Oleh Keuangan setelah
                                                                    Rekomendasi Dikeluarkan
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $adaFui = $item->getFui ? true : false;
                                                        @endphp
                                                        @if (!$adaFui)
                                                            <div class="mb-0 p-2 text-center"
                                                                style="font-size: 0.95rem; border: 1px solid #ffc107; border-radius: 0.25rem;">
                                                                <i class="fa fa-info-circle me-1"
                                                                    style="color: #ffc107;"></i>
                                                                <span class="fw-semibold">FUI</span> akan diisi oleh <span
                                                                    class="fw-semibold">Logum / SMI</span> setelah
                                                                rekomendasi diterbitkan.
                                                            </div>
                                                        @else
                                                            <a href="{{ route('usulan-investasi.print', [$data->id, $item->id]) }}"
                                                                class="btn btn-info">
                                                                <i class="fa fa-print"></i>
                                                                Cetak FUI
                                                            </a>
                                                            <a href="{{ route('usulan-investasi.show', [$data->id, $item->id]) }}"
                                                                class="btn btn-success">
                                                                <i class="fa fa-eye"></i>
                                                                Lihat FUI
                                                            </a>
                                                        @endif
                                                    </td>
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
@endsection
@push('js')
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
                                Email sedang dikirim ke <strong>Ir. H. Arfan Awaloeddin</strong>
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
