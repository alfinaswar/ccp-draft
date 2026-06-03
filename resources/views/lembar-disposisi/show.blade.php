@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Lembar Disposisi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('lembar-disposisi.index') }}">Lembar Disposisi</a></li>
                    <li class="breadcrumb-item active">Tambah Lembar Disposisi</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Formulir Lembar Disposisi</h4>
                </div>
                <div class="card-body">
                    {{-- {{ dd($data) }} --}}
                    <form>
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>NAMA BARANG / JASA YANG AKAN
                                                DIBELI</strong></label>
                                        <input type="text" class="form-control bg-light"
                                            value="@if (isset($data->getBarang->Nama)) {{ $data->getBarang->Nama }}
                                            @else
                                                {{ $data->NamaBarang ?? '-' }} @endif"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>HARGA</strong></label>
                                        @php
                                            $harga =
                                                $data->Harga ??
                                                ($data->getRekomendasi->getRekomedasiDetail[0]->HargaNego ?? 0);
                                        @endphp
                                        <input type="text" class="form-control bg-light"
                                            value="{{ number_format($harga, 0, ',', '.') }}" readonly>
                                        <small class="form-text text-muted">Harga yang telah dinegosiasi</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>RENCANA VENDOR</strong></label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $data->getVendor->Nama ?? ($data->RencanaVendor ?? '-') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="form-label"><strong>TUJUAN PENGGUNAAN/RUANGAN</strong></label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $data->TujuanPenempatan ?? ($data->getPengajuanPembelian->getPermintaan->getDetail[0]->RencanaPenempatan ?? 'Tidak Diisi Dipermintaan') }}"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block"><strong>FORM PERMINTAAN DARI USER</strong></label>
                                        <input type="text" class="form-control bg-light"
                                            value="@if (($data->FormPermintaan ?? null) == 'Y') Ya, Ada
                                                    @elseif(($data->FormPermintaan ?? null) == 'N')
                                                        Tidak ada
                                                    @else
                                                        Tidak diketahui @endif"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                            </div>

                        </div>
                    </form>
                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan Lembar Disposisi</strong></h5>
                            <!-- Tambah baris untuk nama jabatan di atas tabel approval -->
                            <div class="mb-2 text-center">
                                @php

                                    $isCisco = false;
                                    if (isset($data->getPengajuan->getPerusahaan)) {
                                        $perusahaan = $data->getPengajuan->getPerusahaan;
                                        // Ganti pengecekan sesuai struktur database sebenarnya
                                        if (
                                            (isset($perusahaan->Kategori) &&
                                                strtolower($perusahaan->Kategori) == 'cisco') ||
                                            (isset($perusahaan->Nama) && stripos($perusahaan->Nama, 'cisco') !== false)
                                        ) {
                                            $isCisco = true;
                                        }
                                    }

                                    if (($data->JenisForm ?? null) == 9) {
                                        $jabatan = [
                                            'KADIV YANMED RS',
                                            'KADIV JANGMED RS',
                                            'DIREKTUR RS',
                                            'GH PROCUREMENT',
                                            $isCisco ? 'DIREKTUR AB SISCO' : 'DIREKTUR RSAB GROUP',
                                            'CEO RSAB GROUP',
                                        ];
                                    } else {
                                        $jabatan = [
                                            'KADIV UMUM RS',
                                            'DIREKTUR RS',
                                            'GH PROCUREMENT',
                                            $isCisco ? 'DIREKTUR AB SISCO' : 'DIREKTUR RSAB GROUP',
                                            'CEO RSAB GROUP',
                                        ];
                                    }
                                @endphp
                                <div class="row justify-content-center">
                                    @foreach ($jabatan as $namaJabatan)
                                        <div class="col text-center">
                                            <strong>{{ $namaJabatan }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless" style="max-width:100%; margin: 0 auto;">
                                    <colgroup>
                                        @if (!empty($approval))
                                            @foreach ($approval as $item)
                                                <col style="width: {{ 100 / count($approval) }}%;">
                                            @endforeach
                                        @endif
                                    </colgroup>
                                    <tbody>

                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="height:100px; vertical-align: top;">
                                                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                                            style="width:100px; height:100px;">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="padding-bottom:0;">
                                                    <hr
                                                        style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center align-top">
                                                    <small>Nama Lengkap</small><br>
                                                    <span style="font-weight:600;">
                                                        {{ $item->Nama ?? '-' }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $item->Status ?? '-' }}</small><br>
                                                    <small>
                                                        <em>
                                                            {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                                        </em>
                                                    </small>
                                                </td>
                                            @endforeach
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <a href="javascript:history.back()" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>

                            @foreach ($approval as $item)
                                @if (
                                    ($item->UserId ?? null) != 81 &&
                                        auth()->id() == ($item->UserId ?? null) &&
                                        $item->Status != 'Approved' &&
                                        !empty($item->ApprovalToken))
                                    <a href="{{ route('lembar-disposisi.approve', $item->ApprovalToken) }}"
                                        class="btn btn-primary me-2 swal-confirm-btn" data-title="Konfirmasi"
                                        data-text="Apakah Anda yakin ingin menyetujui sebagai {{ $item->getJabatan->Nama ?? $item->JenisUser }}?">
                                        <i class="fa fa-check"></i>
                                        Setujui
                                    </a>
                                @endif
                            @endforeach

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
