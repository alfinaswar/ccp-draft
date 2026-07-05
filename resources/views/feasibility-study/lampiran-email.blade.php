<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feasibility Study</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
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
    </style>
</head>

<body>
    @php
        function safe_number_format($value, $decimals = 0, $dec_point = ',', $thousands_sep = '.')
        {
            if (is_numeric($value)) {
                return number_format((float) $value, $decimals, $dec_point, $thousands_sep);
            }
            return '-';
        }
    @endphp

    <h3 class="center bold">FEASIBILITY STUDY</h3>
    <p class="bold">A. Rincian Data</p>
    <table>
        <tr>
            <td width="180px">Nama Alat</td>
            <td>:</td>
            <td>{{ $data->getBarang->Nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nilai Investasi</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->NilaiInvestasi) && is_numeric($data->NilaiInvestasi) ? 'Rp ' . safe_number_format($data->NilaiInvestasi) : '-' }}
            </td>
        </tr>
        <tr>
            <td>Spesifikasi</td>
            <td>:</td>
            <td>{!! $data->Spesifikasi ?? '-' !!}</td>
        </tr>
    </table>

    <p class="bold">B. Biaya</p>
    <table>
        <tr>
            <td colspan="3" class="bold">Biaya Tetap & Variable</td>
        </tr>
        <tr>
            <td width="180px">Bunga Tetap</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->BungaTetap) && is_numeric($data->BungaTetap) ? 'Rp ' . safe_number_format($data->BungaTetap) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px">Penyusutan</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->Penyusutan) && is_numeric($data->Penyusutan) ? 'Rp ' . safe_number_format($data->Penyusutan) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px">Maintenance</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->Maintenance) && is_numeric($data->Maintenance) ? 'Rp ' . safe_number_format($data->Maintenance) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px">Pegawai</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->Pegawai) && is_numeric($data->Pegawai) ? 'Rp ' . safe_number_format($data->Pegawai) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px">Sewa Gedung</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->SewaGedung) && is_numeric($data->SewaGedung) ? 'Rp ' . safe_number_format($data->SewaGedung) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px" class="bold">Total Biaya Tetap</td>
            <td width="5px" class="bold">:</td>
            <td class="bold">
                {{ isset($data->TotalBiayaTetap) && is_numeric($data->TotalBiayaTetap) ? 'Rp ' . safe_number_format($data->TotalBiayaTetap) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px">Konsumable</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->Konsumable) && is_numeric($data->Konsumable) ? 'Rp ' . safe_number_format($data->Konsumable) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="180px">Dokter</td>
            <td width="5px">:</td>
            <td>
                {{ isset($data->Dokter) && is_numeric($data->Dokter) ? 'Rp ' . safe_number_format($data->Dokter) : '-' }}
            </td>
        </tr>
    </table>


    <p class="bold">C. Tarif</p>
    <table>
        <tr>
            <td width="180px">Coding BPJS kit sebesar Rp</td>
            <td width="5px">:</td>
            <td>{{ $data->Tarif ?? '-' }}</td>
        </tr>
    </table>

    <p class="bold">D. Laba Rugi</p>
    <table class="table align-middle" id="tabel-rugi-laba">
        <thead class="table-light">
            <tr>
                <th>Keterangan</th>
                @for ($i = 1; $i <= 8; $i++)
                    <th>Tahun {{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php
                // Get the details as a collection, indexed by TahunKe (as integer for easy lookup)
                $details = $data->getFsDetail->keyBy(function ($item) {
                    return (int) $item->TahunKe;
                });
            @endphp
            <tr>
                <th>Tahun Ke</th>
                @for ($i = 1; $i <= 8; $i++)
                    <td>{{ $i }}</td>
                @endfor
            </tr>
            <tr>
                <td>Jml Pasien / Tindakan Umum</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        {{ !empty($details[$i]) ? $details[$i]->JumlahPasien ?? '-' : '-' }}
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Jml Pasien / Tindakan BPJS</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        {{ !empty($details[$i]) ? $details[$i]->JumlahPasienBpjs ?? '-' : '-' }}
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Tarif Umum</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->TarifUmum) && is_numeric($details[$i]->TarifUmum))
                            Rp {{ safe_number_format($details[$i]->TarifUmum) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Tarif BPJS</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->TarifBpjs) && is_numeric($details[$i]->TarifBpjs))
                            Rp {{ safe_number_format($details[$i]->TarifBpjs) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Revenue</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->Revenue) && is_numeric($details[$i]->Revenue))
                            Rp {{ safe_number_format($details[$i]->Revenue) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Total Biaya (Biaya Tetap + Variable)</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->TotalBiaya) && is_numeric($details[$i]->TotalBiaya))
                            Rp {{ safe_number_format($details[$i]->TotalBiaya) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Biaya Tetap</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->BiayaTetap) && is_numeric($details[$i]->BiayaTetap))
                            Rp {{ safe_number_format($details[$i]->BiayaTetap) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Biaya Variable</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->BiayaVariable) && is_numeric($details[$i]->BiayaVariable))
                            Rp {{ safe_number_format($details[$i]->BiayaVariable) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Net Profit</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->NetProfit) && is_numeric($details[$i]->NetProfit))
                            Rp {{ safe_number_format($details[$i]->NetProfit) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>EBITDA</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->Ebitda) && is_numeric($details[$i]->Ebitda))
                            Rp {{ safe_number_format($details[$i]->Ebitda) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>Akumulasi EBITDA</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->AkumEbitda) && is_numeric($details[$i]->AkumEbitda))
                            Rp {{ safe_number_format($details[$i]->AkumEbitda) }}
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td>ROI Tahun Ke-</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td>
                        @if (!empty($details[$i]) && isset($details[$i]->RoiTahunKe) && is_numeric($details[$i]->RoiTahunKe))
                            {{ safe_number_format($details[$i]->RoiTahunKe, 2) }} %
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
        </tbody>
    </table>

    <h5 class="text-center mb-4"><strong>Persetujuan Permintaan Pembelian</strong></h5>
    <table style="width:100%; margin: 0 auto; border:none;">
        <colgroup>
            @if (!empty($approval2))
                @foreach ($approval2 as $item)
                    <col style="width: {{ 100 / count($approval2) }}%;">
                @endforeach
            @endif
        </colgroup>
        <tbody>
            <tr>
                @foreach ($approval2 as $item)
                    <td style="text-align:center; font-weight:600; border:none;">
                        {{ $item->NamaJabatan ?? '-' }}
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval2 as $item)
                    <td class="text-center align-bottom" style="height: 20px; border:none;">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval2 as $item)
                    <td style="height: 70px; text-align:center; border:none;">
                        <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                            style="max-width:110px; max-height:60px;">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval2 as $item)
                    <td class="text-center" style="padding-bottom:0; border:none;">
                        <hr style="width: 70%; margin:0 auto 3px auto;border-top:2px solid #000;">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($approval2 as $item)
                    <td class="text-center align-top" style="border:none;">
                        <span style="font-weight:600; display: block; text-align: center;">
                            {{ $item->Nama ?? '-' }}
                        </span>
                        <div style="display: block; text-align: center;">
                            <small style="display: inline-block;">{{ $item->Status ?? '-' }}</small>
                            <br>
                            <small style="display: inline-block;">Approved</small>
                            <br>
                            <small><em>
                                    @if (!empty($item->TanggalApprove))
                                        {{ \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </em></small>
                        </div>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>

</html>
