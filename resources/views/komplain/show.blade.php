@extends('layouts.app')

@section('content')
    @php
        $approvalPelapor = $approval->firstWhere('Urutan', 1);
        $approvalPenandatangan = $approval->where('Urutan', '>=', 2);
        $kandidatApproval = $approval
            ->where('UserId', (string) auth()->id())
            ->where('Status', 'Pending')
            ->sortBy('Urutan')
            ->first();
        $punyaApproval = $kandidatApproval
            && $approval->where('Urutan', '<', $kandidatApproval->Urutan)->where('Status', '!=', 'Approved')->isEmpty()
                ? $kandidatApproval
                : null;
        $statusBadge = $komplain->Status === 'Selesai' ? 'bg-success' : 'bg-warning text-dark';

        // Deteksi jenis tindakan pada tahap approval
        $namaJabatanAppr = strtolower($punyaApproval?->NamaJabatan ?? '');
        $isTahapLogum = $punyaApproval && (
            (int) $punyaApproval->Urutan === 4
            || str_contains($namaJabatanAppr, 'logistik')
            || str_contains($namaJabatanAppr, 'logum')
            || str_contains($namaJabatanAppr, 'smi')
            || (auth()->user()?->hasAnyRole(['Logum', 'SMI']) && empty($komplain->LangkahAwal))
        ) && empty($komplain->LangkahAwal);

        $isTahapCcp = $punyaApproval && (
            (int) $punyaApproval->Urutan === 5
            || (str_contains($namaJabatanAppr, 'ccp') && !str_contains($namaJabatanAppr, 'gh'))
            || (auth()->user()?->hasRole('CCP') && empty($komplain->TindakanCcp))
        ) && empty($komplain->TindakanCcp);

        $isTahapGhCcp = $punyaApproval && (
            (int) $punyaApproval->Urutan === 6
            || str_contains($namaJabatanAppr, 'gh ccp')
            || auth()->user()?->hasRole('GH CCP')
        );
    @endphp

    <div class="page-header ph-redesign no-print">
        <div class="ph-container">
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('komplain.index') }}">Komplain Vendor</a></li>
                    <li class="active">{{ $komplain->KodeKomplain }}</li>
                </ul>
                <h3 class="ph-title">{{ $komplain->KodeKomplain }}</h3>
                <p class="ph-subtitle">
                    <span class="badge {{ $statusBadge }}">{{ $komplain->Status }}</span>
                </p>
            </div>
            <div class="ph-right d-flex gap-2">
                <a href="{{ route('komplain.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('komplain.cetak', encrypt($komplain->id)) }}" target="_blank" class="btn btn-danger">
                    <i class="fa fa-print me-1"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>

    @if (Session::get('success'))
        <div class="alert alert-success no-print {{ $punyaApproval ? '' : 'komplain-wrap' }}">{{ Session::get('success') }}</div>
    @endif
    @if (Session::get('error'))
        <div class="alert alert-danger no-print {{ $punyaApproval ? '' : 'komplain-wrap' }}">{{ Session::get('error') }}</div>
    @endif

    <div class="{{ $punyaApproval ? 'row g-4 align-items-start' : 'komplain-wrap' }}">
        <div class="{{ $punyaApproval ? 'col-xl-8 komplain-print-full' : '' }}">
            <div class="card komplain-doc-card mb-0">
                <div class="card-body p-4 p-md-5">
            {{-- Kop Surat --}}
            <div class="komplain-kop d-flex flex-wrap justify-content-between align-items-end gap-3 pb-3 mb-4">
                <div>
                    <img src="{{ asset('assets/img/logo/logoawalbros.png') }}" alt="RS Awal Bros" class="komplain-logo">
                </div>
                <div class="text-md-end">
                    <div class="text-muted small text-uppercase">Nomor Dokumen</div>
                    <div class="fw-bold fs-6 text-dark">{{ $komplain->KodeKomplain }}</div>
                    <span class="badge {{ $statusBadge }} no-print mt-1">{{ $komplain->Status }}</span>
                </div>
            </div>

            <h5 class="komplain-doc-title text-center text-uppercase fw-bold mb-4">
                Formulir Evaluasi BMHP Umum
            </h5>

            {{-- Info Utama --}}
            <div class="komplain-meta-box rounded p-3 mb-4">
                <div class="row g-2">
                    <div class="col-md-7">
                        <div class="komplain-kv">
                            <span class="komplain-k">Nama RS</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v fw-semibold">{{ $komplain->NamaRS }}</span>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="komplain-kv">
                            <span class="komplain-k">Tanggal</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v fw-semibold">
                                {{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- A & B dalam 2 kolom di layar lebar --}}
            <div class="row g-4 mb-2">
                <div class="col-lg-6">
                    <h6 class="komplain-sec-title">A. Identitas Pelapor</h6>
                    <div class="komplain-kv-list">
                        <div class="komplain-kv">
                            <span class="komplain-k">Nama Pelapor</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->NamaPelapor }}</span>
                        </div>
                        <div class="komplain-kv">
                            <span class="komplain-k">Jabatan</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->Jabatan }}</span>
                        </div>
                        <div class="komplain-kv">
                            <span class="komplain-k">Unit Kerja</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->UnitKerja }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h6 class="komplain-sec-title">B. Data BMHP Umum</h6>
                    <div class="komplain-kv-list">
                        <div class="komplain-kv">
                            <span class="komplain-k">Nama Barang</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->NamaBarang }}</span>
                        </div>
                        <div class="komplain-kv">
                            <span class="komplain-k">Jenis/Kategori</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->JenisKategori }}</span>
                        </div>
                        <div class="komplain-kv">
                            <span class="komplain-k">Merk</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->Merk }}</span>
                        </div>
                        <div class="komplain-kv">
                            <span class="komplain-k">Distributor/Penyedia</span>
                            <span class="komplain-sep">:</span>
                            <span class="komplain-v">{{ $komplain->Distributor }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- C. Parameter Evaluasi --}}
            <h6 class="komplain-sec-title mt-4">C. Parameter Evaluasi BMHP Umum</h6>
            <div class="row g-2 mb-3">
                @foreach ($parameterEvaluasi as $item)
                    @php $dipilih = in_array($item, $komplain->ParameterEvaluasi ?? [], true); @endphp
                    <div class="col-md-6">
                        <div class="komplain-check-item {{ $dipilih ? 'is-checked' : '' }}">
                            <span class="komplain-check-icon">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                            <span>
                                {{ $item }}@if ($item === 'Lainnya' && $dipilih && $komplain->ParameterLainnya)
                                    : <strong class="text-dark">{{ $komplain->ParameterLainnya }}</strong>
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- D. Deskripsi Evaluasi --}}
            <h6 class="komplain-sec-title mt-4">D. Deskripsi Evaluasi</h6>
            <div class="komplain-uraian-box rounded p-3 mb-3">
                @php
                    $uraian = $komplain->UraianSingkat ?? '';
                    $uraianHtml = str_contains($uraian, '<') ? $uraian : nl2br(e($uraian));
                @endphp
                {!! $uraianHtml !!}
            </div>

            {{-- Data Pendukung --}}
            <h6 class="komplain-sec-title mt-4">Data Pendukung</h6>
            @if (!empty($komplain->Lampiran))
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @foreach ($komplain->Lampiran as $file)
                        @php
                            $url = asset('storage/' . $file['path']);
                            $ext = strtolower(pathinfo($file['nama'] ?? '', PATHINFO_EXTENSION));
                            $isImg = str_starts_with($file['tipe'] ?? '', 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            $isPdf = ($file['tipe'] ?? '') === 'application/pdf' || $ext === 'pdf';
                        @endphp
                        <a href="{{ $url }}" target="_blank" rel="noopener"
                            class="komplain-file-card text-decoration-none komplain-previewable-file"
                            data-file-url="{{ $url }}"
                            data-file-name="{{ $file['nama'] }}"
                            data-file-type="{{ $ext }}"
                            data-is-img="{{ $isImg ? '1' : '0' }}"
                            data-is-pdf="{{ $isPdf ? '1' : '0' }}"
                            title="Klik untuk pratinjau / unduh berkas">
                            <div class="position-relative">
                                @if ($isImg)
                                    <img src="{{ $url }}" alt="{{ $file['nama'] }}" class="komplain-file-thumb">
                                    <span class="komplain-preview-badge"><i class="fa fa-eye me-1"></i> Preview</span>
                                @elseif ($isPdf)
                                    <div class="komplain-file-pdf">
                                        <i class="fa fa-file-pdf"></i>
                                    </div>
                                    <span class="komplain-preview-badge"><i class="fa fa-eye me-1"></i> Preview</span>
                                @else
                                    <div class="komplain-file-alt">
                                        <i class="fa fa-file-alt"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="komplain-file-name text-dark fw-medium" title="{{ $file['nama'] }}">{{ $file['nama'] }}</div>
                            @if (!empty($file['ukuran']))
                                <small class="text-muted d-block" style="font-size: 0.68rem;">{{ number_format($file['ukuran'] / 1024, 0) }} KB</small>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted small fst-italic mb-3">Tidak ada berkas pendukung.</p>
            @endif

            @if (!empty($komplain->DataPendukung))
                {{-- data lama sebelum fitur upload dipakai --}}
                <div class="row g-2 mb-3">
                    @foreach ($dataPendukung as $item)
                        @php $dipilih = in_array($item, $komplain->DataPendukung ?? [], true); @endphp
                        <div class="col-md-6">
                            <div class="komplain-check-item {{ $dipilih ? 'is-checked' : '' }}">
                                <span class="komplain-check-icon">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                                <span>
                                    {{ $item }}@if ($item === 'Lainnya' && $dipilih && $komplain->DataPendukungLainnya)
                                        : <strong class="text-dark">{{ $komplain->DataPendukungLainnya }}</strong>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tindak Lanjut Logistik Umum (Jika Ada) --}}
            @if (!empty($komplain->LangkahAwal))
                <h6 class="komplain-sec-title mt-4">Tindak Lanjut Logistik Umum</h6>
                <div class="row g-2 mb-3">
                    @foreach ($komplain->LangkahAwal as $la)
                        <div class="col-md-6">
                            <div class="komplain-check-item is-checked">
                                <span class="komplain-check-icon">&#9745;</span>
                                <span>
                                    {{ $la }}@if ($la === 'Lainnya' && $komplain->LangkahAwalLainnya)
                                        : <strong class="text-dark">{{ $komplain->LangkahAwalLainnya }}</strong>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Hasil Temuan & Tindakan CCP (Tampil untuk Admin, GH CCP, dan Verifikator) --}}
            @if (!empty($komplain->TindakanCcp) || !empty($komplain->KeteranganCcp) || !empty($komplain->LampiranCcp))
                <div class="komplain-ccp-section mt-4 mb-3 p-3 rounded border">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="komplain-ccp-icon"><i class="fa fa-clipboard-check"></i></span>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Hasil Temuan &amp; Tindakan Tim CCP</h6>
                                <small class="text-muted">Investigasi &amp; Data Pendukung CCP</small>
                            </div>
                        </div>
                        @if ($komplain->TanggalCcp || $komplain->CcpOleh)
                            <span class="badge bg-light text-secondary border">
                                <i class="fa fa-user-check me-1"></i> {{ $komplain->CcpOleh ?? 'Tim CCP' }}
                                @if ($komplain->TanggalCcp)
                                    &bull; {{ \Carbon\Carbon::parse($komplain->TanggalCcp)->format('d/m/Y H:i') }}
                                @endif
                            </span>
                        @endif
                    </div>

                    @if (!empty($komplain->TindakanCcp))
                        <div class="mb-3">
                            <div class="small fw-semibold text-muted mb-2 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                Tindakan / Rekomendasi CCP:
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($komplain->TindakanCcp as $tc)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <i class="fa fa-check me-1"></i>
                                        {{ $tc }}@if ($tc === 'Lainnya' && $komplain->TindakanCcpLainnya)
                                            : <strong class="text-dark">{{ $komplain->TindakanCcpLainnya }}</strong>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($komplain->KeteranganCcp))
                        <div class="mb-3">
                            <div class="small fw-semibold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                Keterangan / Analisa Investigasi:
                            </div>
                            <div class="komplain-uraian-box rounded p-3 bg-white">
                                {!! str_contains($komplain->KeteranganCcp, '<') ? $komplain->KeteranganCcp : nl2br(e($komplain->KeteranganCcp)) !!}
                            </div>
                        </div>
                    @endif

                    @if (!empty($komplain->LampiranCcp))
                        <div>
                            <div class="small fw-semibold text-muted mb-2 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                Berkas / Bukti Temuan Investigasi ({{ count($komplain->LampiranCcp) }} Berkas):
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($komplain->LampiranCcp as $fileCcp)
                                    @php
                                        $urlCcp = asset('storage/' . $fileCcp['path']);
                                        $extCcp = strtolower(pathinfo($fileCcp['nama'] ?? '', PATHINFO_EXTENSION));
                                        $isImgCcp = str_starts_with($fileCcp['tipe'] ?? '', 'image/') || in_array($extCcp, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                        $isPdfCcp = ($fileCcp['tipe'] ?? '') === 'application/pdf' || $extCcp === 'pdf';
                                        $isDocCcp = in_array($extCcp, ['doc', 'docx']);
                                        $isXlsCcp = in_array($extCcp, ['xls', 'xlsx', 'csv']);
                                    @endphp
                                    <a href="{{ $urlCcp }}" target="_blank" rel="noopener"
                                        class="komplain-file-card text-decoration-none komplain-previewable-file"
                                        data-file-url="{{ $urlCcp }}"
                                        data-file-name="{{ $fileCcp['nama'] }}"
                                        data-file-type="{{ $extCcp }}"
                                        data-is-img="{{ $isImgCcp ? '1' : '0' }}"
                                        data-is-pdf="{{ $isPdfCcp ? '1' : '0' }}"
                                        title="Klik untuk pratinjau / unduh berkas">
                                        <div class="position-relative">
                                            @if ($isImgCcp)
                                                <img src="{{ $urlCcp }}" alt="{{ $fileCcp['nama'] }}" class="komplain-file-thumb">
                                                <span class="komplain-preview-badge"><i class="fa fa-eye me-1"></i> Preview</span>
                                            @elseif ($isPdfCcp)
                                                <div class="komplain-file-pdf">
                                                    <i class="fa fa-file-pdf"></i>
                                                </div>
                                                <span class="komplain-preview-badge"><i class="fa fa-eye me-1"></i> Preview</span>
                                            @elseif ($isDocCcp)
                                                <div class="komplain-file-doc">
                                                    <i class="fa fa-file-word"></i>
                                                </div>
                                            @elseif ($isXlsCcp)
                                                <div class="komplain-file-excel">
                                                    <i class="fa fa-file-excel"></i>
                                                </div>
                                            @else
                                                <div class="komplain-file-alt">
                                                    <i class="fa fa-file-alt"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="komplain-file-name text-dark fw-medium" title="{{ $fileCcp['nama'] }}">
                                            {{ $fileCcp['nama'] }}
                                        </div>
                                        @if (!empty($fileCcp['ukuran']))
                                            <small class="text-muted d-block" style="font-size: 0.68rem;">
                                                {{ number_format($fileCcp['ukuran'] / 1024, 0) }} KB
                                            </small>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- E. Tanda Tangan --}}
            <h6 class="komplain-sec-title mt-4">E. Tanda Tangan (Nama dan Tanggal)</h6>
            <div class="table-responsive">
                <table class="table table-bordered komplain-ttd-table text-center align-middle mb-2">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Pelapor</th>
                            @forelse ($approvalPenandatangan as $ttd)
                                <th class="fw-semibold">
                                    {{ $ttd->NamaJabatan ?: ($ttd->getJabatan->Nama ?? ($ttd->Urutan == 2 ? 'Kepala Divisi' : ($ttd->Urutan == 3 ? 'Group Head' : 'Approver #' . $ttd->Urutan))) }}
                                </th>
                            @empty
                                <th class="fw-semibold">Kepala Divisi</th>
                                <th class="fw-semibold">Group Head</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                @if ($approvalPelapor && $approvalPelapor->Status === 'Approved')
                                    <small class="text-muted d-block mb-2">
                                        {{ $approvalPelapor->TanggalApprove ? \Carbon\Carbon::parse($approvalPelapor->TanggalApprove)->format('d/m/Y') : \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}
                                    </small>
                                    @if (!empty($approvalPelapor->qrCode))
                                        <div class="mb-2">
                                            <img src="data:image/png;base64,{{ $approvalPelapor->qrCode }}" alt="QR Tanda Tangan" width="76">
                                        </div>
                                    @endif
                                    <div class="fw-bold text-dark">{{ $approvalPelapor->Nama }}</div>
                                @else
                                    <div class="fw-bold text-dark">{{ $komplain->NamaPelapor }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            @forelse ($approvalPenandatangan as $ttd)
                                <td>
                                    @if ($ttd && $ttd->Status === 'Approved')
                                        <small class="text-muted d-block mb-2">
                                            {{ $ttd->TanggalApprove ? \Carbon\Carbon::parse($ttd->TanggalApprove)->format('d/m/Y') : '' }}
                                        </small>
                                        @if (!empty($ttd->qrCode))
                                            <div class="mb-2">
                                                <img src="data:image/png;base64,{{ $ttd->qrCode }}" alt="QR Tanda Tangan" width="76">
                                            </div>
                                        @endif
                                        <div class="fw-bold text-dark">{{ $ttd->Nama }}</div>
                                    @else
                                        <span class="text-muted fst-italic small">Belum ditandatangani</span>
                                        @if ($ttd->Nama)
                                            <div class="small text-muted mt-1">({{ $ttd->Nama }})</div>
                                        @endif
                                    @endif
                                </td>
                            @empty
                                <td><span class="text-muted fst-italic small">Belum ditandatangani</span></td>
                                <td><span class="text-muted fst-italic small">Belum ditandatangani</span></td>
                            @endforelse
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="small text-muted mb-0">Catatan: Formulir dilampirkan bersama BMHP UMUM</p>
                </div>
            </div>
        </div>

        {{-- Aksi Tanda Tangan (Samping Kanan) --}}
        @if ($punyaApproval)
            <div class="col-xl-4 no-print">
                <div class="card komplain-sign-card mb-0">
                    <div class="komplain-sign-header p-3 px-4">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="komplain-sign-icon">
                                    <i class="fa fa-pen-nib"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Tanda Tangan Anda</h6>
                                    <small class="text-muted">Persetujuan Dokumen</small>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark">Tahap {{ $punyaApproval->Urutan }}</span>
                        </div>
                    </div>
                    <div class="card-body p-3 p-xl-4">
                        <div class="komplain-meta-box rounded px-3 py-2 mb-3">
                            <div class="small text-muted">Penandatangan</div>
                            <div class="fw-semibold text-dark">{{ auth()->user()?->name ?? '-' }}</div>
                            @if ($punyaApproval->NamaJabatan)
                                <div class="small text-danger fw-medium">{{ $punyaApproval->NamaJabatan }}</div>
                            @endif
                        </div>

                        @if ($isTahapGhCcp)
                            <div class="alert alert-primary border-0 shadow-sm small mb-3 p-3" style="background-color: #e0f2fe; border-left: 4px solid #0284c7 !important;">
                                <div class="d-flex gap-2">
                                    <i class="fa fa-info-circle text-primary fs-5 mt-1"></i>
                                    <div>
                                        <strong class="text-dark">Peninjauan Group Head CCP</strong>
                                        <p class="mb-0 text-secondary mt-1">
                                            Silakan periksa hasil investigasi, temuan, dan dokumen pendukung Tim CCP pada pratinjau dokumen di sebelah kiri sebelum memberikan persetujuan akhir.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form id="formApprovalKomplain" action="{{ route('komplain.approve', encrypt($komplain->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @if ($isTahapLogum)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-dark mb-1">
                                        Langkah Awal Logistik Umum <span class="text-danger">*</span>
                                    </label>
                                    <div class="border rounded p-2 bg-light">
                                        @foreach ($langkahAwalList as $i => $item)
                                            <div class="form-check small mb-1">
                                                <input class="form-check-input" type="checkbox" name="LangkahAwal[]"
                                                    id="la{{ $i }}" value="{{ $item }}"
                                                    {{ in_array($item, old('LangkahAwal', $komplain->LangkahAwal ?? []), true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="la{{ $i }}">{{ $item }}</label>
                                                @if ($item === 'Lainnya')
                                                    <input type="text" name="LangkahAwalLainnya"
                                                        class="form-control form-control-sm mt-1"
                                                        placeholder="Sebutkan langkah lainnya..."
                                                        value="{{ old('LangkahAwalLainnya', $komplain->LangkahAwalLainnya) }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('LangkahAwal')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('LangkahAwalLainnya')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif ($isTahapCcp)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-dark mb-1">
                                        Tindakan CCP <span class="text-danger">*</span>
                                    </label>
                                    <div class="border rounded p-2 bg-light">
                                        @foreach ($tindakanCcpList as $i => $item)
                                            <div class="form-check small mb-1">
                                                <input class="form-check-input" type="checkbox" name="TindakanCcp[]"
                                                    id="tc{{ $i }}" value="{{ $item }}"
                                                    {{ in_array($item, old('TindakanCcp', $komplain->TindakanCcp ?? []), true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tc{{ $i }}">{{ $item }}</label>
                                                @if ($item === 'Lainnya')
                                                    <input type="text" name="TindakanCcpLainnya"
                                                        class="form-control form-control-sm mt-1"
                                                        placeholder="Sebutkan tindakan lainnya..."
                                                        value="{{ old('TindakanCcpLainnya', $komplain->TindakanCcpLainnya) }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('TindakanCcp')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('TindakanCcpLainnya')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-dark mb-1" for="KeteranganCcp">
                                        Keterangan Hasil Temuan / Investigasi CCP
                                    </label>
                                    <textarea name="KeteranganCcp" id="KeteranganCcp" class="form-control komplain-textarea" rows="4"
                                        placeholder="Tuliskan temuan investigasi, analisa akar masalah, atau rekomendasi teknis...">{{ old('KeteranganCcp', $komplain->KeteranganCcp) }}</textarea>
                                    <div class="form-text small text-muted">Format rich-text didukung untuk membuat keterangan rapi.</div>
                                    @error('KeteranganCcp')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold small text-dark mb-0">
                                            Data Pendukung Investigasi (Foto / Dokumen)
                                        </label>
                                        <small class="text-muted" id="lampiranCcpCount">0 / 10 file</small>
                                    </div>
                                    <div class="komplain-drop-zone p-3 text-center" id="dropAreaCcp" style="border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; cursor: pointer;">
                                        <input type="file" name="LampiranCcp[]" id="inputLampiranCcp" class="d-none"
                                            multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx">
                                        <div class="mb-1">
                                            <span class="komplain-upload-circle">
                                                <i class="fa fa-cloud-upload-alt text-primary fs-5"></i>
                                            </span>
                                        </div>
                                        <div class="fw-semibold text-dark small mb-1">Pilih / Seret Berkas ke Sini</div>
                                        <div class="text-muted small" style="font-size: 0.74rem;">
                                            Foto (JPG, PNG, WEBP) &bull; Dokumen (PDF, Word, Excel)
                                            <br>Maksimal 10 berkas &bull; Hingga 10 MB per file
                                        </div>
                                    </div>
                                    <div id="uploadErrorAlertCcp" class="alert alert-danger small py-1 px-2 mt-2" style="display: none;"></div>
                                    <div id="previewLampiranCcp" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    @error('LampiranCcp')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('LampiranCcp.*')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark mb-1">Catatan (Opsional)</label>
                                <textarea name="Catatan" class="form-control komplain-textarea" rows="2"
                                    placeholder="Tambahkan catatan persetujuan bila perlu..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                                <i class="fa fa-check-circle me-1"></i>
                                @if ($isTahapGhCcp)
                                    Setujui &amp; Selesaikan
                                @elseif ($isTahapLogum || $isTahapCcp)
                                    Simpan &amp; Tandatangani
                                @else
                                    Tandatangani
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Pratinjau Berkas (Foto / PDF / Dokumen) --}}
    <div class="modal fade no-print" id="modalPreviewFile" tabindex="-1" aria-labelledby="modalPreviewFileLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center gap-2 text-truncate me-2">
                        <span id="modalPreviewIcon" class="fs-5 text-primary"><i class="fa fa-file"></i></span>
                        <h6 class="modal-title fw-bold text-dark text-truncate mb-0" id="modalPreviewFileLabel">Pratinjau Berkas</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <a id="modalPreviewDownload" href="#" download class="btn btn-sm btn-outline-secondary" title="Unduh Berkas">
                            <i class="fa fa-download me-1"></i> <span class="d-none d-sm-inline">Unduh</span>
                        </a>
                        <a id="modalPreviewExternal" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="Buka di Tab Baru">
                            <i class="fa fa-external-link-alt me-1"></i> <span class="d-none d-sm-inline">Tab Baru</span>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-2 text-center bg-dark-subtle d-flex align-items-center justify-content-center" style="min-height: 440px; max-height: 80vh; overflow: auto;">
                    <div id="modalPreviewContent" class="w-100 h-100 d-flex align-items-center justify-content-center"></div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light justify-content-between border-top">
                    <small class="text-muted" id="modalPreviewMeta"></small>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .komplain-wrap {
            max-width: 920px;
            margin-left: auto;
            margin-right: auto;
        }

        .komplain-doc-card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
            border-radius: 10px;
        }

        .komplain-kop {
            border-bottom: 2px solid #d21f26;
        }

        .komplain-logo {
            width: 190px;
            max-width: 100%;
            height: auto;
            display: block;
        }

        .komplain-doc-title {
            letter-spacing: 0.6px;
            color: #0f172a;
            font-size: 1.1rem;
        }

        .komplain-meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .komplain-meta-box .komplain-k {
            flex: 0 0 72px;
        }

        .komplain-sec-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #1e293b;
            background: #f1f5f9;
            border-left: 4px solid #d21f26;
            padding: 6px 10px;
            margin-bottom: 12px;
            border-radius: 0 4px 4px 0;
        }

        .komplain-kv-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .komplain-kv {
            display: flex;
            align-items: baseline;
            gap: 8px;
            font-size: 0.9rem;
        }

        .komplain-k {
            flex: 0 0 140px;
            color: #475569;
        }

        .komplain-sep {
            flex: 0 0 8px;
            color: #64748b;
        }

        .komplain-v {
            flex: 1;
            min-width: 0;
            color: #0f172a;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 2px;
            word-break: break-word;
        }

        .komplain-check-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
            color: #64748b;
            font-size: 0.88rem;
        }

        .komplain-check-item.is-checked {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            font-weight: 500;
        }

        .komplain-check-icon {
            font-size: 1rem;
            line-height: 1.2;
        }

        .komplain-uraian-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .komplain-uraian-box p:last-child,
        .komplain-uraian-box ul:last-child,
        .komplain-uraian-box ol:last-child {
            margin-bottom: 0;
        }

        .komplain-file-card {
            width: 140px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            background: #fff;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }

        .komplain-file-card:hover {
            border-color: #0284c7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
        }

        .komplain-preview-badge {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 500;
            padding: 2px 7px;
            border-radius: 10px;
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
            white-space: nowrap;
        }

        .komplain-file-card:hover .komplain-preview-badge {
            opacity: 1;
        }

        .komplain-drop-zone {
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .komplain-drop-zone:hover,
        .komplain-drop-zone.dragover {
            border-color: #0284c7 !important;
            background-color: #f0f9ff !important;
        }

        .komplain-upload-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0f2fe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .komplain-preview-thumb-card {
            position: relative;
            width: 86px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px;
            background: #fff;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .komplain-preview-thumb-card img {
            width: 100%;
            height: 56px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 2px;
        }

        .komplain-preview-thumb-card .file-icon-box {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border-radius: 4px;
            margin-bottom: 2px;
        }

        .komplain-preview-thumb-card .file-name {
            font-size: 0.65rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #0f172a;
        }

        .komplain-preview-thumb-card .file-size {
            font-size: 0.6rem;
            color: #64748b;
        }

        .komplain-preview-thumb-card .btn-remove {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ef4444;
            color: #fff;
            border: 1px solid #fff;
            font-size: 11px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            padding: 0;
        }

        .komplain-preview-thumb-card .btn-remove:hover {
            background: #dc2626;
        }

        .komplain-file-thumb {
            width: 100%;
            height: 86px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .komplain-file-pdf {
            height: 86px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: #d21f26;
            background: #fff5f5;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .komplain-file-doc {
            height: 86px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: #1d4ed8;
            background: #eff6ff;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .komplain-file-excel {
            height: 86px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: #15803d;
            background: #f0fdf4;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .komplain-file-alt {
            height: 86px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: #475569;
            background: #f1f5f9;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .komplain-ccp-section {
            background: #f8fafc;
            border: 1px solid #cbd5e1 !important;
            border-left: 4px solid #0284c7 !important;
        }

        .komplain-ccp-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #e0f2fe;
            color: #0284c7;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .bg-primary-subtle {
            background-color: #e0f2fe !important;
        }

        .border-primary-subtle {
            border-color: #bae6fd !important;
        }

        .komplain-file-name {
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .komplain-ttd-table th,
        .komplain-ttd-table td {
            border-color: #cbd5e1 !important;
            vertical-align: middle;
        }

        .komplain-ttd-table td {
            min-width: 150px;
            height: 130px;
            padding: 12px;
        }

        .komplain-sign-card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
            border-radius: 10px;
            overflow: hidden;
            position: static;
        }

        @media (min-width: 1200px) {
            .komplain-sign-card {
                position: sticky;
                top: 84px;
                z-index: 10;
            }
        }

        .komplain-sign-header {
            background: #f8fafc;
            border-bottom: 2px solid #d21f26;
        }

        .komplain-sign-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #fee2e2;
            color: #d21f26;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .komplain-textarea {
            border-color: #cbd5e1;
            font-size: 0.9rem;
            resize: vertical;
        }

        .komplain-textarea:focus {
            border-color: #d21f26;
            box-shadow: 0 0 0 0.2rem rgba(210, 31, 38, 0.12);
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .header,
            .sidebar,
            .page-header,
            .no-print,
            .footer {
                display: none !important;
            }

            .page-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }

            .komplain-wrap,
            .komplain-print-full {
                max-width: 100% !important;
                flex: 0 0 100% !important;
                width: 100% !important;
            }

            .card {
                border: 0 !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0 !important;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ccpTextarea = document.getElementById('KeteranganCcp');
            if (ccpTextarea && typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('KeteranganCcp', {
                    height: 180,
                    removePlugins: 'elementspath,resize,cloudservices,easyimage',
                    toolbarGroups: [
                        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                        { name: 'paragraph', groups: ['list', 'blocks', 'align'] },
                        { name: 'editing', groups: ['find', 'selection'] },
                        { name: 'tools', groups: ['tools'] },
                        '/',
                        { name: 'styles' }
                    ],
                    removeButtons: 'Strike,Subscript,Superscript,CopyFormatting,Anchor,HorizontalRule,CreateDiv,Outdent,Indent,Blockquote,JustifyBlock,BidiLtr,BidiRtl,Language,Flash,Table,Image,Print,Preview,Save,NewPage,Templates,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Source,About'
                });
            }

            var formApprove = document.getElementById('formApprovalKomplain');
            if (formApprove) {
                formApprove.addEventListener('submit', function() {
                    if (typeof CKEDITOR !== 'undefined') {
                        for (var name in CKEDITOR.instances) {
                            if (CKEDITOR.instances.hasOwnProperty(name)) {
                                CKEDITOR.instances[name].updateElement();
                            }
                        }
                    }
                });
            }

            // ==========================================
            // 1. MODAL PRATINJAU BERKAS (FOTO / PDF / DOKUMEN)
            // ==========================================
            var previewModalEl = document.getElementById('modalPreviewFile');
            var previewModal = previewModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(previewModalEl) : null;
            var modalTitle = document.getElementById('modalPreviewFileLabel');
            var modalIcon = document.getElementById('modalPreviewIcon');
            var modalContent = document.getElementById('modalPreviewContent');
            var modalDownload = document.getElementById('modalPreviewDownload');
            var modalExternal = document.getElementById('modalPreviewExternal');
            var modalMeta = document.getElementById('modalPreviewMeta');

            document.querySelectorAll('.komplain-previewable-file').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    if (e.ctrlKey || e.metaKey || e.which === 2) return;
                    e.preventDefault();

                    var url = this.getAttribute('data-file-url');
                    var name = this.getAttribute('data-file-name') || 'Berkas';
                    var isImg = this.getAttribute('data-is-img') === '1';
                    var isPdf = this.getAttribute('data-is-pdf') === '1';
                    var type = (this.getAttribute('data-file-type') || '').toUpperCase();

                    if (modalTitle) modalTitle.textContent = name;
                    if (modalDownload) modalDownload.href = url;
                    if (modalExternal) modalExternal.href = url;
                    if (modalMeta) modalMeta.textContent = 'Format: ' + (type || 'FILE');

                    if (modalContent) {
                        if (isImg) {
                            if (modalIcon) modalIcon.innerHTML = '<i class="fa fa-image text-primary"></i>';
                            modalContent.innerHTML = '<img src="' + url + '" alt="' + name + '" class="img-fluid rounded shadow-sm" style="max-height: 75vh; max-width: 100%; object-fit: contain;">';
                        } else if (isPdf) {
                            if (modalIcon) modalIcon.innerHTML = '<i class="fa fa-file-pdf text-danger"></i>';
                            modalContent.innerHTML = '<iframe src="' + url + '" class="w-100 rounded" style="height: 75vh; border: 0;" title="' + name + '"></iframe>';
                        } else {
                            var iconClass = 'fa-file-alt text-secondary';
                            if (['DOC', 'DOCX'].includes(type)) iconClass = 'fa-file-word text-primary';
                            if (['XLS', 'XLSX', 'CSV'].includes(type)) iconClass = 'fa-file-excel text-success';
                            if (modalIcon) modalIcon.innerHTML = '<i class="fa ' + iconClass + '"></i>';
                            modalContent.innerHTML = '<div class="p-4 bg-white rounded shadow-sm text-center my-auto">' +
                                '<div class="fs-1 mb-2"><i class="fa ' + iconClass + '"></i></div>' +
                                '<h6 class="fw-bold text-dark mb-1">' + name + '</h6>' +
                                '<p class="text-muted small mb-3">Dokumen ini tidak mendukung pratinjau langsung di dalam browser.</p>' +
                                '<a href="' + url + '" download class="btn btn-primary btn-sm me-2"><i class="fa fa-download me-1"></i> Unduh Dokumen</a>' +
                                '<a href="' + url + '" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm"><i class="fa fa-external-link-alt me-1"></i> Buka File</a>' +
                                '</div>';
                        }
                    }

                    if (previewModal) {
                        previewModal.show();
                    }
                });
            });

            // ==========================================
            // 2. LIVE PREVIEW UPLOAD BERKAS CCP
            // ==========================================
            (function() {
                var input = document.getElementById('inputLampiranCcp');
                var dropZone = document.getElementById('dropAreaCcp');
                var box = document.getElementById('previewLampiranCcp');
                var counter = document.getElementById('lampiranCcpCount');
                var alertBox = document.getElementById('uploadErrorAlertCcp');
                if (!input || !box) return;

                var MAX_FILES = 10;
                var MAX_SIZE = 10 * 1024 * 1024; // 10 MB
                var ALLOWED_EXTS = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
                var selectedFiles = [];

                if (dropZone) {
                    dropZone.addEventListener('click', function(e) {
                        if (e.target !== input) {
                            input.click();
                        }
                    });

                    ['dragenter', 'dragover'].forEach(function(eventName) {
                        dropZone.addEventListener(eventName, function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropZone.classList.add('dragover');
                        });
                    });

                    ['dragleave', 'drop'].forEach(function(eventName) {
                        dropZone.addEventListener(eventName, function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropZone.classList.remove('dragover');
                        });
                    });

                    dropZone.addEventListener('drop', function(e) {
                        handleFiles(e.dataTransfer.files);
                    });
                }

                input.addEventListener('change', function() {
                    handleFiles(input.files);
                });

                function formatSize(bytes) {
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                }

                function showAlert(messages) {
                    if (!alertBox) return;
                    if (!messages || messages.length === 0) {
                        alertBox.style.display = 'none';
                        alertBox.innerHTML = '';
                        return;
                    }
                    var html = '<strong>Peringatan File:</strong><ul class="mb-0 ps-3">';
                    messages.forEach(function(msg) {
                        html += '<li>' + msg + '</li>';
                    });
                    html += '</ul>';
                    alertBox.innerHTML = html;
                    alertBox.style.display = 'block';
                }

                function updateCounter() {
                    if (counter) {
                        counter.textContent = selectedFiles.length + ' / ' + MAX_FILES + ' file';
                    }
                }

                function syncInputFiles() {
                    if (window.DataTransfer) {
                        try {
                            var dt = new DataTransfer();
                            selectedFiles.forEach(function(f) {
                                dt.items.add(f);
                            });
                            input.files = dt.files;
                        } catch (e) {
                            console.error('DataTransfer sync error:', e);
                        }
                    }
                    updateCounter();
                    renderPreview();
                }

                function handleFiles(fileList) {
                    var errors = [];
                    Array.from(fileList).forEach(function(file) {
                        if (selectedFiles.length >= MAX_FILES) {
                            errors.push('Maksimal ' + MAX_FILES + ' berkas.');
                            return;
                        }
                        var ext = file.name.split('.').pop().toLowerCase();
                        if (!ALLOWED_EXTS.includes(ext)) {
                            errors.push(file.name + ': format .' + ext + ' tidak didukung.');
                            return;
                        }
                        if (file.size > MAX_SIZE) {
                            errors.push(file.name + ': ukuran (' + formatSize(file.size) + ') melebihi 10 MB.');
                            return;
                        }
                        var isDuplicate = selectedFiles.some(function(f) {
                            return f.name === file.name && f.size === file.size;
                        });
                        if (!isDuplicate) {
                            selectedFiles.push(file);
                        }
                    });

                    showAlert(errors);
                    syncInputFiles();
                }

                function renderPreview() {
                    box.innerHTML = '';
                    selectedFiles.forEach(function(file, index) {
                        var card = document.createElement('div');
                        card.className = 'komplain-preview-thumb-card';

                        var btnRemove = document.createElement('button');
                        btnRemove.type = 'button';
                        btnRemove.className = 'btn-remove';
                        btnRemove.innerHTML = '&times;';
                        btnRemove.title = 'Hapus berkas ini';
                        btnRemove.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectedFiles.splice(index, 1);
                            syncInputFiles();
                        });
                        card.appendChild(btnRemove);

                        var ext = file.name.split('.').pop().toLowerCase();
                        var isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext) || file.type.startsWith('image/');

                        if (isImage) {
                            var img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            card.appendChild(img);
                        } else {
                            var iconBox = document.createElement('div');
                            iconBox.className = 'file-icon-box';
                            if (ext === 'pdf' || file.type === 'application/pdf') {
                                iconBox.className += ' text-danger bg-danger-subtle';
                                iconBox.innerHTML = '<i class="fa fa-file-pdf"></i>';
                            } else if (['doc', 'docx'].includes(ext)) {
                                iconBox.className += ' text-primary bg-primary-subtle';
                                iconBox.innerHTML = '<i class="fa fa-file-word"></i>';
                            } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
                                iconBox.className += ' text-success bg-success-subtle';
                                iconBox.innerHTML = '<i class="fa fa-file-excel"></i>';
                            } else {
                                iconBox.className += ' text-secondary bg-secondary-subtle';
                                iconBox.innerHTML = '<i class="fa fa-file-alt"></i>';
                            }
                            card.appendChild(iconBox);
                        }

                        var nameDiv = document.createElement('div');
                        nameDiv.className = 'file-name';
                        nameDiv.title = file.name;
                        nameDiv.textContent = file.name;
                        card.appendChild(nameDiv);

                        var sizeDiv = document.createElement('div');
                        sizeDiv.className = 'file-size';
                        sizeDiv.textContent = formatSize(file.size);
                        card.appendChild(sizeDiv);

                        box.appendChild(card);
                    });
                }
            })();
        });
    </script>
@endpush
