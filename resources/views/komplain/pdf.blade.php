<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Formulir Evaluasi BMHP Umum - {{ $komplain->KodeKomplain }}</title>
    <style>
        @page {
            margin: 10mm 14mm 12mm 14mm;
            size: a4 portrait;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 2px solid #d21f26;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .kop-logo {
            width: 170px;
            height: auto;
        }

        .kop-right {
            text-align: right;
            vertical-align: bottom;
        }

        .kop-num-lbl {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-num-val {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 1px;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .badge-status-selesai {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-status-pending {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11pt;
            letter-spacing: 0.6px;
            color: #0f172a;
            margin: 6px 0 10px 0;
        }

        /* Kotak Metadata */
        .meta-box {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px 8px;
            margin-bottom: 10px;
        }

        /* Section Title (Seragam dengan show.blade.php) */
        .sec-title {
            font-size: 8.2pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #1e293b;
            background-color: #f1f5f9;
            border-left: 4px solid #d21f26;
            padding: 3px 6px;
            margin: 8px 0 5px 0;
        }

        /* Tabel Key-Value A & B */
        .kv-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kv-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 8pt;
        }

        .kv-table td.k {
            width: 110px;
            color: #475569;
        }

        .kv-table td.sep {
            width: 8px;
            color: #64748b;
        }

        .kv-table td.v {
            color: #0f172a;
            border-bottom: 1px dashed #cbd5e1;
            word-break: break-word;
        }

        /* Parameter Checklist C */
        .check-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 3px;
            margin-bottom: 6px;
        }

        .check-cell {
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            padding: 3px 6px;
            font-size: 7.8pt;
            color: #64748b;
            border-radius: 3px;
            vertical-align: middle;
        }

        .check-cell.checked {
            border-color: #bbf7d0;
            background-color: #f0fdf4;
            color: #166534;
            font-weight: bold;
        }

        .check-cell .box-icon {
            font-size: 9pt;
            margin-right: 4px;
        }

        /* Uraian Box D */
        .uraian-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 8pt;
            line-height: 1.45;
            color: #0f172a;
            margin-bottom: 8px;
            min-height: 36px;
        }

        .uraian-box p {
            margin: 0 0 4px 0;
        }

        .uraian-box p:last-child {
            margin-bottom: 0;
        }

        /* Berkas Card */
        .file-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            margin-bottom: 6px;
        }

        .file-card {
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            border-radius: 4px;
            padding: 4px 6px;
            font-size: 7.5pt;
            vertical-align: top;
        }

        .file-name {
            font-weight: bold;
            color: #0f172a;
            word-break: break-all;
        }

        .file-meta {
            font-size: 6.8pt;
            color: #64748b;
            margin-top: 1px;
        }

        /* Box CCP */
        .ccp-box {
            border: 1px solid #cbd5e1;
            border-left: 4px solid #0284c7;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 6px 8px;
            margin: 8px 0;
        }

        .ccp-badge {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7.3pt;
            font-weight: bold;
            margin: 1px 3px 1px 0;
        }

        /* Tabel Tanda Tangan */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            page-break-inside: avoid;
        }

        .ttd-table th,
        .ttd-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .ttd-table th {
            background-color: #f1f5f9;
            font-size: 7.8pt;
            font-weight: bold;
            color: #1e293b;
        }

        .ttd-table td {
            height: 78px;
            font-size: 7.5pt;
        }

        .ttd-date {
            font-size: 7pt;
            color: #64748b;
            margin-bottom: 2px;
        }

        .ttd-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 7.8pt;
            margin-top: 2px;
        }

        .ttd-pending {
            font-size: 7pt;
            color: #94a3b8;
            font-style: italic;
        }

        .ttd-qr {
            width: 48px;
            height: 48px;
            margin: 1px auto;
        }

        .foot-note {
            font-size: 7pt;
            color: #64748b;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    @php
        $logoPath = public_path('assets/img/logo/logoawalbros.png');
        $statusBadgeClass = $komplain->Status === 'Selesai' ? 'badge-status-selesai' : 'badge-status-pending';
    @endphp

    {{-- Kop Surat --}}
    <table class="kop-table">
        <tr>
            <td style="vertical-align: bottom;">
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="RS Awal Bros" class="kop-logo">
                @else
                    <strong style="color: #d21f26; font-size: 15pt;">RS AWAL BROS</strong>
                @endif
            </td>
            <td class="kop-right">
                <div class="kop-num-lbl">Nomor Dokumen</div>
                <div class="kop-num-val">{{ $komplain->KodeKomplain }}</div>
                <div>
                    <span class="badge-status {{ $statusBadgeClass }}">
                        {{ $komplain->Status }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">Formulir Evaluasi BMHP Umum</div>

    {{-- Metadata RS & Tanggal --}}
    <table class="meta-box">
        <tr>
            <td width="60%">
                <table width="100%">
                    <tr>
                        <td width="65" style="color: #475569; font-size: 8pt;">Nama RS</td>
                        <td width="8" style="color: #64748b; font-size: 8pt;">:</td>
                        <td style="font-weight: bold; color: #0f172a; font-size: 8.2pt;">{{ $komplain->NamaRS }}</td>
                    </tr>
                </table>
            </td>
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td width="55" style="color: #475569; font-size: 8pt;">Tanggal</td>
                        <td width="8" style="color: #64748b; font-size: 8pt;">:</td>
                        <td style="font-weight: bold; color: #0f172a; font-size: 8.2pt;">
                            {{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- A & B Berdampingan 2 Kolom --}}
    <table width="100%" style="margin-bottom: 6px;">
        <tr>
            <td width="49%" style="vertical-align: top;">
                <div class="sec-title">A. Identitas Pelapor</div>
                <table class="kv-table">
                    <tr>
                        <td class="k">Nama Pelapor</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->NamaPelapor }}</td>
                    </tr>
                    <tr>
                        <td class="k">Jabatan</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->Jabatan }}</td>
                    </tr>
                    <tr>
                        <td class="k">Unit Kerja</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->UnitKerja }}</td>
                    </tr>
                </table>
            </td>
            <td width="2%">&nbsp;</td>
            <td width="49%" style="vertical-align: top;">
                <div class="sec-title">B. Data BMHP Umum</div>
                <table class="kv-table">
                    <tr>
                        <td class="k">Nama Barang</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->NamaBarang }}</td>
                    </tr>
                    <tr>
                        <td class="k">Jenis/Kategori</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->JenisKategori }}</td>
                    </tr>
                    <tr>
                        <td class="k">Merk</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->Merk }}</td>
                    </tr>
                    <tr>
                        <td class="k">Distributor/Penyedia</td>
                        <td class="sep">:</td>
                        <td class="v">{{ $komplain->Distributor }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- C. Parameter Evaluasi --}}
    <div class="sec-title">C. Parameter Evaluasi BMHP Umum</div>
    <table class="check-grid">
        @php $chunks = array_chunk($parameterEvaluasi, 2); @endphp
        @foreach ($chunks as $chunk)
            <tr>
                @foreach ($chunk as $item)
                    @php $dipilih = in_array($item, $komplain->ParameterEvaluasi ?? [], true); @endphp
                    <td width="50%" class="check-cell {{ $dipilih ? 'checked' : '' }}">
                        <span class="box-icon">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                        <span>
                            {{ $item }}@if ($item === 'Lainnya' && $dipilih && $komplain->ParameterLainnya): <strong>{{ $komplain->ParameterLainnya }}</strong>@endif
                        </span>
                    </td>
                @endforeach
                @if (count($chunk) == 1)
                    <td width="50%">&nbsp;</td>
                @endif
            </tr>
        @endforeach
    </table>

    {{-- D. Deskripsi Evaluasi (Teks Uraian Singkat dibuang sesuai request) --}}
    <div class="sec-title">D. Deskripsi Evaluasi</div>
    <div class="uraian-box">
        @php
            $uraian = $komplain->UraianSingkat ?? '';
            $uraianHtml = str_contains($uraian, '<') ? $uraian : nl2br(e($uraian));
        @endphp
        {!! $uraianHtml !!}
    </div>

    {{-- Data Pendukung (Dibuat seperti Deskripsi Evaluasi sesuai request) --}}
    <div class="sec-title">Data Pendukung</div>
    @if (!empty($komplain->Lampiran))
        <table class="file-grid">
            @php $lampChunks = array_chunk($komplain->Lampiran, 3); @endphp
            @foreach ($lampChunks as $row)
                <tr>
                    @foreach ($row as $file)
                        @php
                            $ext = strtolower(pathinfo($file['nama'] ?? '', PATHINFO_EXTENSION));
                        @endphp
                        <td width="33%" class="file-card">
                            <div class="file-name">{{ $file['nama'] }}</div>
                            <div class="file-meta">
                                {{ strtoupper($ext) }}
                                @if (!empty($file['ukuran']))
                                    &bull; {{ number_format($file['ukuran'] / 1024, 0) }} KB
                                @endif
                            </div>
                        </td>
                    @endforeach
                    @for ($i = count($row); $i < 3; $i++)
                        <td width="33%">&nbsp;</td>
                    @endfor
                </tr>
            @endforeach
        </table>
    @else
        <div style="color: #64748b; font-style: italic; font-size: 7.8pt; margin-bottom: 6px;">
            Tidak ada berkas pendukung.
        </div>
    @endif

    {{-- Tindak Lanjut Logistik Umum --}}
    @if (!empty($komplain->LangkahAwal))
        <div class="sec-title">Tindak Lanjut Logistik Umum</div>
        <table class="check-grid">
            @php $laChunks = array_chunk($komplain->LangkahAwal, 2); @endphp
            @foreach ($laChunks as $chunk)
                <tr>
                    @foreach ($chunk as $la)
                        <td width="50%" class="check-cell checked">
                            <span class="box-icon">&#9745;</span>
                            <span>
                                {{ $la }}@if ($la === 'Lainnya' && $komplain->LangkahAwalLainnya): <strong>{{ $komplain->LangkahAwalLainnya }}</strong>@endif
                            </span>
                        </td>
                    @endforeach
                    @if (count($chunk) == 1)
                        <td width="50%">&nbsp;</td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Hasil Temuan & Tindakan CCP --}}
    @if (!empty($komplain->TindakanCcp) || !empty($komplain->KeteranganCcp) || !empty($komplain->LampiranCcp))
        <div class="ccp-box">
            <table width="100%" style="margin-bottom: 4px;">
                <tr>
                    <td style="font-weight: bold; font-size: 8.2pt; color: #0284c7; text-transform: uppercase;">
                        Hasil Temuan &amp; Tindakan Tim CCP
                    </td>
                    @if ($komplain->TanggalCcp || $komplain->CcpOleh)
                        <td style="text-align: right; font-size: 7.2pt; color: #64748b;">
                            {{ $komplain->CcpOleh ?? 'Tim CCP' }}
                            @if ($komplain->TanggalCcp)
                                &bull; {{ \Carbon\Carbon::parse($komplain->TanggalCcp)->format('d/m/Y H:i') }}
                            @endif
                        </td>
                    @endif
                </tr>
            </table>

            @if (!empty($komplain->TindakanCcp))
                <div style="font-size: 7.2pt; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                    Tindakan / Rekomendasi CCP:
                </div>
                <div style="margin-bottom: 5px;">
                    @foreach ($komplain->TindakanCcp as $tc)
                        <span class="ccp-badge">
                            &bull; {{ $tc }}@if ($tc === 'Lainnya' && $komplain->TindakanCcpLainnya): {{ $komplain->TindakanCcpLainnya }}@endif
                        </span>
                    @endforeach
                </div>
            @endif

            @if (!empty($komplain->KeteranganCcp))
                <div style="font-size: 7.2pt; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                    Keterangan / Analisa Investigasi:
                </div>
                <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 3px; padding: 4px 6px; font-size: 7.8pt; line-height: 1.4; color: #0f172a; margin-bottom: 5px;">
                    {!! str_contains($komplain->KeteranganCcp, '<') ? $komplain->KeteranganCcp : nl2br(e($komplain->KeteranganCcp)) !!}
                </div>
            @endif

            @if (!empty($komplain->LampiranCcp))
                <div style="font-size: 7.2pt; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                    Berkas / Bukti Temuan Investigasi ({{ count($komplain->LampiranCcp) }} Berkas):
                </div>
                <table class="file-grid" style="margin-bottom: 0;">
                    @php $ccpChunks = array_chunk($komplain->LampiranCcp, 3); @endphp
                    @foreach ($ccpChunks as $crow)
                        <tr>
                            @foreach ($crow as $cf)
                                @php $cext = strtolower(pathinfo($cf['nama'] ?? '', PATHINFO_EXTENSION)); @endphp
                                <td width="33%" class="file-card">
                                    <div class="file-name">{{ $cf['nama'] }}</div>
                                    <div class="file-meta">
                                        {{ strtoupper($cext) }}
                                        @if (!empty($cf['ukuran']))
                                            &bull; {{ number_format($cf['ukuran'] / 1024, 0) }} KB
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            @for ($i = count($crow); $i < 3; $i++)
                                <td width="33%">&nbsp;</td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    @endif

    {{-- E. Tanda Tangan --}}
    <div class="sec-title" style="page-break-inside: avoid;">E. Tanda Tangan (Nama dan Tanggal)</div>
    @php
        $approvalPelapor = $approval->firstWhere('Urutan', 1);
        $approvalPenandatangan = $approval->where('Urutan', '>=', 2);
        $totalPenandatangan = 1 + count($approvalPenandatangan);
        $colWidth = $totalPenandatangan > 0 ? number_format(100 / $totalPenandatangan, 2) : 20;
    @endphp
    <table class="ttd-table">
        <thead>
            <tr>
                <th width="{{ $colWidth }}%">Pelapor</th>
                @forelse ($approvalPenandatangan as $ttd)
                    <th width="{{ $colWidth }}%">
                        {{ $ttd->NamaJabatan ?: ($ttd->getJabatan->Nama ?? ($ttd->Urutan == 2 ? 'Kepala Divisi' : ($ttd->Urutan == 3 ? 'Group Head' : 'Approver #' . $ttd->Urutan))) }}
                    </th>
                @empty
                    <th width="33%">Kepala Divisi</th>
                    <th width="33%">Group Head</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if ($approvalPelapor && $approvalPelapor->Status === 'Approved')
                        <div class="ttd-date">
                            {{ $approvalPelapor->TanggalApprove ? \Carbon\Carbon::parse($approvalPelapor->TanggalApprove)->format('d/m/Y') : \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}
                        </div>
                        @if (!empty($approvalPelapor->qrCode))
                            <img src="data:image/png;base64,{{ $approvalPelapor->qrCode }}" alt="QR" class="ttd-qr">
                        @endif
                        <div class="ttd-name">{{ $approvalPelapor->Nama }}</div>
                    @else
                        <div class="ttd-name">{{ $komplain->NamaPelapor }}</div>
                        <div class="ttd-date">{{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}</div>
                    @endif
                </td>
                @forelse ($approvalPenandatangan as $ttd)
                    <td>
                        @if ($ttd && $ttd->Status === 'Approved')
                            <div class="ttd-date">
                                {{ $ttd->TanggalApprove ? \Carbon\Carbon::parse($ttd->TanggalApprove)->format('d/m/Y') : '' }}
                            </div>
                            @if (!empty($ttd->qrCode))
                                <img src="data:image/png;base64,{{ $ttd->qrCode }}" alt="QR" class="ttd-qr">
                            @endif
                            <div class="ttd-name">{{ $ttd->Nama }}</div>
                        @else
                            <div class="ttd-pending">Belum ditandatangani</div>
                            @if ($ttd->Nama)
                                <div class="ttd-date">({{ $ttd->Nama }})</div>
                            @endif
                        @endif
                    </td>
                @empty
                    <td><div class="ttd-pending">Belum ditandatangani</div></td>
                    <td><div class="ttd-pending">Belum ditandatangani</div></td>
                @endforelse
            </tr>
        </tbody>
    </table>
    <div class="foot-note">Catatan: Formulir dilampirkan bersama BMHP UMUM</div>
</body>

</html>
