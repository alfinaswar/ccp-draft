<style>
    @page hta {
        size: landscape;
        margin: 10mm;
        scale: 0.9;
    }

    .page-hta {
        page: hta;
        font-size: 8pt;
        transform: scale(0.9);
        transform-origin: top left;
        width: unset;
        height: unset;
    }

    body,
    .cetak-hta-gpa-global {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9pt;
    }

    table.cetak-hta {
        font-size: 9pt;
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table.cetak-hta th,
    table.cetak-hta td {
        border: 1px solid #222;
        padding: 5px;
    }

    table.cetak-hta tr {
        line-height: 0.9;
    }

    .cetak-hta-caption {
        border: none !important;
    }

    .cetak-hta-caption th,
    .cetak-hta-caption td {
        border: none !important;
        padding-bottom: 3px;
    }

    .col-parameter {
        font-size: 9pt !important;
        min-width: 110px;
        max-width: 180px;
        width: 140px;
        vertical-align: middle;
        text-align: center;
    }

    .col-subtotal {
        font-size: 9pt !important;
        min-width: 30px;
        width: 30px;
        text-align: center;
        font-weight: 700;
        background: #f4f4f4;
        vertical-align: middle;
    }

    .printed-info {
        margin-top: 30px;
        font-size: 8pt;
        color: #555;
        font-style: italic;
        text-align: right;
    }
</style>
<div class="page-hta">
    <div class="cetak-hta-gpa-global">
        <h2 style="text-align:center; margin-bottom:20px;">PENILAIAN HTA / GPA</h2>
        <table class="cetak-hta cetak-hta-caption">
            <tr>
                <th>Nama Barang</th>
                <td colspan="{{ count($dataHta->getVendor) }}">
                    {{ $dataHta->getPengajuanItem[0]->getBarang->Nama ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>Merek</th>
                <td colspan="{{ count($dataHta->getVendor) }}">
                    {{ $dataHta->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}
                </td>
            </tr>
        </table>

        {{-- Table HEAD: No | Parameter | Vendor 1 (Deskripsi, Nilai 1-5, Subtotal) | Vendor 2 (Deskripsi, Nilai 1-5, Subtotal) ... --}}
        <table class="cetak-hta">
            <thead>
                <tr>
                    <th rowspan="2" style="min-width: 30px; width: 35px;">No</th>
                    <th rowspan="2" class="col-parameter">Parameter</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <th colspan="7" style="text-align:center; min-width:310px;">
                            {{ $Vendor->getNamaVendor->Nama ?? 'Vendor ' . ($vIdx + 1) }}
                        </th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <th style="min-width:110px;max-width:200px;">Deskripsi</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th class="col-subtotal">Sub Total</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- {{ dd($data->getHtaGpa) }} --}}
                @foreach ($dataHta->getJenisPermintaan->getForm->Parameter as $key => $pm)
                    <tr>
                        <td style="text-align:center;">{{ $key + 1 }}</td>
                        <td class="col-parameter">{{ $parameter[$pm - 1]->Nama ?? '-' }}</td>
                        @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                            <td>
                                {!! isset($dataHta->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key])
                                    ? $dataHta->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key]
                                    : '-' !!}
                            </td>
                            <td style="width:14px;text-align:center;">
                                {{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->Nilai1[$key] ?? '' }}</td>
                            <td style="width:14px;text-align:center;">
                                {{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->Nilai2[$key] ?? '' }}</td>
                            <td style="width:14px;text-align:center;">
                                {{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->Nilai3[$key] ?? '' }}</td>
                            <td style="width:14px;text-align:center;">
                                {{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->Nilai4[$key] ?? '' }}</td>
                            <td style="width:14px;text-align:center;">
                                {{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->Nilai5[$key] ?? '' }}</td>
                            <td class="col-subtotal">
                                {{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align:right;">Grand Total</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        @php
                            $grandTotal = 0;
                            if (
                                isset($dataHta->getHtaGpa->getDetailHta[$vIdx]->SubTotal) &&
                                is_array($dataHta->getHtaGpa->getDetailHta[$vIdx]->SubTotal)
                            ) {
                                foreach ($dataHta->getHtaGpa->getDetailHta[$vIdx]->SubTotal as $sub) {
                                    $grandTotal += is_numeric($sub) ? $sub : 0;
                                }
                            }
                        @endphp
                        <th colspan="7" style="text-align:right; background: #f4f4f4; font-weight:700;">
                            {{ $grandTotal }}
                        </th>
                    @endforeach
                </tr>
            </tfoot>
        </table>

        {{-- Table Perbandingan Ekonomi --}}
        <table class="cetak-hta">
            <thead>
                <tr>
                    <th class="col-parameter">Parameter</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <th>{{ $Vendor->getNamaVendor->Nama ?? 'Vendor ' . ($vIdx + 1) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="col-parameter">Umur Ekonomis</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <td>{{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->UmurEkonomis ?? '-' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <th class="col-parameter">Buyback Period</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <td>{{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->BuybackPeriod ?? '-' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <th class="col-parameter">Tarif Diusulkan</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <td>{{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->TarifDiusulkan ?? '-' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <th class="col-parameter">Target Pemakaian Bulanan</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <td>{{ $dataHta->getHtaGpa->getDetailHta[$vIdx]->TargetPemakaianBulanan ?? '-' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <th class="col-parameter">Keterangan</th>
                    @foreach ($dataHta->getVendor as $vIdx => $Vendor)
                        <td>{!! $dataHta->getHtaGpa->getDetailHta[$vIdx]->Keterangan ?? '-' !!}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <h5 class="text-center mb-4"><strong>Persetujuan Permintaan Pembelian</strong></h5>
    <!-- Untuk cetak PDF tanda tangan approval -->
    <table style="width:100%; margin: 0 auto; border:none;">
        <colgroup>
            @if (!empty($approvalHta))
                @foreach ($approvalHta as $item)
                    <col style="width: {{ 100 / count($approvalHta) }}%;">
                @endforeach
            @endif
        </colgroup>
        <tbody>
            <tr>
                @php
                    $jabatan = [
                        'Kepala KSM Rumah Sakit',
                        'Ketua Tim HTA Rumah Sakit',
                        'Direktur Rumah Sakit',
                        'Group Head Medik',
                        'Group Head Penunjang Medis',
                    ];
                @endphp
                @foreach ($approvalHta as $idx => $item)
                    <td style="text-align:center; font-weight:600; border:none;">
                        {{ $jabatan[$idx] ?? '-' }}
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalHta as $item)
                    <td class="text-center align-bottom" style="height: 20px; border:none;">
                        {{-- Tempat kosong untuk tanda tangan basah di cetak PDF --}}
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalHta as $item)
                    <td class="text-center" style="height:80px; vertical-align: top;">
                        @if ($item->Status == 'Approved' && isset($item->qrCode))
                            <center>
                                <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                    style="width:80px; height:80px;">

                            </center>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalHta as $item)
                    <td class="text-center" style="padding-bottom:0; border:none;">
                        <hr style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalHta as $item)
                    <td class="text-center align-top" style="border:none;">
                        <span style="font-weight:600; display: block; text-align: center;">
                            {{ $item->Nama ?? '-' }}
                        </span>
                        <div style="display: block; text-align: center;">
                            <small style="display: inline-block;">{{ $item->Status ?? '-' }}</small>
                            <br>
                            <small><em>
                                    {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        </div>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <div class="printed-info">
        Dicetak oleh: {{ auth()->user()->name ?? '-' }} pada {{ now()->format('d-m-Y H:i') }}
    </div>
    </table>
</div>
