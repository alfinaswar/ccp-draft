@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Detail Penilaian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Hta / Gpa</a></li>
                    <li class="breadcrumb-item active">Detail Penilaian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Penilaian HTA / GPA
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
                            </thead>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>File Penilaian yang Diupload</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @forelse ($data->getVendor as $vIdx => $Vendor)
                                        <div class="col-md-6 mb-3">
                                            <div class="card" style="border:1px solid #d7d7d7;">
                                                <div class="card-body">
                                                    <h6 class="mb-2">{{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}</h6>
                                                    @if (!empty($Vendor->getHtaGpa->File))
                                                        <ul>
                                                            <li>
                                                                <a href="{{ asset('storage/upload/gpa/' . $Vendor->getHtaGpa->File) }}"
                                                                    target="_blank">
                                                                    {{ $Vendor->getHtaGpa->File }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">Tidak ada file diupload.</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <span class="text-muted">Tidak ada vendor ditemukan.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan Permintaan Pembelian</strong></h5>
                            <!-- Tambah baris untuk nama jabatan di atas tabel approval -->
                            <div class="mb-2 text-center">

                                @if (!empty($approval))
                                    @if ($data->Jenis == '2')
                                        {{-- UMUM --}}
                                        @php
                                            if (
                                                !empty($approval) &&
                                                isset($approval[0]->NamaJabatan) &&
                                                $approval[0]->NamaJabatan !== null
                                            ) {
                                                $jabatan = [];
                                                foreach ($approval as $item) {
                                                    $jabatan[] = $item->NamaJabatan ?? '-';
                                                }
                                            } else {
                                                $jabatan = [
                                                    'Kepala Divisi Umum',
                                                    'Kepala Divisi Keuangan',
                                                    'Direktur Rumah Sakut',
                                                ];
                                            }
                                        @endphp
                                    @else
                                        {{-- PROYEK --}}
                                        @php
                                            if (
                                                !empty($approval) &&
                                                isset($approval[0]->NamaJabatan) &&
                                                $approval[0]->NamaJabatan !== null
                                            ) {
                                                $jabatan = [];
                                                foreach ($approval as $item) {
                                                    $jabatan[] = $item->NamaJabatan ?? '-';
                                                }
                                            } else {
                                                $jabatan = [
                                                    'Kepala Divisi Umum',
                                                    'Kepala Divisi Keuangan',
                                                    'Direktur Rumah Sakut',
                                                ];
                                            }
                                        @endphp
                                    @endif

                                    <div class="row justify-content-center">
                                        @foreach ($jabatan as $namaJabatan)
                                            <div class="col text-center" style="font-weight:600;">
                                                {{ $namaJabatan ?? '-' }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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
                                                <td class="text-center" style="height:80px; vertical-align: top;">
                                                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                                            style="width:80px; height:80px;">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        {{-- <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center" style="padding-bottom:0;">
                                                    <hr
                                                        style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                                                </td>
                                            @endforeach
                                        </tr> --}}
                                        <tr>
                                            @foreach ($approval as $item)
                                                <td class="text-center align-top">

                                                    <span style="font-weight:600;">
                                                        {{ $item->Nama ?? '-' }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $item->Status ?? '-' }}</small><br>
                                                    <small><em>
                                                            {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                                        </em></small>
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
                                @if (auth()->id() == ($item->UserId ?? null) && $item->Status != 'Approved' && !empty($item->ApprovalToken))
                                    <a href="{{ route('htagpa.approve', $item->ApprovalToken) }}"
                                        class="btn me-2 swal-confirm-btn"
                                        style="background-color: #28a745; color: #fff; border-color: #28a745;"
                                        data-title="Konfirmasi"
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

@push('js')
    @if (Session::get('error'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ Session::get('error') }}',
                    iconColor: '#d33',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#d33',
                });
            }, 500);
        </script>
    @endif
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ Session::get('success') }}',
                    iconColor: '#28a745',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#28a745',
                });
            }, 500);
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiApprove(idx, penilaiKe) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: 'Apakah Anda yakin ingin menyetujui HTA/GPA ini sebagai Penilai ' + penilaiKe + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + idx).submit();
                }
            });
        }
    </script>
@endpush
