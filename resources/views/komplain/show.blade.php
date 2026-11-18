@extends('layouts.app')

@section('content')
    @php
        $approvalKadiv = $approval->firstWhere('Urutan', 1);
        $approvalGh = $approval->firstWhere('Urutan', 2);
        $punyaApproval = $approval->where('UserId', (string) auth()->id())->where('Status', 'Pending')->first();
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
                <p class="ph-subtitle">{{ $komplain->Status }}</p>
            </div>
            <div class="ph-right">
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="fa fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>

    @if (Session::get('success'))
        <div class="alert alert-success no-print">{{ Session::get('success') }}</div>
    @endif
    @if (Session::get('error'))
        <div class="alert alert-danger no-print">{{ Session::get('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4 class="text-danger fw-bold mb-3">RS AWAL BROS</h4>
            <h5 class="text-center text-uppercase mb-4">Formulir Evaluasi BMHP Umum</h5>

            <div class="row mb-1">
                <div class="col-sm-4 fw-semibold">Nama RS</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->NamaRS }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4 fw-semibold">Tanggal</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">
                    {{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}</div>
            </div>

            <h6 class="fw-bold text-uppercase mt-4">A. Identitas Pelapor</h6>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Nama Pelapor</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->NamaPelapor }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Jabatan</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->Jabatan }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Unit Kerja</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->UnitKerja }}</div>
            </div>

            <h6 class="fw-bold text-uppercase mt-4">B. Data BMHP Umum</h6>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Nama Barang</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->NamaBarang }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Jenis/Kategori</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->JenisKategori }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Merk</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->Merk }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-4">&bull;&nbsp; Nama Distributor/Penyedia</div>
                <div class="col-1">:</div>
                <div class="col border-bottom">{{ $komplain->Distributor }}</div>
            </div>

            <h6 class="fw-bold text-uppercase mt-4">C. Parameter Evaluasi BMHP Umum</h6>
            <ul class="list-unstyled mb-3">
                @foreach ($parameterEvaluasi as $item)
                    @php $dipilih = in_array($item, $komplain->ParameterEvaluasi ?? [], true); @endphp
                    <li class="mb-1">
                        <span class="me-2 {{ $dipilih ? 'text-success' : 'text-muted' }}">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                        {{ $item }}@if ($item !== 'Lainnya')
                            :
                        @endif
                        @if ($item === 'Lainnya')
                            <span class="border-bottom d-inline-block ms-2">{{ $komplain->ParameterLainnya }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <h6 class="fw-bold text-uppercase mt-4">D. Deskripsi Evaluasi</h6>
            <div class="mb-2">&bull;&nbsp; Uraian Singkat</div>
            <div class="border rounded p-3 mb-3">
                @php
                    $uraian = $komplain->UraianSingkat ?? '';
                    $uraianHtml = str_contains($uraian, '<') ? $uraian : nl2br(e($uraian));
                @endphp
                {!! $uraianHtml !!}
            </div>

            <div class="mb-2">&bull;&nbsp; Data Pendukung</div>
            @if (!empty($komplain->Lampiran))
                <div class="d-flex flex-wrap gap-3 mb-3">
                    @foreach ($komplain->Lampiran as $file)
                        @php $url = asset('storage/' . $file['path']); @endphp
                        <div class="text-center border rounded p-2" style="width: 140px;">
                            @if (str_starts_with($file['tipe'] ?? '', 'image/'))
                                <a href="{{ $url }}" target="_blank" rel="noopener">
                                    <img src="{{ $url }}" alt="{{ $file['nama'] }}" class="img-fluid rounded mb-2"
                                        style="height: 90px; object-fit: cover; width: 100%;">
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                    class="d-block mb-2 text-danger" style="font-size: 40px; line-height: 90px;">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                            @endif
                            <div class="small text-break">{{ $file['nama'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-3">Tidak ada berkas pendukung.</p>
            @endif

            @if (!empty($komplain->DataPendukung))
                {{-- data lama sebelum fitur upload dipakai --}}
                <ul class="list-unstyled mb-3">
                    @foreach ($dataPendukung as $item)
                        @php $dipilih = in_array($item, $komplain->DataPendukung ?? [], true); @endphp
                        <li class="mb-1">
                            <span class="me-2 {{ $dipilih ? 'text-success' : 'text-muted' }}">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                            {{ $item }}@if ($item !== 'Lainnya')
                                :
                            @endif
                            @if ($item === 'Lainnya')
                                <span class="border-bottom d-inline-block ms-2">{{ $komplain->DataPendukungLainnya }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <h6 class="fw-bold text-uppercase mt-4">E. Tanda Tangan (Nama dan Tanggal)</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-2">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Pelapor</th>
                            <th class="fw-semibold">Kepala Divisi</th>
                            <th class="fw-semibold">Group Head</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $komplain->NamaPelapor }}</div>
                                <small>{{ \Carbon\Carbon::parse($komplain->Tanggal)->format('d/m/Y') }}</small>
                            </td>
                            @foreach ([$approvalKadiv, $approvalGh] as $ttd)
                                <td>
                                    @if ($ttd && $ttd->Status === 'Approved')
                                        <small class="d-block mb-1">
                                            {{ $ttd->TanggalApprove ? \Carbon\Carbon::parse($ttd->TanggalApprove)->format('d/m/Y') : '' }}
                                        </small>
                                        @if (!empty($ttd->qrCode))
                                            <img src="data:image/png;base64,{{ $ttd->qrCode }}" alt="QR Tanda Tangan"
                                                width="70" class="mb-1">
                                        @endif
                                        <div class="fw-bold">{{ $ttd->Nama }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Belum ditandatangani</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="small mb-3">Catatan: Formulir dilampirkan bersama BMHP UMUM</p>

            <h6 class="fw-bold text-uppercase mt-4">E. Tindak Lanjut</h6>
            <div class="mb-2">&bull;&nbsp; Langkah Awal</div>
            <ul class="list-unstyled mb-3">
                @foreach ($langkahAwalList as $item)
                    @php $dipilih = in_array($item, $komplain->LangkahAwal ?? [], true); @endphp
                    <li class="mb-1">
                        <span class="me-2 {{ $dipilih ? 'text-success' : 'text-muted' }}">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                        {{ $item }}@if ($item !== 'Lainnya')
                            :
                        @endif
                        @if ($item === 'Lainnya')
                            <span class="border-bottom d-inline-block ms-2">{{ $komplain->LangkahAwalLainnya }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mb-1">&bull;&nbsp; Tanda Tangan Logistik Umum :</div>
            <div class="mb-4 ps-3">
                @if ($komplain->LogistikUmumOleh)
                    <div class="fw-bold">{{ $komplain->LogistikUmumOleh }}</div>
                    <small class="text-muted">
                        {{ $komplain->TanggalLogistikUmum ? \Carbon\Carbon::parse($komplain->TanggalLogistikUmum)->format('d/m/Y H:i') : '' }}
                    </small>
                @else
                    <span class="text-muted fst-italic">Belum diisi</span>
                @endif
            </div>

            <h6 class="fw-bold text-uppercase mt-4">F. Tindak Lanjut CCP</h6>
            <div class="mb-2">&bull;&nbsp; Tindakan CCP</div>
            <ul class="list-unstyled mb-3">
                @foreach ($tindakanCcpList as $item)
                    @php $dipilih = in_array($item, $komplain->TindakanCcp ?? [], true); @endphp
                    <li class="mb-1">
                        <span class="me-2 {{ $dipilih ? 'text-success' : 'text-muted' }}">{!! $dipilih ? '&#9745;' : '&#9744;' !!}</span>
                        {{ $item }}@if ($item !== 'Lainnya')
                            :
                        @endif
                        @if ($item === 'Lainnya')
                            <span class="border-bottom d-inline-block ms-2">{{ $komplain->TindakanCcpLainnya }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mb-1">&bull;&nbsp; Tanda Tangan CCP :</div>
            <div class="mb-4 ps-3">
                @if ($komplain->CcpOleh)
                    <div class="fw-bold">{{ $komplain->CcpOleh }}</div>
                    <small class="text-muted">
                        {{ $komplain->TanggalCcp ? \Carbon\Carbon::parse($komplain->TanggalCcp)->format('d/m/Y H:i') : '' }}
                    </small>
                @else
                    <span class="text-muted fst-italic">Belum diisi</span>
                @endif
            </div>

            <div class="row mt-5">
                <div class="col-6">
                    <div class="fw-bold"><u>Ruth Minar Hutagalung, SE</u></div>
                    <div class="fst-italic">CCP dan BMHP Umum</div>
                </div>
                <div class="col-6">
                    <div class="fw-bold"><u>dr. Putri Ingen Setiasih, MARS</u></div>
                    <div class="fst-italic">Group Head Coorporate Procurement</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi internal --}}
    <div class="no-print">
        @if ($punyaApproval)
            <div class="card">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0 text-white">Tanda Tangan Anda</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('komplain.approve', encrypt($komplain->id)) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="Catatan" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-success"><i class="fa fa-check"></i> Tandatangani</button>
                    </form>
                </div>
            </div>
        @endif

        @if ($komplain->Status === 'Menunggu Tindak Lanjut Logistik Umum' && $bisaLogum)
            <div class="card">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0 text-white">Tindak Lanjut Logistik Umum</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('komplain.logistik-umum', encrypt($komplain->id)) }}" method="POST">
                        @csrf
                        @foreach ($langkahAwalList as $i => $item)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="LangkahAwal[]"
                                    id="la{{ $i }}" value="{{ $item }}">
                                <label class="form-check-label" for="la{{ $i }}">{{ $item }}</label>
                                @if ($item === 'Lainnya')
                                    <input type="text" name="LangkahAwalLainnya"
                                        class="form-control form-control-sm mt-1" value="{{ old('LangkahAwalLainnya') }}">
                                @endif
                            </div>
                        @endforeach
                        @error('LangkahAwal')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('LangkahAwalLainnya')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <button class="btn btn-primary mt-3"><i class="fa fa-save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        @endif

        @if ($komplain->Status === 'Menunggu Tindakan CCP' && $bisaCcp)
            <div class="card">
                <div class="card-header bg-dark">
                    <h5 class="card-title mb-0 text-white">Tindak Lanjut CCP</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('komplain.tindakan-ccp', encrypt($komplain->id)) }}" method="POST">
                        @csrf
                        @foreach ($tindakanCcpList as $i => $item)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="TindakanCcp[]"
                                    id="tc{{ $i }}" value="{{ $item }}">
                                <label class="form-check-label" for="tc{{ $i }}">{{ $item }}</label>
                                @if ($item === 'Lainnya')
                                    <input type="text" name="TindakanCcpLainnya"
                                        class="form-control form-control-sm mt-1" value="{{ old('TindakanCcpLainnya') }}">
                                @endif
                            </div>
                        @endforeach
                        @error('TindakanCcp')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('TindakanCcpLainnya')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <button class="btn btn-success mt-3"><i class="fa fa-check"></i> Simpan Keputusan</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <style>
        @media print {

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
