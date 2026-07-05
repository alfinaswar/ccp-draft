<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feasibility Study</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 10px;
            margin-top: 1.5cm;
            margin-right: 0;
            margin-bottom: 0;
            margin-left: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px 0;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .nb {
            border: none;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .gray {
            background: #ddd;
        }

        .print-footer {
            width: 100%;
            font-size: 8px;
            color: #444;
            text-align: right;
            margin-top: 12px;
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
<div class="watermark">
    <img src="{{ url('assets/img/ccp/bgsurat/main-bg.png') }}" alt="">
</div>

@php
    function nf_safe($value, $decimal = 0, $dec_point = ',', $thousands_sep = '.')
    {
        // Converts value to float if possible, else returns '-'
        if (is_numeric($value)) {
            return number_format((float) $value, $decimal, $dec_point, $thousands_sep);
        }
        return '-';
    }
@endphp

<body>
    <h3 class="center bold">FEASIBILITY STUDY</h3>
    <p class="bold">A. Rincian Data</p>
    <table>
        <tr>
            <td width="100px">Nama Alat</td>
            <td>:</td>
            <td>
                {{ $data->getBarang->Nama ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>Nilai Investasi</td>
            <td>:</td>
            <td>
                {{ isset($data->NilaiInvestasi) && is_numeric($data->NilaiInvestasi) ? 'Rp ' . nf_safe($data->NilaiInvestasi, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td>Spesifikasi</td>
            <td>:</td>
            <td>
                {!! $data->Spesifikasi ?? '-' !!}
            </td>
        </tr>
    </table>

    <p class="bold">B. Biaya</p>
    <table>
        <tr>
            <td colspan="3" class="bold">Biaya Tetap & Variable</td>
        </tr>
        <tr>
            <td width="100px">Bunga Tetap</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->BungaTetap) && is_numeric($data->BungaTetap) ? 'Rp ' . nf_safe($data->BungaTetap, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px">Penyusutan</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->Penyusutan) && is_numeric($data->Penyusutan) ? 'Rp ' . nf_safe($data->Penyusutan, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px">Maintenance</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->Maintenance) && is_numeric($data->Maintenance) ? 'Rp ' . nf_safe($data->Maintenance, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px">Pegawai</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->Pegawai) && is_numeric($data->Pegawai) ? 'Rp ' . nf_safe($data->Pegawai, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px">Sewa Gedung</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->SewaGedung) && is_numeric($data->SewaGedung) ? 'Rp ' . nf_safe($data->SewaGedung, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px" class="bold">Total Biaya Tetap</td>
            <td width="3px" class="bold">:</td>
            <td class="bold">
                {{ isset($data->TotalBiayaTetap) && is_numeric($data->TotalBiayaTetap) ? 'Rp ' . nf_safe($data->TotalBiayaTetap, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px">Konsumable</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->Konsumable) && is_numeric($data->Konsumable) ? 'Rp ' . nf_safe($data->Konsumable, 0, ',', '.') : '-' }}
            </td>
        </tr>
        <tr>
            <td width="100px">Dokter</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->Dokter) && is_numeric($data->Dokter) ? 'Rp ' . nf_safe($data->Dokter, 0, ',', '.') : '-' }}
            </td>
        </tr>
    </table>

    <p class="bold">C. Tarif</p>
    <table>
        <tr>
            <td width="100px">Coding BPJS kit sebesar Rp</td>
            <td width="3px">:</td>
            <td>
                {{ isset($data->Tarif) && is_numeric($data->Tarif) ? 'Rp ' . nf_safe($data->Tarif, 0, ',', '.') : '-' }}
            </td>
        </tr>
    </table>

    <p class="bold">D. Laba Rugi</p>
    <table style="font-size: 7px;">
        <tr class="gray bold center">
            <td>Keterangan</td>
            @php
                $romawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];
            @endphp
            @for ($i = 1; $i <= 8; $i++)
                <td>Tahun {{ $romawi[$i] }}</td>
            @endfor
        </tr>
        @php
            $details = $data->getFsDetail->keyBy(function ($item) {
                return (int) $item->TahunKe;
            });
            function fsDetailVal($details, $tahun, $field, $decimal = 0)
            {
                if (!empty($details[$tahun]) && isset($details[$tahun]->$field) && $details[$tahun]->$field !== null) {
                    if (is_numeric($details[$tahun]->$field)) {
                        return number_format((float) $details[$tahun]->$field, $decimal, ',', '.');
                    }
                    return $details[$tahun]->$field;
                } else {
                    return '-';
                }
            }
        @endphp
        <tr>
            <td>Jumlah Tindakan BPJS</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>{{ fsDetailVal($details, $i, 'JumlahPasienBpjs') }}</td>
            @endfor
        </tr>
        <tr>
            <td>Tarif Umum</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'TarifUmum');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Tarif BPJS</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'TarifBpjs');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Revenue</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'Revenue');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Biaya</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'Biaya');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Biaya Tetap</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'BiayaTetap');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Biaya Variable</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'BiayaVariable');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Net Profit</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'NetProfit');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>EBITDA</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'Ebitda');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>Akumulasi EBITDA</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>
                    @php
                        $val = fsDetailVal($details, $i, 'AkumulasiEbitda');
                    @endphp
                    {{ is_numeric(str_replace('.', '', $val)) ? 'Rp ' . $val : $val }}
                </td>
            @endfor
        </tr>
        <tr>
            <td>ROI Tahun Ke-</td>
            @for ($i = 1; $i <= 8; $i++)
                <td>{{ fsDetailVal($details, $i, 'RoiTahunKe') }} %</td>
            @endfor
        </tr>
    </table>
    <table style="max-width:100%; margin: 0 auto; border-collapse:collapse; border:none; text-align:center;">
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
                    <td class="text-center" style="border:none; vertical-align:bottom; padding-bottom:3px;">
                        <div style="font-weight:600;">
                            {{ $item->NamaJabatan ?? '-' }}
                        </div>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $item)
                    <td class="text-center" style="height:70px; vertical-align: top; border:none;">
                        @if ($item->Status == 'Approved' && isset($item->qrCode))
                            <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                style="width:70px; height:70px;">
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $item)
                    <td class="text-center" style="padding-bottom:0; border:none;">
                        <hr style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $item)
                    <td class="text-center align-top" style="border:none;">
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
    <div class="print-footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }},
        {{ \Carbon\Carbon::now()->format('H:i') }}
        <br>
        Oleh: {{ auth()->user()->name ?? '-' }}
    </div>
</body>

</html>
