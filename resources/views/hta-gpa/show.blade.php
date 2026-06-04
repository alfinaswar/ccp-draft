@extends('layouts.app')

@section('content')
    {{-- @php
        dd($data->getVendor);
    @endphp --}}
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
                        <table class="table align-middle" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width:40px;">No
                                    </th>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle; width:180px;">
                                        Parameter Penilaian</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" colspan="7" style="min-width:250px;">
                                            {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th class="text-center" style="width:120px;">Deskripsi</th>
                                        <th class="text-center" style="width:70px;">Nilai 1</th>
                                        <th class="text-center" style="width:70px;">Nilai 2</th>
                                        <th class="text-center" style="width:70px;">Nilai 3</th>
                                        <th class="text-center" style="width:70px;">Nilai 4</th>
                                        <th class="text-center" style="width:70px;">Nilai 5</th>
                                        <th class="text-center" style="width:90px;">Subtotal</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->getJenisPermintaan->getForm->Parameter as $key => $pm)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td>
                                            {{ $parameter[$pm - 1]->Nama ?? '-' }}
                                        </td>
                                        @foreach ($data->getVendor as $vIdx => $Vendor)
                                            <td>
                                                {!! $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key] ?? '' !!}
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai1[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai2[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai3[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai4[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="number" readonly min="0" max="5"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai5[$key] ?? '' }}"
                                                    class="form-control bg-light" style="width:65px;">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}"
                                                    class="form-control bg-light" readonly style="font-weight:bold;">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Grand Total</th>
                                    @foreach ($data->getVendor as $vIdx => $Vendor)
                                        <th colspan="6"></th>
                                        <th>
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
                                                // Fallback ke GrandTotal lama jika kolom tetap mau muncul
                                                $grandTotalShow =
                                                    $grandTotal > 0
                                                        ? $grandTotal
                                                        : $data->getHtaGpa->getDetailHta[$vIdx]->GrandTotal ?? '';
                                            @endphp
                                            <input type="text" value="{{ $grandTotalShow }}"
                                                class="form-control bg-light" readonly style="font-weight:bold;">
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Umur Ekonomis
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"> <input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->UmurEkonomis ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Tarif Diusulkan
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"> <input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Buyback Period
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"><input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->BuybackPeriod ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end" colspan="2" style="vertical-align: middle;">Target Pemakaian
                                        Bulanan</th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6"> <input type="text" class="form-control bg-light" readonly
                                                value="{{ $data->getHtaGpa->getDetailHta[$vIdx]->TargetPemakaianBulanan ?? '-' }}">
                                        </th>
                                        <th>

                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="text-end align-top" colspan="2" style="vertical-align: top;">Keterangan
                                    </th>
                                    @foreach ($data->getVendor as $Vendor)
                                        <th colspan="6">
                                            <textarea class="form-control bg-light" rows="6" readonly style="resize: vertical;">{!! $data->getHtaGpa->getDetailHta[$vIdx]->Keterangan ?? '-' !!}</textarea>
                                        </th>
                                        <th>

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
                        {{-- {{ dd($data->getHtaGpa->IdPengajuan, $data->getHtaGpa) }} --}}
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
