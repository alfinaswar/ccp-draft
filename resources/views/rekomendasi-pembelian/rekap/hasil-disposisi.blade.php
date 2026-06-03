<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Lembar Disposisi</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 8px;
            margin-top: 1.0cm;
            margin-right: 1.5cm;
            margin-bottom: 1.0cm;
            margin-left: 1.5cm;
            color: #000000;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
        }

        .watermark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 1.6em;
            margin: 0 0 3px 0;
            letter-spacing: 1px;
        }

        .header h3 {
            font-size: 1.15em;
            margin: 0;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .disposisi-label {
            display: inline-block;
            color: #222;
            font-weight: bold;
            font-size: 13px;
            padding: 2px 14px;
            border-radius: 6px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .info-section {
            margin-bottom: 22px;
        }

        .info-section table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .info-section td {
            border: none;
            padding: 4px 2px;
            vertical-align: top;
        }

        .info-section td:first-child {
            width: 43%;
            font-weight: bold;
            color: #000000;
        }

        table.approval-table,
        .approval-table th,
        .approval-table td {
            border: 1px solid #333;
            border-collapse: collapse;
        }

        table.approval-table {
            width: 100%;
            margin-bottom: 0px;
            font-size: 8px;
        }

        .approval-table th {
            background: #ffffff;
            color: #333;
            padding: 5px 0px;
            text-align: center;
            font-size: 1em;
            font-weight: 600;
        }

        .approval-table td {
            padding: 6px 6px 6px 6px;
            min-height: 38px;
            vertical-align: middle;
            position: relative;
        }

        .approval-box {
            font-size: 1em;
        }

        .sign-area {
            text-align: center;
            min-height: 20px;
            line-height: 1.0;
        }

        .qr-code img {
            width: 57px;
            height: 57px;
            margin-bottom: 0px;
        }

        .sign-area p {
            margin: 8px 0 0 0;
            font-weight: 500;
            letter-spacing: .5px;
            margin-bottom: 0px;
        }

        .footer-note {
            margin-top: 38px;
            font-size: 11px;
            font-style: italic;
            color: #666;
            text-align: left;
            letter-spacing: .2px;
        }

        @media print {
            .watermark {
                position: fixed;
            }

            .footer-note {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 1cm;
                font-size: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ asset('assets/img/ccp/bgsurat/main-bg.png') }}" alt="watermark">
    </div>
    <div class="header">

        <h2 style="font-size: 16px;">LEMBARAN DISPOSISI PENGADAAN BARANG / JASA</h2>
    </div>
    <div class="info-section">
        <table>
            <tr>
                <td>Nama Barang / Jasa Yang Akan Dibeli</td>
                <td>
                    : {{ $data['namaBarang'] ?? '-' }}
                    @if (!empty($data['merek']) || !empty($data['tipe']))
                        / {{ $data['merek'] ?? '-' }} / {{ $data['tipe'] ?? '-' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>:
                    {{ isset($data['harga']) ? 'Rp ' . number_format($data['harga'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            <tr>
                <td>Rencana Vendor</td>
                <td>: {{ $data['rencanaVendor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tujuan Penggunaan / Ruangan</td>
                <td>: {{ $data['tujuanPenempatan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Form Permintaan Dari User</td>
                <td>:
                    @if (isset($data['formPermintaan']))
                        {{ $data['formPermintaan'] == 'Y' ? 'Ada' : 'Tidak' }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">JUSTIFIKASI PEMBELIAN BARANG/JASA</th>
            <th style="width: 30%;">KADIV YANMED RS</th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($data['approval'][0]) && isset($data['approval'][0]->Catatan))
                    {{ $data['approval'][0]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($data['approval'][0]))
                        @if ($data['approval'][0]->Status == 'Approved' && isset($data['approval'][0]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $data['approval'][0]->qrCode }}" alt="QR Code">
                            </div>
                        @endif
                        @if (isset($data['approval'][0]->TanggalApprove))
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalApprove = \Carbon\Carbon::parse($data['approval'][0]->TanggalApprove);
                            @endphp
                            <div>
                                <small><i>{{ $tanggalApprove->translatedFormat('d F Y H:i') }}</i></small>
                            </div>
                        @endif
                        @if (isset($data['approval'][0]->Nama))
                            <p>{{ $data['approval'][0]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">JUSTIFIKASI PEMILIHAN BARANG/JASA</th>
            <th style="width: 30%;">KADIV JANGMED RS</th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($data['approval'][1]) && isset($data['approval'][1]->Catatan))
                    {{ $data['approval'][1]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($data['approval'][1]))
                        @if ($data['approval'][1]->Status == 'Approved' && isset($data['approval'][1]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $data['approval'][1]->qrCode }}" alt="QR Code">
                            </div>
                        @endif
                        @if (isset($data['approval'][1]->TanggalApprove))
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalApprove = \Carbon\Carbon::parse($data['approval'][1]->TanggalApprove);
                            @endphp
                            <div>
                                <small><i>{{ $tanggalApprove->translatedFormat('d F Y H:i') }}</i></small>
                            </div>
                        @endif
                        @if (isset($data['approval'][1]->Nama))
                            <p>{{ $data['approval'][1]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">PERSETUJUAN</th>
            <th style="width: 30%;">DIREKTUR RS</th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($data['approval'][2]) && isset($data['approval'][2]->Catatan))
                    {{ $data['approval'][2]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($data['approval'][2]))
                        @if ($data['approval'][2]->Status == 'Approved' && isset($data['approval'][2]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $data['approval'][2]->qrCode }}" alt="QR Code">
                            </div>
                        @endif
                        @if (isset($data['approval'][2]->TanggalApprove))
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalApprove = \Carbon\Carbon::parse($data['approval'][2]->TanggalApprove);
                            @endphp
                            <div>
                                <small><i>{{ $tanggalApprove->translatedFormat('d F Y H:i') }}</i></small>
                            </div>
                        @endif
                        @if (isset($data['approval'][2]->Nama))
                            <p>{{ $data['approval'][2]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">PERSETUJUAN</th>
            <th style="width: 30%;">GH PROCUREMENT</th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($data['approval'][3]) && isset($data['approval'][3]->Catatan))
                    {{ $data['approval'][3]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($data['approval'][3]))
                        @if ($data['approval'][3]->Status == 'Approved' && isset($data['approval'][3]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $data['approval'][3]->qrCode }}" alt="QR Code">
                            </div>
                        @endif
                        @if (isset($data['approval'][3]->TanggalApprove))
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalApprove = \Carbon\Carbon::parse($data['approval'][3]->TanggalApprove);
                            @endphp
                            <div>
                                <small><i>{{ $tanggalApprove->translatedFormat('d F Y H:i') }}</i></small>
                            </div>
                        @endif
                        @if (isset($data['approval'][3]->Nama))
                            <p>{{ $data['approval'][3]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">PERSETUJUAN</th>
            <th style="width: 30%;">
                @if (isset($data['rekomendasi']->getPerusahaan->Kategori) && $data['rekomendasi']->getPerusahaan->Kategori == 'CISCO')
                    CEO AB SISCO
                @else
                    DIREKTUR RSAB GROUP
                @endif
            </th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($data['approval'][4]) && isset($data['approval'][4]->Catatan))
                    {{ $data['approval'][4]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($data['approval'][4]))
                        @if ($data['approval'][4]->Status == 'Approved' && isset($data['approval'][4]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $data['approval'][4]->qrCode }}" alt="QR Code">
                            </div>
                        @endif
                        @if (isset($data['approval'][4]->TanggalApprove))
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalApprove = \Carbon\Carbon::parse($data['approval'][4]->TanggalApprove);
                            @endphp
                            <div>
                                <small><i>{{ $tanggalApprove->translatedFormat('d F Y H:i') }}</i></small>
                            </div>
                        @endif
                        @if (isset($data['approval'][4]->Nama))
                            <p>{{ $data['approval'][4]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">PERSETUJUAN</th>
            <th style="width: 30%;">CEO RSAB GROUP</th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($data['approval'][5]) && isset($data['approval'][5]->Catatan))
                    {{ $data['approval'][5]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($data['approval'][5]))
                        @if ($data['approval'][5]->Status == 'Approved' && isset($data['approval'][5]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $data['approval'][5]->qrCode }}" alt="QR Code">
                            </div>
                        @endif
                        @if (isset($data['approval'][5]->TanggalApprove))
                            @php
                                \Carbon\Carbon::setLocale('id');
                                $tanggalApprove = \Carbon\Carbon::parse($data['approval'][5]->TanggalApprove);
                            @endphp
                            <div>
                                <small><i>{{ $tanggalApprove->translatedFormat('d F Y H:i') }}</i></small>
                            </div>
                        @endif
                        @if (isset($data['approval'][5]->Nama))
                            <p>{{ $data['approval'][5]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
