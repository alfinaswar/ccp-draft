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
            <div style="font-size:14px;">{{ $usulan->getNamaForm->Nama }}</div>
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
                <td style="width:18%;">{{ \Carbon\Carbon::parse($usulan->Tanggal)->translatedFormat('d F Y') }}</td>
                <td style="width:13%;">Tanggal</td>
                <td style="width:2%;">:</td>
                <td style="width:18%;">{{ \Carbon\Carbon::parse($usulan->Tanggal2)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Nama Kepala Divisi</td>
                <td>:</td>
                <td>{{ $usulan->getKadiv->name }}</td>
                <td>Nama Kepala Divisi</td>
                <td>:</td>
                <td>{{ $usulan->getKadiv2->name }}</td>
            </tr>
            <tr>
                <td>Divisi</td>
                <td>:</td>
                <td>{{ $usulan->getDepartemen->Nama }}</td>
                <td>Divisi</td>
                <td>:</td>
                <td>{{ $usulan->getDepartemen2->Nama }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ $usulan->Kategori }}</td>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ $usulan->Kategori }}</td>
            </tr>
            <tr>
                <td colspan="6"><i>* Pilih salah satu</i></td>
            </tr>
        </table>
        <div style="margin-top: 18px; margin-bottom: 6px;">
            Dengan ini kami ajukan permohonan untuk pengadaan barang/jasa dengan alasan sebagai berikut:
        </div>
        <div style="margin-bottom: 20px;">{{ $usulan->Alasan }}</div>
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
                    function rupiah($angka)
                    {
                        return 'Rp ' . number_format($angka, 0, ',', '.');
                    }
                @endphp
                @forelse ($dataRekom->getRekomedasiDetail as $key => $rekomDetail)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            {{ $rekomDetail->getBarang->Nama ?? '-' }}
                        </td>
                        <td>
                            {{ $rekomDetail->getNamaVendor->Nama ?? '-' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaAwal) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaAwal)) : 'Rp 0' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaNego) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego)) : 'Rp 0' }}
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
                            {{ isset($rekomDetail->HargaAwal) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaAwal)) : 'Rp 0' }}
                        </td>
                        <td>
                            {{ isset($rekomDetail->HargaNego) ? rupiah((int) preg_replace('/[^\d]/', '', $rekomDetail->HargaNego)) : 'Rp 0' }}
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
                @if ($usulan->SudahRkap == 'Y')
                    <span>Ya, Sudah</span>
                @elseif ($usulan->SudahRkap == 'N')
                    <span>Tidak, Belum</span>
                @else
                    <span>-</span>
                @endif
            </div>
            <div>
                Sisa Budget dari RKAP untuk tahun ini yang masih dapat dipergunakan Rp.
                <u>{{ number_format($usulan->SisaBudget ?? 0, 0, ',', '.') }}</u> [diisi oleh departemen terkait]
            </div>
            <div style="margin-top:12px;"><b>Verifikasi keuangan:</b></div>
            <div>
                <span>Sudah masuk RKAP dari departemen ybs:</span>
                @if ($usulan->SudahRkap2 == 'Y')
                    <span>Ya, Sudah</span>
                @elseif ($usulan->SudahRkap2 == 'N')
                    <span>Tidak, Belum</span>
                @else
                    <span>-</span>
                @endif
            </div>
            <div>
                Sisa Budget dari RKAP untuk tahun ini yang masih dapat dipergunakan Rp.
                <u>{{ number_format($usulan->SisaBudget2 ?? 0, 0, ',', '.') }}</u>
            </div>
        </div>
        <table style="border:none; margin-top:24px;">
            <colgroup>
                @if (!empty($approval))
                    @foreach ($approval as $item)
                        <col style="width:{{ 100 / count($approval) }}%;">
                    @endforeach
                @endif
            </colgroup>
            <tbody>
                <tr>
                    @php
                        $showApprovalList = false;
                        $approvalList = [];
                        $totalNego = 0;
                        $jenisP = $data2->getPerusahaan->Kategori ?? null;

                        // IF Versi v2: gunakan flow dari gambar/prompt
                        if (
                            isset($data2) &&
                            ($data2->Versi ?? ($data2->Versi ?? 'v1')) === 'v2' &&
                            !empty($dataRekom) &&
                            isset($dataRekom->getRekomedasiDetail)
                        ) {
                            // Ambil HargaNego rekomendasi 1, default 0
                            $rekomendasiSatu = $dataRekom->getRekomedasiDetail->first(function ($item) {
                                return isset($item->Rekomendasi) && $item->Rekomendasi == 1;
                            });
                            $totalNego = (int) ($rekomendasiSatu->HargaNego ?? 0);

                            $showApprovalList = true;
                            // NOTE: Ikuti gambar sebagai acuan utama, tanpa membedakan Jenis/jangmed/umum
                            if ($totalNego > 100000000) {
                                $approvalList = ['Direktur RS', 'GH Keuangan', 'Direktur RSAB Group', 'CEO'];
                            } elseif ($totalNego > 50000000 && $totalNego <= 100000000) {
                                $approvalList = ['Direktur RS', 'GH Keuangan', 'Direktur RSAB Group'];
                            } else {
                                // Untuk <= 50 juta, tetap pake default $approvalList kosong/nilai lama jika ada
                                $showApprovalList = false; // fallback pake approval lama jika di bawah 50jt
                            }
                        }
                        // fallback ke versi sebelumnya (v1 logic lama)
                        elseif (
                            isset($data2) &&
                            isset($data2->Jenis) &&
                            !empty($dataRekom) &&
                            isset($dataRekom->getRekomedasiDetail)
                        ) {
                            $rekomendasiSatu = $dataRekom->getRekomedasiDetail->first(function ($item) {
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

                    @if ($showApprovalList && !empty($approvalList))
                       @foreach ($approval as $item)
                            <td class="text-center no-border">
                                {{ $item->NamaJabatan ?? '-' }}<br>
                            </td>
                        @endforeach
                    @else
                        @foreach ($approval as $item)
                            <td class="text-center no-border">
                                {{ $item->NamaJabatan ?? '-' }}
                            </td>
                        @endforeach
                    @endif
                </tr>
                <tr>
                    @foreach ($approval as $item)
                        <td class="text-center no-border" style="vertical-align:middle;">
                            @if ($item->Status == 'Approved' && isset($item->qrCode))
                                <img src="data:image/png;base64,{{ $item->qrCode }}" alt="QR Code"
                                    style="width:70px; height:70px; margin-bottom:4px;">
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($approval as $item)
                        <td class="text-center no-border">
                            <span><b>{{ $item->Nama ?? '-' }}</b></span>
                            <div><small>{{ $item->Status ?? '-' }}</small>
                                <br>
                                <small><em>
                                        {{ $item->TanggalApprove
                                            ? \Carbon\Carbon::parse($item->TanggalApprove)->locale('id')->isoFormat('D MMMM Y') .
                                                ' ' .
                                                \Carbon\Carbon::parse($item->TanggalApprove)->format('H:i')
                                            : '-' }}
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
