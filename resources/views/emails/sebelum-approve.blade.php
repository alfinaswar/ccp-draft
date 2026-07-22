<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justifikasi Approval - {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf3 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem 0;
        }

        .approval-wrapper {
            max-width: 720px;
            margin: 0 auto;
        }

        .card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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
            font-size: 0.9rem;
        }

        .info-box {
            background: #f8fafc;
            border-left: 4px solid #206bc4;
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
        }

        .info-box .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7a8c;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.15rem;
        }

        .info-box .info-value {
            font-size: 0.95rem;
            color: #1e293b;
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-label .required {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-control:focus {
            border-color: #206bc4;
            box-shadow: 0 0 0 0.2rem rgba(32, 107, 196, 0.15);
        }

        .helper-text {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.35rem;
        }

        .invalid-feedback {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        .is-invalid~.invalid-feedback {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .alert-warning-custom {
            background: #fff8e1;
            border: 1px solid #ffd54f;
            color: #7a5a00;
            border-radius: 8px;
            padding: 0.85rem 1.1rem;
            font-size: 0.9rem;
        }

        .char-counter {
            font-size: 0.78rem;
            color: #94a3b8;
            text-align: right;
            margin-top: 0.25rem;
        }

        /* Styling untuk dt/dd alignment */
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
    </style>
</head>

<body>

    <div class="container">
        <div class="approval-wrapper">

            {{-- Header --}}
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">{{ config('app.name', 'Jasuindo') }}</h3>
                <p class="text-muted mb-0">Sistem Persetujuan HTA / GPA</p>
            </div>

            {{-- Alert Error dari Session --}}
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="ti ti-alert-circle me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            {{-- Card Utama --}}
            <div class="card">
                <div class="card-header card-header-custom text-center">
                    <h4 class="d-inline-block"><i class="ti ti-file-description me-2"></i>Justifikasi Approval</h4>
                    <p class="mb-0">Wajib diisi sebelum proses persetujuan dilakukan</p>
                </div>

                <div class="card-body p-4">

                    {{-- Info Dokumen --}}
                    <div class="mb-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">
                                <div class="dt-align-colon">
                                    <span class="label-text">Nomor Pengajuan</span>
                                    <span class="colon">:</span>
                                </div>
                            </dt>
                            <dd class="col-sm-8">{{ $penilai->getDokumenHTAGPA->getPengajuan->KodePengajuan ?? '-' }}
                            </dd>

                            <dt class="col-sm-4">
                                <div class="dt-align-colon">
                                    <span class="label-text">Pemohon</span>
                                    <span class="colon">:</span>
                                </div>
                            </dt>
                            <dd class="col-sm-8">
                                {{ $penilai->getDokumenHTAGPA->getPengajuan->getPerusahaan->NamaLengkap ?? '-' }}
                            </dd>

                            <dt class="col-sm-4">
                                <div class="dt-align-colon">
                                    <span class="label-text">Tanggal Pengajuan</span>
                                    <span class="colon">:</span>
                                </div>
                            </dt>
                            <dd class="col-sm-8">{{ $penilai->getDokumenHTAGPA->getPengajuan->created_at ?? '-' }}</dd>
                        </dl>
                    </div>

                    {{-- Info Approver --}}
                    <div class="alert alert-warning-custom d-flex align-items-start mb-4">
                        <i class="ti ti-info-circle me-2 fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Yth. {{ $penilai->Nama ?? 'Bapak/Ibu' }},</strong><br>
                            Anda menerima permintaan persetujuan ini.<br>
                            Mohon isi justifikasi di bawah ini sebagai dasar pertimbangan persetujuan.
                        </div>
                    </div>

                    {{-- Form Justifikasi --}}
                    <form action="{{ route('htagpa.submitJustifikasi', $penilai->ApprovalToken) }}" method="POST"
                        id="formJustifikasi">
                        @csrf

                        <div class="mb-3">
                            <label for="justifikasi" class="form-label">
                                <i class="ti ti-note me-1"></i>
                                Justifikasi Pembelian / Pemilihan Barang/Jasa
                                <span class="required">*</span>
                            </label>
                            <textarea required class="form-control @error('justifikasi') is-invalid @enderror" id="justifikasi" name="justifikasi"
                                rows="6" maxlength="1000"
                                placeholder="Jelaskan alasan pembelian/pemilihan barang/jasa ini, termasuk urgensi, manfaat, dan kesesuaian dengan kebutuhan departemen...">{{ old('justifikasi') }}</textarea>

                            <div class="invalid-feedback">
                                @error('justifikasi')
                                    {{ $message }}
                                @enderror
                            </div>
                            <div class="helper-text">
                                <i class="ti ti-info-circle me-1"></i>
                                Minimal 20 karakter. Jelaskan alasan, urgensi, dan manfaat pembelian secara ringkas.
                            </div>
                            <div class="char-counter">
                                <span id="charCount">0</span> / 1000 karakter
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Tombol Aksi --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger btn-approve px-4"
                                    onclick="handleReject()">
                                    <i class="ti ti-circle-x me-1"></i> Tolak
                                </button>
                                <button type="submit" class="btn btn-primary btn-approve px-4" id="btnSubmit">
                                    <i class="ti ti-check me-1"></i> Lanjutkan Approval
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-4">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Email ini dikirim secara otomatis, mohon tidak
                membalas.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Character counter (pure JS, tanpa library)
        const textarea = document.getElementById('justifikasi');
        const charCount = document.getElementById('charCount');
        const form = documentJustifikasi = document.getElementById('formJustifikasi');
        const btnSubmit = document.getElementById('btnSubmit');

        // Update counter saat mengetik
        textarea.addEventListener('input', function() {
            const len = this.value.length;
            charCount.textContent = len;

            if (len > 1000) {
                charCount.style.color = '#dc3545';
            } else if (len >= 20) {
                charCount.style.color = '#198754';
            } else {
                charCount.style.color = '#94a3b8';
            }
        });

        // Trigger initial count jika ada old value
        if (textarea.value) {
            textarea.dispatchEvent(new Event('input'));
        }

        // Submit biasa - langsung kirim, tanpa konfirmasi
        formJustifikasi.addEventListener('submit', function() {
            // Disable tombol & tampilkan loading (mencegah double submit)
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });
    </script>

</body>

</html>
