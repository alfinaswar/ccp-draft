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
            font-size: 10px;
            margin-top: 2.5cm;
            margin-right: 1.5cm;
            margin-bottom: 1.5cm;
            margin-left: 1.5cm;
            color: #222;
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

        .info-section {
            margin-bottom: 22px;
        }

        .info-section table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
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
            margin-bottom: 1px;
            font-size: 10px;
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
            padding: 11px 6px 21px 6px;
            min-height: 38px;
            vertical-align: middle;
            position: relative;
        }

        .approval-box {
            font-size: 1em;
        }

        .sign-area {
            text-align: center;
            min-height: 48px;
            line-height: 1.15;
        }

        .qr-code img {
            width: 62px;
            height: 62px;
            margin-bottom: 0px;
        }

        .sign-area p {
            margin: 8px 0 0 0;
            font-weight: 500;
            letter-spacing: .5px;
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
                font-size: 10px;
            }
        }
    </style>
</head>


<body>

    <div class="watermark">
        <img src="{{ asset('assets/img/ccp/bgsurat/main-bg.png') }}" alt="watermark">
    </div>
    @include('rekomendasi-pembelian.rekap.hasil-rekomendasi')
    <div style="page-break-after: always;"></div>
    <div class="header">
        <h2 style="font-size: 16px;">LEMBARAN DISPOSISI PENGADAAN BARANG / JASA</h2>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td>Nama Barang / Jasa Yang Akan Dibeli</td>
                <td>
                    : {{ $MasterBarang['Nama'] ?? '-' }}
                    @if (!empty($MasterBarang->getMerk->Nama) || !empty($MasterBarang->Tipe))
                        / {{ $MasterBarang->getMerk->Nama ?? '-' }} / {{ $MasterBarang->Tipe ?? '-' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>:
                    {{ isset($disposisi['Harga']) ? 'Rp ' . number_format($disposisi['Harga'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            <tr>
                <td>Rencana Vendor</td>
                <td>: {{ $MasterVendor['Nama'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tujuan Penggunaan / Ruangan</td>
                <td>: {{ $disposisi['TujuanPenempatan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Form Permintaan Dari User</td>
                <td>:
                    @if (isset($disposisi['formPermintaan']))
                        {{ $disposisi['formPermintaan'] == 'Y' ? 'Ada' : 'Tidak' }}
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
                @if (!empty($approval[0]) && isset($approval[0]->Catatan))
                    {{ $approval[0]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($approval[0]))
                        @if ($approval[0]->Status == 'Approved' && isset($approval[0]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $approval[0]->qrCode }}" alt="QR Code">
                            </div>
                            <br>
                            <small><em>
                                    {{ $approval[0]->TanggalApprove ? \Carbon\Carbon::parse($approval[0]->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($approval[0]->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        @endif
                        @if (isset($approval[0]->Nama))
                            <p>{{ $approval[0]->Nama }}</p>
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
                @if (!empty($approval[1]) && isset($approval[1]->Catatan))
                    {{ $approval[1]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($approval[1]))
                        @if ($approval[1]->Status == 'Approved' && isset($approval[1]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $approval[1]->qrCode }}" alt="QR Code">
                            </div>
                            <br>
                            <small><em>
                                    {{ $approval[1]->TanggalApprove ? \Carbon\Carbon::parse($approval[1]->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($approval[1]->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        @endif
                        @if (isset($approval[1]->Nama))
                            <p>{{ $approval[1]->Nama }}</p>
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
                @if (!empty($approval[2]) && isset($approval[2]->Catatan))
                    {{ $approval[2]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($approval[2]))
                        @if ($approval[2]->Status == 'Approved' && isset($approval[2]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $approval[2]->qrCode }}" alt="QR Code">
                            </div>
                            <br>
                            <small><em>
                                    {{ $approval[2]->TanggalApprove ? \Carbon\Carbon::parse($approval[2]->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($approval[2]->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        @endif
                        @if (isset($approval[2]->Nama))
                            <p>{{ $approval[2]->Nama }}</p>
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
                @if (!empty($approval[3]) && isset($approval[3]->Catatan))
                    {{ $approval[3]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($approval[3]))
                        @if ($approval[3]->Status == 'Approved' && isset($approval[3]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $approval[3]->qrCode }}" alt="QR Code">
                            </div>
                            <br>
                            <small><em>
                                    {{ $approval[3]->TanggalApprove ? \Carbon\Carbon::parse($approval[3]->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($approval[3]->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        @endif
                        @if (isset($approval[3]->Nama))
                            <p>{{ $approval[3]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="approval-table">
        <tr>
            <th style="width: 70%;">PERSETUJUAN</th>
            <th style="width: 30%;">DIREKTUR RSAB GROUP</th>
        </tr>
        <tr>
            <td class="approval-box">
                @if (!empty($approval[4]) && isset($approval[4]->Catatan))
                    {{ $approval[4]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($approval[4]))
                        @if ($approval[4]->Status == 'Approved' && isset($approval[4]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $approval[4]->qrCode }}" alt="QR Code">
                            </div>
                            <br>
                            <small><em>
                                    {{ $approval[4]->TanggalApprove ? \Carbon\Carbon::parse($approval[4]->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($approval[4]->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        @endif
                        @if (isset($approval[4]->Nama))
                            <p>{{ $approval[4]->Nama }}</p>
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
                @if (!empty($approval[5]) && isset($approval[5]->Catatan))
                    {{ $approval[5]->Catatan }}
                @endif
            </td>
            <td class="approval-box">
                <div class="sign-area">
                    @if (!empty($approval[5]))
                        @if ($approval[5]->Status == 'Approved' && isset($approval[5]->qrCode))
                            <div class="qr-code">
                                <img src="data:image/png;base64,{{ $approval[5]->qrCode }}" alt="QR Code">
                            </div>
                            <br>
                            <small><em>
                                    {{ $approval[5]->TanggalApprove ? \Carbon\Carbon::parse($approval[5]->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($approval[5]->TanggalApprove)->format('H:i') : '-' }}
                                </em></small>
                        @endif
                        @if (isset($approval[5]->Nama))
                            <p>{{ $approval[5]->Nama }}</p>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        *) Coret jika tidak ada halaman lain
    </div>
</body>

</html>
