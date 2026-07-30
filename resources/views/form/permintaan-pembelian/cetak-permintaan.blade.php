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

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 3.0cm 1cm;
            position: relative;
            min-height: 100vh;
        }

        .watermark {
            position: fixed;
            inset: 0;
            width: 21cm;
            height: 29.7cm;
            z-index: -10;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        th {
            text-align: center;
            font-weight: bold;
            white-space: nowrap;
        }

        .header h2,
        .header p {
            margin: 0;
            text-align: center;
        }

        .signature-section td {
            border: none !important;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            padding: 0 2px;
        }

        .signature-section hr {
            width: 70%;
            border: none;
            border-top: 2px solid #000;
            margin: 8px auto 3px auto;
        }

        .printed-info {
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
    <div class="watermark">
        <img src="{{ asset('assets/img/ccp/bgsurat/main-bg.png') }}" alt="" width="100%" height="100%">
    </div>
    <div class="header">
        <div class="title-section">
            <h2>PERMINTAAN PEMBELIAN</h2>
            <p>PURCHASE REQUESTION</p>
        </div>
    </div>

    <div class="form-info" style="margin-bottom:14px; margin-top: 1cm;">
        <table style="border: none !important;">
            <tr>
                <td style="border: none !important;">Unit</td>
                <td style="border: none !important;">: {{ $data->getDepartemen->Nama ?? '' }}</td>
            </tr>
            <tr>
                <td style="border: none !important;">Tanggal</td>
                <td style="border: none !important;">:
                    {{ !empty($data->Tanggal) ? \Carbon\Carbon::parse($data->Tanggal)->format('d-m-Y') : '' }}</td>
            </tr>
            <tr>
                <td style="border: none !important;">No.</td>
                <td style="border: none !important;">: {{ $data->NomorPermintaan ?? '' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:25%;">Nama Barang</th>
                <th style="width:10%;">Jumlah</th>
                <th style="width:10%;">Satuan</th>
                <th style="width:20%;">Nama dan Paraf User**</th>
                <th style="width:15%;">Rencana Pemanfaatan</th>
                <th style="width:15%;">Keterangan Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->getDetail as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $detail->getBarang->Nama ?? '' }}

                    </td>
                    <td>
                        {{ is_numeric($detail->Jumlah) ? number_format($detail->Jumlah, 0, ',', '.') : $detail->Jumlah }}
                    </td>
                    <td>
                        {{ $detail->getSatuan->NamaSatuan }}
                    </td>
                    <td>
                        @if (isset($data->getDiajukanOleh->name))
                            {{ $data->getDiajukanOleh->name }}
                        @endif
                    </td>
                    <td>
                        {{ $detail->RencanaPenempatan ?? '' }}
                    </td>
                    <td>
                        {{ $detail->Keterangan ?? '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section" style="margin-top:28px;">
        <table>
            <colgroup>
                @if (!empty($approval))
                    @foreach ($approval as $item)
                        <col style="width: {{ 100 / count($approval) }}%;">
                    @endforeach
                @endif
            </colgroup>
            <tbody>
                <tr>
                    <td style="font-weight:600;">Meminta</td>
                    <td style="font-weight:600;">Disetujui Oleh</td>
                </tr>
                <tr>
                    @foreach ($approval as $item)
                        <td style="vertical-align:middle;padding-bottom:0;">
                            <div style="display:flex;flex-direction:column;align-items:center;">
                                @if ($item->Status == 'Approved' && isset($item->qrCode))
                                    <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                        style="width:75px;height:75px;display:block;margin:0 auto;">
                                @endif
                                <hr>
                            </div>
                        </td>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($approval as $item)
                        <td style="vertical-align:top;">
                            <span style="font-weight:600;">{{ $item->Nama ?? '-' }}</span>
                            <div><small>{{ $item->Status ?? '-' }}</small></div>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div class="printed-info">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}<br>
        Dicetak oleh: {{ auth()->user()->name ?? '-' }}
    </div>
</body>

</html>
