<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Pembelian - RS Awal Bros</title>
    <style>
        @page {
            margin: 0;
        }

        .prp-watermark {
            position: fixed;
            inset: 0;
            width: 21cm;
            height: 29.7cm;
            z-index: -10;
        }

        .prp-table {
            border-collapse: collapse;
            width: 100%;
        }

        .prp-th,
        .prp-td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        .prp-th {
            text-align: center;
            font-weight: bold;
            white-space: nowrap;
        }

        .prp-header h2,
        .prp-header p {
            margin: 0;
            text-align: center;
        }

        .prp-signature-section td {
            border: none !important;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            padding: 0 2px;
        }

        .prp-signature-section hr {
            width: 70%;
            border: none;
            border-top: 2px solid #000;
            margin: 8px auto 3px auto;
        }

        .prp-printed-info {
            position: fixed;
            bottom: 1cm;
            left: 1cm;
            right: 1cm;
            margin: 0;
            font-size: 12px;
            color: #555;
            text-align: left;
            background: transparent;
            z-index: 999;
        }
    </style>
</head>

<body>
    <div class="prp-watermark">
        <img src="{{ asset('assets/img/ccp/bgsurat/main-bg.png') }}" alt="" width="100%" height="100%">
    </div>
    <div class="prp-header">
        <div class="prp-title-section">
            <h2>PERMINTAAN PEMBELIAN</h2>
            <p>PURCHASE REQUESTION</p>
        </div>
    </div>

    <div class="prp-form-info" style="margin-bottom:14px; margin-top: 1cm;">
        <table style="border: none !important;">
            <tr>
                <td style="border: none !important;">Unit</td>
                <td style="border: none !important;">: {{ $permintaan->getDepartemen->Nama ?? '' }}</td>
            </tr>
            <tr>
                <td style="border: none !important;">Tanggal</td>
                <td style="border: none !important;">:
                    {{ !empty($permintaan->Tanggal) ? \Carbon\Carbon::parse($permintaan->Tanggal)->format('d-m-Y') : '' }}
                </td>
            </tr>
            <tr>
                <td style="border: none !important;">No.</td>
                <td style="border: none !important;">: {{ $permintaan->NomorPermintaan ?? '' }}</td>
            </tr>
        </table>
    </div>

    <table class="prp-table">
        <thead>
            <tr>
                <th class="prp-th" style="width:5%;">No</th>
                <th class="prp-th" style="width:25%;">Nama Barang</th>
                <th class="prp-th" style="width:10%;">Jumlah</th>
                <th class="prp-th" style="width:10%;">Satuan</th>
                <th class="prp-th" style="width:20%;">Nama dan Paraf User**</th>
                <th class="prp-th" style="width:15%;">Rencana Pemanfaatan</th>
                <th class="prp-th" style="width:15%;">Keterangan Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permintaan->getDetail as $i => $detail)
                <tr>
                    <td class="prp-td">{{ $i + 1 }}</td>
                    <td class="prp-td">
                        {{ $detail->getBarang->Nama ?? '' }}
                    </td>
                    <td class="prp-td">
                        {{ is_numeric($detail->Jumlah) ? number_format($detail->Jumlah, 0, ',', '.') : $detail->Jumlah }}
                    </td>
                    <td class="prp-td">
                        {{ $detail->getBarang->getSatuan->NamaSatuan }}
                    </td>
                    <td class="prp-td">
                        @if (isset($permintaan->getDiajukanOleh->name))
                            {{ $permintaan->getDiajukanOleh->name }}
                        @endif
                    </td>
                    <td class="prp-td">
                        {{ $detail->RencanaPenempatan ?? '' }}
                    </td>
                    <td class="prp-td">
                        {{ $detail->Keterangan ?? '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="prp-signature-section" style="margin-top:28px;">
        <table style="border:none; margin-top:24px; width:100%;">
            <tbody>
                <tr>
                    <td class="fui-no-border" style="width:50%; vertical-align:top; text-align:center;">
                        @php
                            $left = $approval3[0] ?? null;
                        @endphp
                        @if ($left)
                            <div>
                                {{ $left->getJabatan->Nama ?? '-' }}<br>
                                {{ $left->getDepartemen->Nama ?? '' }}
                            </div>
                            <div
                                style="margin: 12px 0; min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                @if ($left->Status == 'Approved' && isset($left->qrCode))
                                    <img src="data:image/png;base64,{{ $left->qrCode }}" alt="QR Code"
                                        style="width:70px; height:70px; margin-bottom:4px;">
                                @else
                                    <div style="height:70px;"></div>
                                @endif
                                <hr style="width:70%; margin:6px auto 3px auto;">
                            </div>
                            <div>
                                <span><b>{{ $left->Nama ?? '-' }}</b></span>
                                <div><span class="fui-small">{{ $left->Status ?? '-' }}</span></div>
                            </div>
                        @endif
                    </td>
                    <td class="fui-no-border" style="width:50%; vertical-align:top; text-align:center;">
                        @php
                            $right = $approval3[1] ?? null;
                        @endphp
                        @if ($right)
                            <div>
                                {{ $right->getJabatan->Nama ?? '-' }}<br>
                                {{ $right->getDepartemen->Nama ?? '' }}
                            </div>
                            <div
                                style="margin: 12px 0; min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                @if ($right->Status == 'Approved' && isset($right->qrCode))
                                    <img src="data:image/png;base64,{{ $right->qrCode }}" alt="QR Code"
                                        style="width:70px; height:70px; margin-bottom:4px;">
                                @else
                                    <div style="height:70px;"></div>
                                @endif
                                <hr style="width:70%; margin:6px auto 3px auto;">
                            </div>
                            <div>
                                <span><b>{{ $right->Nama ?? '-' }}</b></span>
                                <div><span class="fui-small">{{ $right->Status ?? '-' }}</span></div>
                            </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="prp-printed-info">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}<br>
        Dicetak oleh: {{ auth()->user()->name ?? '-' }}
    </div>
</body>

</html>
