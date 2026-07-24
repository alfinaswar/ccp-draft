<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Feasibility Study</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tabler-icons@2.39.0/iconfont/tabler-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf3 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem 0;
        }

        .fs-wrapper {
            max-width: 800px;
            margin: 0 auto;
        }

        .card-custom {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.10);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #206bc4 0%, #1a5ba8 100%);
            color: #fff;
            padding: 1.5rem 1.75rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
        }

        .card-header-custom p {
            margin: 0.25rem 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .info-box {
            background: #f8fafc;
            border-left: 4px solid #206bc4;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
        }

        .dt-align-colon {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 1.6em;
        }

        .dt-align-colon .label-text {
            flex: 1 0 auto;
            text-align: left;
        }

        .dt-align-colon .colon {
            min-width: 15px;
            text-align: right;
            flex-shrink: 0;
            padding-left: 4px;
            padding-right: 4px;
            display: inline-block;
        }

        .btn-approve {
            padding: 0.65rem 1.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-approve:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(32, 107, 196, 0.3);
        }

        .pdf-preview-box {
            border: 1px solid #ddd;
            border-radius: 7px;
            overflow: hidden;
            margin: 1.5rem 0 0.5rem 0;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            min-height: 400px;
        }

        .pdf-preview-box iframe {
            border: none;
            width: 100%;
            min-height: 600px;
        }

        .alert-custom-info {
            background: #e3f0fd;
            border: 1px solid #b1ceef;
            color: #24507a;
            border-radius: 8px;
            padding: 0.9rem 1.1rem;
            font-size: 0.98rem;
            margin-bottom: 1.1rem;
        }

        @media (max-width: 767.98px) {
            .fs-wrapper {
                padding: 0 0.4rem;
                max-width: 100%;
            }
            .card-header-custom {
                padding: 1.2rem 1rem;
            }
            .card-body.p-4 {
                padding: 1rem !important;
            }
            dt.col-sm-4, dd.col-sm-8 {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
                margin-bottom: .7rem;
                padding-left: 0;
                padding-right: 0;
            }
            .pdf-preview-box iframe {
                min-height: 340px;
            }
        }
        @media (max-width: 420px) {
            .btn-approve {
                width: 100%;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="fs-wrapper">

        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark">{{ config('app.name', 'Jasuindo') }}</h3>
            <p class="text-muted mb-0">Sistem Preview Feasibility Study</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="ti ti-circle-check me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="ti ti-alert-circle me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-header card-header-custom text-center">
                <h4 class="d-inline-block"><i class="ti ti-file-description me-2"></i>Preview Feasibility Study &amp; Lampiran</h4>
                <p class="mb-0">Tinjau detail dan lampiran berikut sebelum approval</p>
            </div>
            <div class="card-body p-4">
          <div class="mb-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">
                            <div class="dt-align-colon">
                                <span class="label-text">Kode Pengajuan</span>
                                <span class="colon">:</span>
                            </div>
                        </dt>
                        <dd class="col-sm-8">{{ $pengajuan->KodePengajuan ?? '-' }}</dd>

                        <dt class="col-sm-4">
                            <div class="dt-align-colon">
                                <span class="label-text">Asal Permintaan</span>
                                <span class="colon">:</span>
                            </div>
                        </dt>
                        <dd class="col-sm-8">{{ $pengajuan->getPerusahaan->NamaLengkap ?? '-' }}</dd>
                        <dt class="col-sm-4">
                            <div class="dt-align-colon">
                                <span class="label-text">Nama Barang / Jasa</span>
                                <span class="colon">:</span>
                            </div>
                        </dt>
                        <dd class="col-sm-8">{{ $rekomendasi->getRekomedasiDetail[0]->getBarang->Nama ?? '-' }}</dd>

                        <dt class="col-sm-4">
                            <div class="dt-align-colon">
                                <span class="label-text">Tujuan Penempatan</span>
                                <span class="colon">:</span>
                            </div>
                        </dt>
                        <dd class="col-sm-8">{{ $pengajuan->getPermintaan->getDetail[0]->RencanaPenempatan ?? '-' }}</dd>
                    </dl>
                </d

                {{-- Lampiran File & Preview PDF --}}
                <div class="mb-4">
                    <h5 class="mb-1">Lampiran Feasibility Study dalam PDF</h5>
                    <ul>
                        @php
                            $filename = $fileName ?? null;
                            $idFs = $fs->id ?? null;
                            $downloadUrl = $downloadUrl ?? null;
                            // Fallback if downloadUrl not provided by backend
                            if(!$downloadUrl && $filename && $idFs){
                                $downloadUrl = url('storage/rekap-fs/fs-' . $idFs . '/' . $filename);
                            }
                        @endphp
                        @if($downloadUrl && $filename)
                            <li>
                                <a href="{{ $downloadUrl }}"
                                   download
                                   title="Download file: {{ $filename }}">
                                    <i class="ti ti-download"></i>
                                    <span>Lampiran Feasibility Study</span>
                                </a>
                            </li>
                        @else
                            <li class="text-danger">File lampiran PDF FS tidak tersedia.</li>
                        @endif
                    </ul>
                    @if($downloadUrl && $filename)
                        <div class="pdf-preview-box mt-3">
                            {{-- Untuk browser yang support PDF --}}
                            <iframe src="{{ $downloadUrl }}#toolbar=1&navpanes=0" allowfullscreen></iframe>
                        </div>
                        <div class="text-muted small mt-2">
                            <i class="ti ti-info-circle"></i>
                            Jika preview file tidak tampil, silakan <a href="{{ $downloadUrl }}" target="_blank">buka di tab baru</a> atau <a href="{{ $downloadUrl }}" download>download file PDF</a>.
                        </div>
                    @endif
                </div>

                {{-- LINK APPROVAL --}}
                @php
                    $approvalUrl = route(
                        'fs.approve',
                        $token ?? ''
                    );
                @endphp

                <div class="mb-4 text-center">
                    <a href="{{ $approvalUrl }}" class="btn btn-success btn-approve px-5" target="_blank" rel="noopener">
                        <i class="ti ti-check me-1"></i> Approve Sekarang
                    </a>
                </div>
            </div>
        </div>

        <p class="text-center text-muted small mt-4">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Email ini dikirim secara otomatis, mohon tidak membalas.
        </p>
    </div>
</div>
</body>
</html>
