<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Penilaian GPA - {{ $data->KodePengajuan ?? '-' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14pt;
            font-weight: bold;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.info td {
            padding: 8px;
            vertical-align: top;
            border: 1px solid #333;
        }

        table.info td.label {
            width: 200px;
            font-weight: bold;
            background-color: #f4f4f4;
        }

        table.info td.value {
            background-color: #fff;
        }

        table.approval {
            width: 100%;
            margin: 30px auto 0 auto;
            border: none;
            border-collapse: collapse;
        }

        table.approval td {
            vertical-align: top;
            text-align: center;
            padding: 5px;
        }

        .jabatan {
            font-weight: 600;
            min-height: 20px;
        }

        .qr-container {
            height: 90px;
            vertical-align: top;
        }

        .qr-container img {
            width: 80px;
            height: 80px;
        }

        .sign-line {
            width: 70%;
            margin: 0 auto 3px auto;
            border-top: 2px solid #000;
        }

        .nama-approver {
            font-weight: 600;
            display: block;
            text-align: center;
        }

        .status-approver {
            display: block;
            text-align: center;
            font-size: 9pt;
        }

        .approve-time {
            display: block;
            text-align: center;
            font-size: 8pt;
            color: #555;
        }

        table.justifikasi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #333;
        }

        table.justifikasi th,
        table.justifikasi td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        table.justifikasi th {
            background-color: #f4f4f4;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>PENILAIAN GPA</h2>

    {{-- Tabel Informasi Utama --}}
    <table class="info">
        <tr>
            <td class="label">No Pengajuan</td>
            <td class="value">{{ $data->KodePengajuan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Alat / Barang</td>
            <td class="value">
                @if ($data->getPengajuanItem && $data->getPengajuanItem->count() > 0)
                    {{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Rencana Penempatan</td>
            <td class="value">
                @if ($data->getPermintaan && $data->getPermintaan->getDetail && $data->getPermintaan->getDetail->count() > 0)
                    {{ $data->getPermintaan->getDetail[0]->RencanaPenempatan ?? '-' }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    {{-- Tabel Persetujuan / TTD --}}
    @if (!empty($approval2) && count($approval2) > 0)
        <h5 style="text-align:center; margin-top:30px;"><strong>Persetujuan GPA</strong></h5>
        <table class="approval">
            <colgroup>
                @foreach ($approval2 as $item)
                    <col style="width: {{ 100 / count($approval2) }}%;">
                @endforeach
            </colgroup>
            <tbody>
                {{-- Jabatan --}}
                <tr>
                    @foreach ($approval2 as $item)
                        <td class="jabatan">
                            {{ $item->NamaJabatan ?? '-' }}
                        </td>
                    @endforeach
                </tr>
                {{-- Ruang TTD --}}
                <tr>
                    @foreach ($approval2 as $item)
                        <td style="height: 20px; border:none;">&nbsp;</td>
                    @endforeach
                </tr>
                {{-- QR Code --}}
                <tr>
                    @foreach ($approval2 as $item)
                        <td class="qr-container">
                            @if ($item->Status == 'Approved' && isset($item->qrCode))
                                <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code">
                            @endif
                        </td>
                    @endforeach
                </tr>
                {{-- Garis Tanda Tangan --}}
                <tr>
                    @foreach ($approval2 as $item)
                        <td style="padding-bottom:0; border:none;">
                            <div class="sign-line"></div>
                        </td>
                    @endforeach
                </tr>
                {{-- Nama, Status & Jam Approve --}}
                <tr>
                    @foreach ($approval2 as $item)
                        <td>
                            <span class="nama-approver">{{ $item->Nama ?? '-' }}</span>
                            <span class="status-approver">{{ $item->Status ?? '-' }}</span>
                            @if ($item->Status == 'Approved' && !empty($item->TanggalApprove))
                                <span class="approve-time">
                                    {{ \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') }}
                                </span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Tabel Justifikasi --}}
    @if (isset($approval2) && count($approval2) > 0)
        @php
            $justifikasiItems = $approval2->filter(function ($item) {
                return !empty($item->Justifikasi);
            });
        @endphp

        @if ($justifikasiItems->count() > 0)
            <table class="justifikasi">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Justifikasi</th>
                        <th style="width:200px;">Nama</th>
                    </tr>
                </thead>
                <tbody>
                    @php $nomor = 1; @endphp
                    @foreach ($justifikasiItems as $item)
                        <tr>
                            <td style="text-align:center;">{{ $nomor++ }}</td>
                            <td>{{ $item->Justifikasi ?? '-' }}</td>
                            <td>{{ $item->Nama ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</body>

</html>
