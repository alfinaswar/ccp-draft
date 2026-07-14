@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Detail Usulan Investasi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Usulan Investasi</a></li>
                    <li class="breadcrumb-item active">Detail Usulan Investasi</li>
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
                <div class="card-body">


                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h5 class="fw-bold mb-1">Departemen Peminta</h5>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Tanggal</label>
                                    <input type="date" class="form-control" value="{{ $usulan->Tanggal ?? '-' }}"
                                        readonly>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Departemen</label>
                                    <input type="text" class="form-control"
                                        value="{{ $usulan->getDepartemen->Nama ?? '-' }}" readonly>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Nama Kepala Divisi</label>
                                    <input type="text" class="form-control" value="{{ $usulan->getKadiv->name ?? '-' }}"
                                        readonly>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <input type="text" class="form-control" value="{{ $usulan->Kategori ?? '-' }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h5 class="fw-bold mb-1">Departemen Pembelian</h5>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Tanggal</label>
                                    <input type="date" class="form-control" value="{{ $usulan->Tanggal2 ?? '-' }}"
                                        readonly>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Departemen</label>
                                    <input type="text" class="form-control"
                                        value="{{ $usulan->getDepartemen->Nama ?? '-' }}" readonly>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Nama Kepala Divisi</label>
                                    <input type="text" class="form-control" value="{{ $usulan->getKadiv2->name ?? '-' }}"
                                        readonly>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <input type="text" class="form-control" value="{{ $usulan->Kategori2 ?? '-' }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="Keterangan" class="form-label fw-bold">Dengan ini kami ajukan permohonan untuk
                            pengadaan barang / jasa dengan alasan sebagai berikut :</label>
                        <textarea class="form-control" id="Keterangan" rows="3" readonly>{{ $usulan->Alasan ?? '-' }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">List Item Usulan Investasi</label>
                        <div class="table-responsive">
                            <table class="table align-middle" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th>Nama Barang / Jasa</th>
                                        <th>Nama Vendor</th>
                                        <th>Harga Awal</th>
                                        <th>Harga Nego</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandTotal = 0;
                                        if (!function_exists('rupiah')) {
                                            function rupiah($angka)
                                            {
                                                return 'Rp ' . number_format($angka, 0, ',', '.');
                                            }
                                        }
                                    @endphp
                                    @if (!empty($dataRekom) && isset($dataRekom->getRekomedasiDetail) && count($dataRekom->getRekomedasiDetail) > 0)
                                        @foreach ($dataRekom->getRekomedasiDetail as $key => $rekomDetail)
                                            @php
                                                $harga_awal = (int) preg_replace(
                                                    '/[^\d]/',
                                                    '',
                                                    $rekomDetail->HargaAwal ?? 0,
                                                );
                                                $harga_nego = (int) preg_replace(
                                                    '/[^\d]/',
                                                    '',
                                                    $rekomDetail->HargaNego ?? 0,
                                                );
                                                $total = $harga_nego; // Total = Harga Nego
                                                $grandTotal += $total;
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    {{ $rekomDetail->getBarang->Nama ?? '-' }}
                                                </td>
                                                <td>
                                                    {{ $rekomDetail->getNamaVendor->Nama ?? '-' }}
                                                </td>
                                                <td>
                                                    {{ isset($rekomDetail->HargaAwal) ? rupiah($harga_awal) : 'Rp 0' }}
                                                </td>
                                                <td>
                                                    {{ isset($rekomDetail->HargaNego) ? rupiah($harga_nego) : 'Rp 0' }}
                                                </td>
                                                <td>
                                                    {{ rupiah($total) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada item.</td>
                                        </tr>
                                    @endif
                                    {{-- <tr>
                                        <td colspan="5" class="text-end fw-bold">Grand Total</td>
                                        <td>
                                            <strong>{{ rupiah($grandTotal) }}</strong>
                                        </td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rincian Biaya</label>
                        <div class="table-responsive">
                            <table class="table align-middle" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th>Nama Barang / Jasa</th>
                                        <th>Nama Vendor</th>
                                        <th>Harga Awal</th>
                                        <th>Harga Nego</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandTotal = 0;
                                        if (!function_exists('rupiah')) {
                                            function rupiah($angka)
                                            {
                                                return 'Rp ' . number_format($angka, 0, ',', '.');
                                            }
                                        }
                                        $rekomItems = collect($data2->getRekomendasi[0]->getRekomedasiDetail ?? [])
                                            ->where('Rekomendasi', 1)
                                            ->values();
                                    @endphp
                                    @forelse ($rekomItems as $key => $rekomDetail)
                                        @php
                                            $harga_awal = (int) preg_replace(
                                                '/[^\d]/',
                                                '',
                                                $rekomDetail->HargaAwal ?? 0,
                                            );
                                            $harga_nego = (int) preg_replace(
                                                '/[^\d]/',
                                                '',
                                                $rekomDetail->HargaNego ?? 0,
                                            );
                                            $total = $harga_nego; // Total = Harga Nego, bisa disesuaikan jika ada field lain (misal dikali qty)
                                            $grandTotal += $total;
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $rekomDetail->getBarang->Nama ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $rekomDetail->getNamaVendor->Nama ?? '-' }}
                                            </td>
                                            <td>
                                                {{ isset($rekomDetail->HargaAwal) ? rupiah($harga_awal) : 'Rp 0' }}
                                            </td>
                                            <td>
                                                {{ isset($rekomDetail->HargaNego) ? rupiah($harga_nego) : 'Rp 0' }}
                                            </td>
                                            <td>
                                                {{ rupiah($total) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada item.</td>
                                        </tr>
                                    @endforelse
                                    {{-- <tr>
                                        <td colspan="5" class="text-end fw-bold">Grand Total</td>
                                        <td>
                                            <strong>{{ rupiah($grandTotal) }}</strong>
                                        </td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-2">Verifikasi RKAP <span class="fw-normal">(Departemen)</span>
                                    </h5>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                        <div>
                                            @if ($usulan->SudahRkap === 'Y')
                                                <span class="badge bg-success">Ya</span>
                                            @elseif($usulan->SudahRkap === 'N')
                                                <span class="badge bg-danger">Tidak</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Sisa Budget dari RKAP untuk tahun ini yang masih
                                            dapat dipergunakan:</label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($usulan->SisaBudget) ? 'Rp ' . number_format($usulan->SisaBudget, 0, ',', '.') : '-' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h5 class="fw-bold mb-2">Verifikasi RKAP <span class="fw-normal">(Keuangan)</span>
                                    </h5>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Sudah masuk RKAP dari departemen ybs:</label>
                                        <div>
                                            @if ($usulan->SudahRkap2 === 'Y')
                                                <span class="badge bg-success">Ya</span>
                                            @elseif($usulan->SudahRkap2 === 'N')
                                                <span class="badge bg-danger">Tidak</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Sisa Budget dari RKAP untuk tahun ini yang masih
                                            dapat dipergunakan:</label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($usulan->SisaBudget2) ? 'Rp ' . number_format($usulan->SisaBudget2, 0, ',', '.') : '-' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan Permintaan Pembelian</strong></h5>

                            @php
                                $showApprovalList = false;
                                $approvalList = [];
                                $totalNego = 0;
                                $jenisP = $data2->getPerusahaan->Kategori;
                                if (
                                    isset($data2) &&
                                    isset($data2->Jenis) &&
                                    !empty($dataRekom) &&
                                    isset($dataRekom->getRekomedasiDetail)
                                ) {
                                    $rekomendasiSatu = $dataRekom->getRekomedasiDetail->first(function ($item) {
                                        return isset($item->Rekomendasi) && $item->Rekomendasi == 1;
                                    });
                                    $totalNego = $rekomendasiSatu->HargaNego;
                                    $showApprovalList = true;

                                    if ($data2->Jenis == 1) {
                                        if ($totalNego < 50000000) {
                                            $approvalList = ['Kepala Divisi JangMed', 'Direktur'];
                                        } elseif ($totalNego >= 50000000 && $totalNego <= 100000000) {
                                            $approvalList = [
                                                'Kepala Divisi Jangmed',
                                                'Direktur RS',
                                                $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                            ];
                                        } else {
                                            $approvalList = [
                                                'Kepala Divisi JangMed',
                                                'Direktur RS',
                                                'GH Keuangan & Akt. RS. Awal Bros Group',
                                                $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                                'CEO RS. Awal Bros Group',
                                            ];
                                        }
                                    } else {
                                        if ($totalNego < 50000000) {
                                            $approvalList = ['Kepala Divisi Umum', 'Direktur'];
                                        } elseif ($totalNego >= 50000000 && $totalNego <= 100000000) {
                                            $approvalList = [
                                                'Kepala Divisi Umum',
                                                'Direktur RS',
                                                $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                            ];
                                        } else {
                                            $approvalList = [
                                                'Kepala Divisi Umum',
                                                'Direktur RS',
                                                'GH Keuangan & Akt. RS. Awal Bros Group',
                                                $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                                'CEO RS. Awal Bros Group',
                                            ];
                                        }
                                    }
                                }
                            @endphp

                            @if ($showApprovalList && !empty($approvalList))
                                <div class="mb-2 text-center">
                                    <div class="row justify-content-center">
                                        @foreach ($approval as $item)
                                            <div class="col text-center" style="font-weight:600;">
                                                {{ $item->NamaJabatan ?? '-' }}
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            @endif
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
                                                <td class="text-center" style="height:110px; vertical-align: top;">
                                                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}"
                                                            alt="QR Code" style="width:110px; height:110px;">
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
                                                    <small>{{ $item->Status ?? '-' }}</small>
                                                    <br>
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
                                @if (
                                    ($item->UserId ?? null) != 81 &&
                                        auth()->id() == ($item->UserId ?? null) &&
                                        $item->Status != 'Approved' &&
                                        !empty($item->ApprovalToken))
                                    <a href="{{ route('usulan-investasi.approve', $item->ApprovalToken) }}"
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
        document.getElementById('acc-direktur-btn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi ACC Direktur',
                text: "Apakah Anda yakin ingin meng-ACC sebagai Direktur?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ACC Direktur!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('acc-direktur-form').submit();
                }
            });
        });

        document.getElementById('acc-jangmed-btn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi ACC Jangmed',
                text: "Apakah Anda yakin ingin meng-ACC sebagai Jangmed?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ACC Jangmed!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('acc-jangmed-form').submit();
                }
            });
        });
    </script>
@endpush
