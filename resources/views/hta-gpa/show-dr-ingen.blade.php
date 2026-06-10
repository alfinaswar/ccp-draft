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

                    @php
                        // Array warna background pastel untuk tiap vendor
                        $vendorColors = [
                            '#e3f2fd', // Biru muda
                            '#e8f5e9', // Hijau muda
                            '#fff3e0', // Oranye muda
                            '#f3e5f5', // Ungu muda
                            '#fff9c4', // Kuning muda
                            '#fce4ec', // Pink muda
                            '#e0f7fa', // Cyan muda
                        ];
                    @endphp
                    <div class="table-responsive">
                        <table class="table align-middle table-bordered" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width: 5%;">No
                                    </th>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width: 15%;">
                                        Parameter Penilaian</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" colspan="2"
                                            style="width: {{ 80 / count($data->getVendor) }}%;">
                                            {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" style="width: calc(35 / {{ count($data->getVendor) }}%);">
                                            Deskripsi</th>
                                        <th class="text-center"
                                            style="width: calc(10 / {{ count($data->getVendor) }}%); min-width:60px; max-width: 90px;">
                                            Subtotal
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->getJenisPermintaan->getForm->Parameter as $key => $pm)
                                    <tr>
                                        <td class="text-center" style="background:#f8f9fa;">{{ $key + 1 }}</td>
                                        <td style="background:#f8f9fa;">
                                            {{ $parameter[$pm - 1]->Nama ?? '-' }}
                                        </td>
                                        @foreach ($data->getVendor as $vIdx => $Vendor)
                                            <td
                                                style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                                {!! $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key] ?? '' !!}
                                            </td>
                                            <td
                                                style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width: 90px; width:1%;">
                                                <input type="text"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}"
                                                    class="form-control bg-transparent border-0" readonly
                                                    style="font-weight:bold; text-align:center; min-width:60px; max-width:90px;">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                {{-- Grand Total --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Grand Total</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                            @php
                                                $grandTotal = 0;
                                                if (
                                                    isset($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal) &&
                                                    is_array($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal)
                                                ) {
                                                    foreach (
                                                        $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal
                                                        as $subtotal
                                                    ) {
                                                        $grandTotal += is_numeric($subtotal) ? floatval($subtotal) : 0;
                                                    }
                                                }
                                                $grandTotalShow =
                                                    $grandTotal > 0
                                                        ? $grandTotal
                                                        : $data->getHtaGpa->getDetailHta[$vIdx]->GrandTotal ?? '';
                                            @endphp
                                            <input type="text" value="{{ $grandTotalShow }}"
                                                class="form-control bg-transparent border-0" readonly
                                                style="font-weight:bold; text-align:center; min-width:60px; max-width:90px;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Umur Ekonomis --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Umur Ekonomis</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->UmurEkonomis ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Tarif Diusulkan --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Tarif Diusulkan</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Buyback Period --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Buyback Period</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->BuybackPeriod ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Target Pemakaian Bulanan --}}
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle; background:#f8f9fa;">
                                        Target Pemakaian Bulanan</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <input type="text" class="form-control bg-transparent border-0" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TargetPemakaianBulanan ?? '-' }}"
                                                style="text-align:center;">
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Keterangan --}}
                                <tr>
                                    <th class="text-end align-top" colspan="2"
                                        style="vertical-align: top; background:#f8f9fa;">Keterangan</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="1"
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }};">
                                            <textarea class="form-control bg-transparent border-0" rows="6" readonly style="resize: vertical;">{!! $data->getHtaGpa->getDetailHta[$vIdx]->Keterangan ?? '-' !!}</textarea>
                                        </th>
                                        <th
                                            style="background-color: {{ $vendorColors[$vIdx % count($vendorColors)] }}; min-width:60px; max-width:90px; width:1%;">
                                        </th>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <div class="row mt-4 justify-content-center">
                        <div class="col-12">
                            <h5 class="text-center mb-4"><strong>Persetujuan HTA / GPA</strong></h5>
                            <div class="mb-2 text-center">
                                @if (!empty($approval))
                                    <div class="row justify-content-center">
                                        @php
                                            $jabatan = [
                                                'Kepala KSM Rumah Sakit',
                                                'Ketua Tim HTA Rumah Sakit',
                                                'Direktur Rumah Sakit',
                                                'Group Head Medik',
                                                'Group Head Penunjang Medis',
                                            ];
                                        @endphp
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
                                                        <img src="data:image/png;base64,{{ $item->qrCode }}"
                                                            alt="QR Code" style="width:80px; height:80px;"><br>
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
                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary me-2">
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
                        @if (isset($approval) && count($approval) > 0)
                            <div class="mt-4">
                                <dl>
                                    @php $nomor = 1; @endphp
                                    @foreach ($approval as $item)
                                        @if (!empty($item->Justifikasi))
                                            <dt>
                                                <strong>{{ $nomor++ }}.
                                                    Justifikasi{{ $item->Nama ? ' oleh ' . $item->Nama : '' }}:</strong>
                                            </dt>
                                            <dd class="text-muted" style="margin-bottom:10px;">
                                                {{ $item->Justifikasi }}
                                            </dd>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        @endif
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
