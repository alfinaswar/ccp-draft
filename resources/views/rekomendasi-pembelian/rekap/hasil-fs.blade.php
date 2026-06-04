<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feasibility Study</title>
    <style>
        .fs-body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 10px;
            margin-top: 1.5cm;
            margin-right: 0;
            margin-bottom: 0;
            margin-left: 0;
        }

        .fs-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px 0;
        }

        .fs-td,
        .fs-th {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .fs-nb {
            border: none;
        }

        .fs-center {
            text-align: center;
        }

        .fs-bold {
            font-weight: bold;
        }

        .fs-gray {
            background: #ddd;
        }

        .fs-print-footer {
            width: 100%;
            font-size: 8px;
            color: #444;
            text-align: right;
            margin-top: 12px;
        }

        .fs-watermark {
            position: fixed;
            inset: 0;
            z-index: -1;
        }

        .fs-watermark img {
            width: 100%;
            height: 100%;
        }

        .fs-text-center {
            text-align: center;
        }

        .fs-align-top {
            vertical-align: top;
        }
    </style>
</head>
<div class="fs-watermark">
    <img src="{{ url('assets/img/ccp/bgsurat/main-bg.png') }}" alt="">
</div>

<body class="fs-body">
    <h3 class="fs-center fs-bold">FEASIBILITY STUDY</h3>
    <p class="fs-bold">A. Rincian Data</p>
    <table class="fs-table">
        <tr>
            <td class="fs-td" width="100px">Nama Alat</td>
            <td class="fs-td">:</td>
            <td class="fs-td">
                {{ $datafs->getBarang->Nama ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td">Nilai Investasi</td>
            <td class="fs-td">:</td>
            <td class="fs-td">
                {{ isset($datafs->NilaiInvestasi) && $datafs->NilaiInvestasi !== null && is_numeric($datafs->NilaiInvestasi)
                    ? 'Rp ' . number_format((float) $datafs->NilaiInvestasi, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td">Spesifikasi</td>
            <td class="fs-td">:</td>
            <td class="fs-td">
                {!! $datafs->Spesifikasi ?? '-' !!}
            </td>
        </tr>
    </table>

    <p class="fs-bold">B. Biaya</p>
    <table class="fs-table">
        <tr>
            <td class="fs-td" colspan="3" class="fs-bold">Biaya Tetap & Variable</td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Bunga Tetap</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->BungaTetap) && $datafs->BungaTetap !== null && is_numeric($datafs->BungaTetap)
                    ? 'Rp ' . number_format((float) $datafs->BungaTetap, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Penyusutan</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->Penyusutan) && $datafs->Penyusutan !== null && is_numeric($datafs->Penyusutan)
                    ? 'Rp ' . number_format((float) $datafs->Penyusutan, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Maintenance</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->Maintenance) && $datafs->Maintenance !== null && is_numeric($datafs->Maintenance)
                    ? 'Rp ' . number_format((float) $datafs->Maintenance, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Pegawai</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->Pegawai) && $datafs->Pegawai !== null && is_numeric($datafs->Pegawai)
                    ? 'Rp ' . number_format((float) $datafs->Pegawai, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Sewa Gedung</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->SewaGedung) && $datafs->SewaGedung !== null && is_numeric($datafs->SewaGedung)
                    ? 'Rp ' . number_format((float) $datafs->SewaGedung, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td fs-bold" width="100px">Total Biaya Tetap</td>
            <td class="fs-td fs-bold" width="3px">:</td>
            <td class="fs-td fs-bold">
                {{ isset($datafs->TotalBiayaTetap) && $datafs->TotalBiayaTetap !== null && is_numeric($datafs->TotalBiayaTetap)
                    ? 'Rp ' . number_format((float) $datafs->TotalBiayaTetap, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Konsumable</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->Konsumable) && $datafs->Konsumable !== null && is_numeric($datafs->Konsumable)
                    ? 'Rp ' . number_format((float) $datafs->Konsumable, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
        <tr>
            <td class="fs-td" width="100px">Dokter</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->Dokter) && $datafs->Dokter !== null && is_numeric($datafs->Dokter)
                    ? 'Rp ' . number_format((float) $datafs->Dokter, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
    </table>

    <p class="fs-bold">C. Tarif</p>
    <table class="fs-table">
        <tr>
            <td class="fs-td" width="100px">Coding BPJS kit sebesar Rp</td>
            <td class="fs-td" width="3px">:</td>
            <td class="fs-td">
                {{ isset($datafs->Tarif) && $datafs->Tarif !== null && is_numeric($datafs->Tarif)
                    ? 'Rp ' . number_format((float) $datafs->Tarif, 0, ',', '.')
                    : 'Rp 0' }}
            </td>
        </tr>
    </table>

    <p class="fs-bold">D. Laba Rugi</p>
    <table class="fs-table" style="font-size: 7px;">
        <tr class="fs-gray fs-bold fs-center">
            <td class="fs-td">Keterangan</td>
            @php
                $romawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];
            @endphp
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">Tahun {{ $romawi[$i] }}</td>
            @endfor
        </tr>
        @php
            $details = $datafs->getFsDetail->keyBy(function ($item) {
                return (int) $item->TahunKe;
            });
            // Avoid "Cannot redeclare fsDetailVal()" by checking if function exists
            if (!function_exists('fsDetailVal')) {
                function fsDetailVal($details, $tahun, $field, $decimal = 0)
                {
                    if (
                        !empty($details[$tahun]) &&
                        isset($details[$tahun]->$field) &&
                        $details[$tahun]->$field !== null
                    ) {
                        if (is_numeric($details[$tahun]->$field)) {
                            return number_format((float) $details[$tahun]->$field, $decimal, ',', '.');
                        }
                        return $details[$tahun]->$field;
                    } else {
                        // Untuk nilai number, default Rp 0
                        $fieldsRp = [
                            'TarifUmum',
                            'TarifBpjs',
                            'Revenue',
                            'Biaya',
                            'BiayaTetap',
                            'BiayaVariable',
                            'NetProfit',
                            'Ebitda',
                            'AkumulasiEbitda',
                        ];
                        return in_array($field, $fieldsRp) ? '0' : '-';
                    }
                }
            }
        @endphp
        <tr>
            <td class="fs-td">Jumlah Tindakan BPJS</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">{{ fsDetailVal($details, $i, 'JumlahPasienBpjs') }}</td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Tarif Umum</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'TarifUmum');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Tarif BPJS</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'TarifBpjs');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Revenue</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'Revenue');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Biaya</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'Biaya');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Biaya Tetap</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'BiayaTetap');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Biaya Variable</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'BiayaVariable');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Net Profit</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'NetProfit');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">EBITDA</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'Ebitda');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">Akumulasi EBITDA</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">
                    @php
                        $val = fsDetailVal($details, $i, 'AkumulasiEbitda');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : ($val === '0' ? 'Rp 0' : $val) }}
                </td>
            @endfor
        </tr>
        <tr>
            <td class="fs-td">ROI Tahun Ke-</td>
            @for ($i = 1; $i <= 8; $i++)
                <td class="fs-td">{{ fsDetailVal($details, $i, 'RoiTahunKe') }} %</td>
            @endfor
        </tr>
    </table>
    <table class="fs-table"
        style="max-width:100%; margin: 0 auto; border-collapse:collapse; border:none; text-align:center;">
        <colgroup>
            @if (!empty($approvalfS))
                @foreach ($approvalfS as $item)
                    <col style="width: {{ 100 / count($approvalfS) }}%;">
                @endforeach
            @endif
        </colgroup>
        <tbody>
            <tr>
                @foreach ($approvalfS as $item)
                    <td class="fs-text-center" style="border:none; vertical-align:bottom; padding-bottom:3px;">
                        <div style="font-weight:600;">
                            {{ $item->getJabatan->Nama ?? '-' }}
                            {{ $item->getDepartemen->Nama ?? '-' }}
                        </div>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalfS as $item)
                    <td class="fs-text-center" style="height:70px; vertical-align: top; border:none;">
                        @if ($item->Status == 'Approved' && isset($item->qrCode))
                            <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                style="width:70px; height:70px;">
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalfS as $item)
                    <td class="fs-text-center" style="padding-bottom:0; border:none;">
                        <hr style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approvalfS as $item)
                    <td class="fs-text-center fs-align-top" style="border:none;">
                        <span style="font-weight:600;">
                            {{ $item->Nama ?? '-' }}
                        </span>
                        <br>
                        <small>{{ $item->Status ?? '-' }}</small>
                        <br>
                        <small><em>
                                {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                            </em></small>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{-- Print info footer --}}
    <div class="fs-print-footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }},
        {{ \Carbon\Carbon::now()->format('H:i') }}
        <br>
        Oleh: {{ auth()->user()->name ?? '-' }}
    </div>
</body>

</html>
