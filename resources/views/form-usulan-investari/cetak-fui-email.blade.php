<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir Usulan Investasi - RS Awal Bros</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 3cm 1cm;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            font-size: inherit;
        }

        th {
            background: #f1f1f1;
        }

        .header,
        .kop-surat,
        .footer-note {
            text-align: center;
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

        .no-border {
            border: none !important;
        }

        .no-top-border {
            border-top: none !important;
        }

        .no-bottom-border {
            border-bottom: none !important;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        small {
            font-size: 9px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ url('assets/img/ccp/bgsurat/main-bg.png') }}" alt="">
    </div>
    <div>
        <div class="header" style="font-size:16px; margin-bottom: 20px;">
            <h3 style="margin:0; font-size:16px;">FORMULIR USULAN INVESTASI</h3>
            <div style="font-size:14px;">{{ $data->getNamaForm->Nama ?? '-' }}</div>
        </div>
        <table>
            <tr>
                <td colspan="6" style="background:#f3f3f3">
                    <b>Diisi lengkap oleh pemohon, setelah ditandatangani Atasan dan Direktur, kemudian diserahkan ke
                        Bagian Pembelian Barang / Jasa Medik dan Umum :</b>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="background:#f9f9f9;"><b>Diisi oleh Pemohon (Departemen terkait)</b></td>
                <td colspan="3" style="background:#f9f9f9;"><b>Diisi oleh Bagian Pembelian</b></td>
            </tr>
            <tr>
                <td style="width:13%;">Tanggal</td>
                <td style="width:2%;">:</td>
                <td style="width:18%;">
                    {{ $data->Tanggal ? \Carbon\Carbon::parse($data->Tanggal)->translatedFormat('d F Y') : '-' }}</td>
                <td style="width:13%;">Tanggal</td>
                <td style="width:2%;">:</td>
                <td style="width:18%;">
                    {{ $data->Tanggal2 ? \Carbon\Carbon::parse($data->Tanggal2)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Nama Kepala Divisi</td>
                <td>:</td>
                <td>{{ $data->getKadiv->name ?? '-' }}</td>
                <td>Nama Kepala Divisi</td>
                <td>:</td>
                <td>{{ $data->getKadiv2->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Divisi</td>
                <td>:</td>
                <td>{{ $data->getDepartemen->Nama ?? '-' }}</td>
                <td>Divisi</td>
                <td>:</td>
                <td>{{ $data->getDepartemen2->Nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ $data->Kategori ?? '-' }}</td>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ $data->Kategori ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="6"><i>* Pilih salah satu</i></td>
            </tr>
        </table>
        <div style="margin-top: 18px; margin-bottom: 6px;">
            Dengan ini kami ajukan permohonan untuk pengadaan barang/jasa dengan alasan sebagai berikut:
        </div>
        <div style="margin-bottom: 20px;">{{ $data->Alasan ?? '-' }}</div>
        <table class="table align-middle" width="100%">
            <thead class="table-light">
                <tr>
                    <th style="width:2%">No</th>
                    <th style="width:20%">Nama Barang / Jasa</th>
                    <th>Vendor</th>
                    <th>Harga Awal</th>
                    <th>Harga Nego</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    if (!function_exists('rupiah')) {
                        function rupiah($angka)
                        {
                            return 'Rp ' . number_format($angka, 0, ',', '.');
                        }
                    }
                @endphp
                @forelse (($dataRekom->getRekomedasiDetail ?? []) as $key => $rekomDetail)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            {{ $rekomDetail->getBarang->Nama ?? '-' }}
                        </td>
                        <td>
                            {{ $rekomDetail->getNamaVendor->Nama ?? '-' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaAwal) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaAwal ?? 0)) : 'Rp 0' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaNego) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego ?? 0)) : 'Rp 0' }}
                        </td>
                        <td>
                            {{ rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego ?? 0)) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada item.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div>
            <b>Catatan:</b><br>
            1. Lampiran quotation diatas Rp 100.000.000<br>
            2. Untuk nominal di atas Rp 100.000.000 lampirkan MTA/KFA<br>
            3. Penawaran harga yang telah disetujui, dan pembanding antara vendor. Jika tidak ada vendor pembanding,
            mohon jelaskan alasannya
        </div>
        <br>
        <table class="table align-middle" width="100%">
            <thead class="table-light">
                <tr>
                    <th style="width:2%">No</th>
                    <th style="width:20%">Nama Barang / Jasa</th>
                    <th>Vendor</th>
                    <th>Harga Awal</th>
                    <th>Harga Nego</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    if (!function_exists('rupiah')) {
                        function rupiah($angka)
                        {
                            return 'Rp ' . number_format($angka, 0, ',', '.');
                        }
                    }
                    $rekomItems = collect($dataRekom->getRekomedasiDetail ?? [])
                        ->where('Rekomendasi', 1)
                        ->values();
                @endphp
                @forelse ($rekomItems as $key => $rekomDetail)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            {{ $rekomDetail->getBarang->Nama ?? '-' }}
                        </td>
                        <td>
                            {{ $rekomDetail->getNamaVendor->Nama ?? '-' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaAwal) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaAwal ?? 0)) : 'Rp 0' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaNego) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego ?? 0)) : 'Rp 0' }}
                        </td>
                        <td>
                            {{ rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego ?? 0)) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada item.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div>
            <div>
                <span>Sudah masuk RKAP dari departemen ybs:</span>
                @if (($data->SudahRkap ?? null) == 'Y')
                    <span>Ya, Sudah</span>
                @elseif (($data->SudahRkap ?? null) == 'N')
                    <span>Tidak, Belum</span>
                @else
                    <span>-</span>
                @endif
            </div>
            <div>
                Sisa Budget dari RKAP untuk tahun ini yang masih dapat dipergunakan Rp.
                <u>{{ number_format($data->SisaBudget ?? 0, 0, ',', '.') }}</u> [diisi oleh departemen terkait]
            </div>
            <div style="margin-top:12px;"><b>Verifikasi keuangan:</b></div>
            <div>
                <span>Sudah masuk RKAP dari departemen ybs:</span>
                @if (($data->SudahRkap2 ?? null) == 'Y')
                    <span>Ya, Sudah</span>
                @elseif (($data->SudahRkap2 ?? null) == 'N')
                    <span>Tidak, Belum</span>
                @else
                    <span>-</span>
                @endif
            </div>
            <div>
                Sisa Budget dari RKAP untuk tahun ini yang masih dapat dipergunakan Rp.
                <u>{{ number_format($data->SisaBudget2 ?? 0, 0, ',', '.') }}</u>
            </div>
        </div>
        <table style="border:none; margin-top:24px;">
            <colgroup>
                {{-- {{ dd($approval2) }} --}}
                @php
                    $approval2Count =
                        is_iterable($approval2 ?? []) && !is_string($approval2 ?? [])
                            ? (is_countable($approval2 ?? [])
                                ? count($approval2 ?? [])
                                : iterator_count($approval2 ?? []))
                            : 0;
                @endphp
                @if (!empty($approval2 ?? []) && $approval2Count > 0)
                    @foreach ($approval2 as $item)
                        <col style="width:{{ 100 / $approval2Count }}%;">
                    @endforeach
                @endif
            </colgroup>
            <tbody>
                <tr>
                    @php
                        $showApprovalList2 = false;
                        $approvalList2 = [];
                        $totalNego2 = 0;

                        $jenisP = $data2->getPerusahaan->Kategori ?? null;
                        if (
                            isset($data2) &&
                            isset($data2->Jenis) &&
                            !empty($dataRekom ?? null) &&
                            isset($dataRekom->getRekomedasiDetail)
                        ) {
                            $rekomendasiSatu = ($dataRekom->getRekomedasiDetail ?? collect())->first(function ($item) {
                                return isset($item->Rekomendasi) && $item->Rekomendasi == 1;
                            });
                            $totalNego = $rekomendasiSatu->HargaNego ?? 0;
                            $showApprovalList = true;

                            if ($data2->Jenis == 1) {
                                if ($totalNego < 50000000) {
                                    $approvalList = ['Kepala Divisi JangMed', 'Direktur'];
                                } elseif ($totalNego >= 50000000 && $totalNego <= 100000000) {
                                    $approvalList = [
                                        'Kepala Divisi Jangmed',
                                        'Direktur RS',
                                        $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                    ];
                                } else {
                                    $approvalList = [
                                        'Kepala Divisi JangMed',
                                        'Direktur RS',
                                        'GH Keuangan & Akt. RS. Awal Bros Group',
                                        $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                        'CEO RS. Awal Bros Group',
                                    ];
                                }
                            } else {
                                if ($totalNego < 50000000) {
                                    $approvalList = ['Kepala Divisi Umum', 'Direktur'];
                                } elseif ($totalNego >= 50000000 && $totalNego <= 100000000) {
                                    $approvalList = [
                                        'Kepala Divisi Umum',
                                        'Direktur RS',
                                        $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                    ];
                                } else {
                                    $approvalList = [
                                        'Kepala Divisi Umum',
                                        'Direktur RS',
                                        'GH Keuangan & Akt. RS. Awal Bros Group',
                                        $jenisP == 'CISCO' ? 'CEO AB Sisco' : 'Direktur RS. Awal Bros Group',
                                        'CEO RS. Awal Bros Group',
                                    ];
                                }
                            }
                        }

                    @endphp

                    @if (($showApprovalList2 ?? false) && !empty($approvalList2 ?? []))
                        @foreach ($approval2 ?? [] as $item)
                            <td class="text-center no-border" style="font-weight:600;">
                                {{ $item->NamaJabatan ?? '-' }}
                            </td>
                        @endforeach

                    @else
                        @foreach ($approval2 ?? [] as $item)
                            <td class="text-center no-border">
                                {{ $item->NamaJabatan ?? '-' }}
                            </td>
                        @endforeach
                    @endif
                </tr>
                <tr>
                    @foreach ($approval2 ?? [] as $item)
                        <td class="text-center no-border" style="vertical-align:middle;">
                            @if (is_object($item) && isset($item->Status) && $item->Status == 'Approved' && isset($item->qrCode))
                                <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                    style="width:70px; height:70px; margin-bottom:4px;">
                            @endif
                            <hr style="width:70%; margin:6px auto 3px auto;">
                        </td>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($approval2 ?? [] as $item)
                        <td class="text-center no-border">
                            <span><b>{{ $item->Nama ?? '-' }}</b></span>
                            <div><small>{{ $item->Status ?? '-' }}</small>
                                <br>
                                <small><em>
                                        {{ $item->TanggalApprove ?? null ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') . ' ' . \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i') : '-' }}
                                    </em></small>
                            </div>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
