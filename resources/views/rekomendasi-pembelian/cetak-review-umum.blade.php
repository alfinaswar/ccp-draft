<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekomendasi CCP - Cetak Per Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin-top: 2.5cm;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
            text-decoration: underline;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #444;
            padding: 6px 8px;
        }

        .main-table th {
            background: #eee;
            font-weight: bold;
            text-align: left;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 60px;
            font-size: 13px;
        }

        .signature-table td {
            border: 1px solid #444;
            padding: 16px 8px 6px 8px;
            text-align: center;
            vertical-align: middle;
            height: 100px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 2px;
            display: block;
        }

        .signature-sub {
            font-size: 13px;
            font-weight: 600;
        }

        .watermark {
            position: fixed;
            inset: 0;
            z-index: -1;
        }

        .watermark img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ url('assets/img/ccp/bgsurat/main-bg.png') }}" alt="">
    </div>
    <div class="title">
        REKOMENDASI PEMBELIAN BARANG
    </div>

    <table class="main-table" style="margin-bottom: 45px;">
        <tr>
            <td style="width: 100px;">RS yang Meminta</td>
            <td colspan="{{ max(1, $rekomendasi->getRekomedasiDetail->count()) }}">
                <strong>
                    {{ $rekomendasi->getRekomedasiDetail[0]->getPerusahaan->NamaLengkap ?? '-' }}
                </strong>
            </td>
        </tr>
        <tr>
            <td>Nama Vendor</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->getNamaVendor->Nama ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Nama Barang</td>
            @foreach ($rekomendasi->getPengajuan->getVendor as $item)
                @foreach ($item->getVendorDetail as $detail)
                    <td>
                        {{ $detail->getNamaBarang->Nama ?? '-' }} / {{ $detail->getNamaBarang->getMerk->Nama ?? '-' }} /
                        {{ $detail->getNamaBarang->Tipe ?? '-' }}
                    </td>
                @endforeach
            @endforeach
        </tr>
        <tr>
            <td>Harga Awal</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td style="text-align: right;">
                    @if ($item->HargaAwal)
                        Rp
                        {{ is_numeric($item->HargaAwal) ? number_format($item->HargaAwal, 0, ',', '.') : $item->HargaAwal }}
                    @else
                        -
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Harga Nego</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td style="text-align: right;">
                    @if ($item->HargaNego)
                        Rp
                        {{ is_numeric($item->HargaNego) ? number_format($item->HargaNego, 0, ',', '.') : $item->HargaNego }}
                    @else
                        -
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Spesifikasi</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td style="vertical-align: top;">
                    {!! $item->Spesifikasi ?? '-' !!}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Populasi</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {!! $item->Populasi ?? '-' !!}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Garansi</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->Garansi ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Time Line Pekerjaan</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->TimeLinePekerjaan ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Jumlah Pekerja</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->JumlahPekerja ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Luasan</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->Luasan ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Review Vendor</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->ReviewVendor ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Top</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->Top ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Rekomendasi</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->Rekomendasi ?? '-' }}
                </td>
            @endforeach
        </tr>
        <tr>
            <td>Keterangan</td>
            @foreach ($rekomendasi->getRekomedasiDetail as $item)
                <td>
                    {{ $item->Keterangan ?? '-' }}
                </td>
            @endforeach
        </tr>
    </table>

    <div style="margin-top: 40px;">
        <div style="text-align: right;">
            <span>
                Pekanbaru,
                {{ isset($rekomendasi->getRekomedasiDetail[0]->created_at)
                    ? \Carbon\Carbon::parse($rekomendasi->getRekomedasiDetail[0]->created_at)->format('d-m-Y')
                    : date('d-m-Y') }}
            </span>
        </div>
        <table class="signature-table"
            style="width: 100%; table-layout: fixed; font-family: Arial, sans-serif; font-weight: normal; font-size:10px;">
            <tr>
                <td style="text-align: center; vertical-align: bottom; width: 50%; height: 110px; padding-bottom:0;">
                    <span class="signature-label"
                        style="display: block; margin-bottom: 6px; font-family: Arial, sans-serif; font-weight: normal; font-size:10px;">
                        Yang Menegosiasi</span>
                    <div
                        style="height:60px; display: flex; align-items: flex-end; justify-content: center; margin-bottom:6px;">
                        @if (!is_null($rekomendasi->UserNego) && !empty($rekomendasi->qrCodeNego))
                            <img src="data:image/png;base64,{{ $rekomendasi->qrCodeNego }}" alt="QR Code"
                                style="width:60px; height:60px;">
                        @endif

                    </div>

                    <span style="display: block; font-size:10px;">
                        {{ $rekomendasi->getUserNego->name ?? '-' }}
                    </span>
                    <hr style="width:70%; border: 0; border-top: 1.3px solid #aaa;">
                    <div
                        style="text-align: center; font-size: 9px; font-family: Arial, sans-serif; margin-bottom: 10px; font-weight: normal;">
                        <small>
                            <em>Dibuat dan disetujui pada</em>
                            <br>
                            <span style="font-weight: normal;">
                                <em>
                                    {{ isset($rekomendasi->created_at) ? \Carbon\Carbon::parse($rekomendasi->created_at)->translatedFormat('d F Y, H:i') : '-' }}
                                </em>
                            </span>
                        </small>
                    </div>
                </td>
                <td style="text-align: center; vertical-align: bottom; width: 50%; height: 110px; padding-bottom:0;">
                    <span class="signature-label"
                        style="display: block; margin-bottom: 2px; font-family: Arial, sans-serif; font-weight: normal; font-size:10px;">
                        Disetujui</span>
                    <span class="signature-sub"
                        style="display: block; margin-bottom: 4px; font-family: Arial, sans-serif; font-weight: normal; font-size:10px;">
                        Procurement Group</span>
                    <div
                        style="height:60px; display: flex; align-items: flex-end; justify-content: center; margin-bottom:6px;">
                        @if (!empty($rekomendasi->qrCodeApprove))
                            <img src="data:image/png;base64,{{ $rekomendasi->qrCodeApprove }}" alt="QR Code"
                                style="width:60px; height:60px;">
                        @endif
                    </div>
                    <span style="display: block; font-size:10px;">
                        {{ $rekomendasi->getDisetujuiOleh->name ?? '-' }}
                    </span>
                    <hr style="width:70%; border: 0; border-top: 1.3px solid #aaa;">
                    <div
                        style="text-align: center; font-size: 9px; font-family: Arial, sans-serif; margin-bottom: 10px; font-weight: normal;">
                        <small>
                            <em>Dibuat dan disetujui pada</em>
                            <br>
                            <span style="font-weight: normal;">
                                <em>
                                    {{ isset($rekomendasi->DisetujuiPada) ? \Carbon\Carbon::parse($rekomendasi->DisetujuiPada)->translatedFormat('d F Y, H:i') : '-' }}
                                </em>
                            </span>
                        </small>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
