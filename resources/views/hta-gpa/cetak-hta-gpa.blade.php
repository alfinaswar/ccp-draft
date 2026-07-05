<style>
    * {
        box-sizing: border-box;
    }

    body,
    .cetak-hta-gpa-global {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 7.5pt;
        margin: 0;
        padding: 0;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* =============================================
       TABEL UTAMA
    ============================================= */
    table.cetak-hta {
        font-size: 7.5pt;
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        page-break-inside: auto;
    }

    table.cetak-hta th,
    table.cetak-hta td {
        border: 1px solid #222;
        padding: 3px 5px;
        vertical-align: top;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: normal !important;
        overflow: visible !important;
        max-height: none !important;
    }

    table.cetak-hta tr {
        line-height: 1.3;
    }

    /* Caption (Nama Barang / Merek) */
    .cetak-hta-caption {
        table-layout: auto;
        border: none !important;
    }

    .cetak-hta-caption th,
    .cetak-hta-caption td {
        border: none !important;
        padding-bottom: 3px;
        white-space: normal !important;
        word-wrap: break-word;
    }

    /* =============================================
       LEBAR KOLOM
    ============================================= */
    .col-no {
        width: 24px;
        min-width: 20px;
        text-align: center;
        vertical-align: middle;
    }

    .col-parameter {
        font-size: 7.5pt !important;
        width: 100px;
        min-width: 80px;
        vertical-align: middle;
        text-align: left;
        word-break: break-word;
    }

    .col-deskripsi {
        font-size: 7.5pt !important;
        text-align: left;
        vertical-align: top;
        word-break: break-word;
        overflow: visible !important;
        max-height: none !important;
    }

    .col-nilai {
        width: 5px;
        min-width: 5px;
        text-align: center;
        font-size: 7pt;
        vertical-align: middle;
    }

    .col-subtotal {
        font-size: 7.5pt !important;
        width: 26px;
        min-width: 22px;
        text-align: center;
        font-weight: 700;
        background: #f4f4f4;
        vertical-align: middle;
    }

    /* =============================================
       PAGE BREAK — PERBAIKAN UTAMA
    ============================================= */

    /* Header tabel WAJIB ulang di setiap halaman */
    thead {
        display: table-header-group !important;
    }

    tfoot {
        display: table-footer-group !important;
    }

    /* Biarkan semua baris bisa terpotong antar halaman */
    tbody tr {
        page-break-inside: auto !important;
    }

    /* Khusus baris yang memang pendek, boleh dijaga agar tidak terpotong */
    tbody tr.no-page-break {
        page-break-inside: avoid !important;
    }

    /* =============================================
       SECTION APPROVAL
    ============================================= */
    .approval-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        margin-top: 20px;
        page-break-inside: avoid;
    }

    .approval-table td {
        border: none;
        text-align: center;
        padding: 4px;
        vertical-align: top;
        word-wrap: break-word;
        word-break: break-word;
    }

    /* =============================================
       PRINT / @page
    ============================================= */
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 8mm;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        table.cetak-hta {
            font-size: 6.5pt;
            page-break-inside: auto !important;
        }

        /* WAJIB: header tabel ulang tiap halaman saat print */
        thead {
            display: table-header-group !important;
        }

        tfoot {
            display: table-footer-group !important;
        }

        /* Semua baris boleh terpotong — ini kunci agar teks tidak hilang */
        tbody tr {
            page-break-inside: auto !important;
        }

        /* Sel tidak boleh clip/sembunyikan konten */
        table.cetak-hta td,
        table.cetak-hta th {
            overflow: visible !important;
            max-height: none !important;
        }

        .col-parameter {
            font-size: 6.5pt !important;
            width: 90px;
        }

        .col-deskripsi {
            font-size: 6.5pt !important;
            overflow: visible !important;
            max-height: none !important;
        }
    }

    .printed-info {
        margin-top: 20px;
        font-size: 7.5pt;
        color: #555;
        font-style: italic;
        text-align: right;
    }
</style>

<div class="cetak-hta-gpa-global">
    <h2 style="text-align:center; margin-bottom:16px; font-size:12pt;">PENILAIAN HTA / GPA</h2>

    {{-- Info Barang --}}
    <table class="cetak-hta cetak-hta-caption">
        <tr>
            <th style="width:100px; font-weight:bold;">Nama Barang</th>
            <td>{{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
        </tr>
        <tr>
            <th style="font-weight:bold;">Merek</th>
            <td>{{ $data->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
        </tr>
    </table>
    <table class="cetak-hta">
        <colgroup>
            <col style="width:14px;">
            <col style="width:1020px;">
            @foreach ($data->getVendor as $vIdx => $Vendor)
                <col>
                <col style="width:200px;">
                <col style="width:100px;">
                <col style="width:100px;">
                <col style="width:100px;">
                <col style="width:100px;">
                <col style="width:100px;">
                <col style="width:26px;">
            @endforeach
        </colgroup>

        <thead>
            <tr>
                <th rowspan="2" class="col-no" style="vertical-align:middle; text-align:center;">No</th>
                <th rowspan="2" class="col-parameter" style="text-align:center; vertical-align:middle;">Parameter
                </th>
                @foreach ($data->getVendor as $vIdx => $Vendor)
                    <th colspan="7" style="text-align:center;">
                        {{ $Vendor->getNamaVendor->Nama ?? 'Vendor ' . ($vIdx + 1) }}
                    </th>
                @endforeach
            </tr>
            <tr>
                @foreach ($data->getVendor as $vIdx => $Vendor)
                    <th class="col-deskripsi" style="text-align:center;">Deskripsi</th>
                    <th class="col-nilai">1</th>
                    <th class="col-nilai">2</th>
                    <th class="col-nilai">3</th>
                    <th class="col-nilai">4</th>
                    <th class="col-nilai">5</th>
                    <th class="col-subtotal">Sub<br>Total</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach ($data->getJenisPermintaan->getForm->Parameter as $key => $pm)
                <tr>
                    <td class="col-no" style="vertical-align:middle;">{{ $key + 1 }}</td>
                    <td class="col-parameter">{{ $parameter[$pm - 1]->Nama ?? '-' }}</td>
                    @foreach ($data->getVendor as $vIdx => $Vendor)
                        <td class="col-deskripsi">
                            {!! isset($data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key])
                                ? $data->getHtaGpa->getDetailHta[$vIdx]->Deskripsi[$key]
                                : '-' !!}
                        </td>
                        <td class="col-nilai">{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai1[$key] ?? '' }}</td>
                        <td class="col-nilai">{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai2[$key] ?? '' }}</td>
                        <td class="col-nilai">{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai3[$key] ?? '' }}</td>
                        <td class="col-nilai">{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai4[$key] ?? '' }}</td>
                        <td class="col-nilai">{{ $data->getHtaGpa->getDetailHta[$vIdx]->Nilai5[$key] ?? '' }}</td>
                        <td class="col-subtotal">
                            {{ $data->getHtaGpa->getDetailHta[$vIdx]->SubTotal[$key] ?? '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="2" style="text-align:right; vertical-align:middle;">Grand Total</th>
                @foreach ($data->getVendor as $vIdx => $Vendor)
                    @php
                        $grandTotal = 0;
                        if (
                            isset($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal) &&
                            is_array($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal)
                        ) {
                            foreach ($data->getHtaGpa->getDetailHta[$vIdx]->SubTotal as $sub) {
                                $grandTotal += is_numeric($sub) ? $sub : 0;
                            }
                        }
                    @endphp
                    <th colspan="7" style="text-align:right; background:#f4f4f4; font-weight:700;">
                        {{ $grandTotal }}
                    </th>
                @endforeach
            </tr>
        </tfoot>
    </table>
    <table class="cetak-hta">
        <colgroup>
            <col style="width:130px;">
            @foreach ($data->getVendor as $vIdx => $Vendor)
                <col>
            @endforeach
        </colgroup>
        <thead>
            <tr>
                <th class="col-parameter" style="text-align:center;">Parameter</th>
                @foreach ($data->getVendor as $vIdx => $Vendor)
                    <th style="text-align:center;">
                        {{ $Vendor->getNamaVendor->Nama ?? 'Vendor ' . ($vIdx + 1) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ([
        'UmurEkonomis' => 'Umur Ekonomis',
        'BuybackPeriod' => 'Buyback Period',
        'TarifDiusulkan' => 'Tarif Diusulkan',
        'TargetPemakaianBulanan' => 'Target Pemakaian Bulanan',
        'Keterangan' => 'Keterangan',
    ] as $field => $label)
                <tr>
                    <th class="col-parameter">{{ $label }}</th>
                    @foreach ($data->getVendor as $vIdx => $Vendor)
                        <td>{!! $data->getHtaGpa->getDetailHta[$vIdx]->{$field} ?? '-' !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

{{-- ============================================================
     SECTION PERSETUJUAN
============================================================ --}}
<h5 style="text-align:center; margin:20px 0 10px; font-size:10pt;">
    <strong>Persetujuan</strong>
</h5>

<table class="approval-table">
    <colgroup>
        @if (!empty($approval2))
            @foreach ($approval2 as $item)
                <col style="width:{{ 100 / count($approval2) }}%;">
            @endforeach
        @endif
    </colgroup>
    <tbody>
        {{-- Jabatan & Departemen --}}
        <tr>
            @foreach ($approval2 as $item)
                <td style="font-weight:600; vertical-align:bottom;">
                    {{ $item->NamaJabatan ?? '-' }}
                </td>
            @endforeach
        </tr>

        {{-- QR Code / Area Tanda Tangan --}}
        <tr>
            @foreach ($approval2 as $item)
                <td style="height:90px; vertical-align:bottom; padding-bottom:4px;">
                    @if ($item->Status == 'Approved' && isset($item->qrCode))
                        <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                            style="width:75px; height:75px; display:block; margin:0 auto;">
                    @endif
                </td>
            @endforeach
        </tr>

        {{-- Garis tanda tangan --}}
        <tr>
            @foreach ($approval2 as $item)
                <td style="padding:0 15%;">
                    <hr style="border:none; border-top:2px solid #000; margin:0 auto 3px;">
                </td>
            @endforeach
        </tr>

        {{-- Nama & Status --}}
        <tr>
            @foreach ($approval2 as $item)
                <td style="vertical-align:top; padding-top:3px;">
                    <span style="font-weight:600; display:block; text-align:center;">
                        {{ $item->Nama ?? '-' }}
                    </span>
                    <small style="display:block; text-align:center;">{{ $item->Status ?? '-' }}</small>
                    <small style="display:block; text-align:center;"><em>
                            {{ $item->TanggalApprove
                                ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') .
                                    ' ' .
                                    \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i')
                                : '-' }}
                        </em></small>
                </td>
            @endforeach
        </tr>
    </tbody>
</table>
@if (isset($approval2) && count($approval2) > 0)
    <div class="mt-3" style="padding: 12px; border-radius:4px;">
        <p style="font-weight:600; margin-bottom: 8px;">Justifikasi Persetujuan:</p>
        @php $nomor = 1; @endphp
        @foreach ($approval2 as $item)
            @if (!empty($item->Justifikasi))
                <p style="margin-bottom:10px; padding-left: 4px;">
                    <span style="display:inline-block; min-width:30px; font-weight:bold;">{{ $nomor++ }}.</span>
                    <span style="display:inline-block;">
                        <strong>Justifikasi:</strong> {{ $item->Justifikasi ?? '' }}<br>
                        <strong>Nama:</strong> {{ $item->Nama ?? '-' }}
                    </span>
                </p>
            @endif
        @endforeach
    </div>
@endif
<div class="printed-info">
    Dicetak oleh: {{ auth()->user()->name ?? '-' }} pada {{ now()->format('d-m-Y H:i') }}
</div>
