@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Pengajuan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ajukan.index') }}">Pengajuan Pembelian</a></li>
                    <li class="breadcrumb-item active">Tambah Pengajuan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('ajukan.store') }}" method="POST" enctype="multipart/form-data" id="form-pengajuan">
                @csrf
                <div class="card mb-4">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0">Formulir Pengajuan Pembelian</h4>
                        <p class="card-text mb-0">
                            Silakan isi data pengajuan pembelian di bawah ini.
                        </p>
                    </div>
                    <div class="card-body">
                        {{-- Table pengajuan pembelian --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label"><strong>Tanggal</strong></label>
                                <input type="date" name="pengajuan[tanggal]" id="tanggal"
                                    class="form-control @error('pengajuan.tanggal') is-invalid @enderror"
                                    value="{{ old('pengajuan.tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                    placeholder="Pilih tanggal">
                                @error('pengajuan.tanggal')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="pengajuan[id_permintaan]" value="{{ $permintaan->id }}">
                            </div>
                            <div class="col-md-4">
                                <label for="departemen" class="form-label"><strong>Permintaan Dari
                                        Departemen</strong></label>
                                <select name="pengajuan[departemen]" id="departemen"
                                    class="form-select select2 @error('pengajuan.departemen') is-invalid @enderror"
                                    readonly>
                                    @if (isset($permintaan->getDepartemen))
                                        <option value="{{ $permintaan->Departemen }}" selected>
                                            {{ $permintaan->getDepartemen->Nama ?? '-' }}
                                        </option>
                                    @else
                                        <option value="">Pilih Departemen</option>
                                        @foreach ($departemen as $dept)
                                            <option value="{{ $dept->Kode }}"
                                                {{ old('pengajuan.departemen') == $dept->Kode ? 'selected' : '' }}>
                                                {{ $dept->Nama ?? '-' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('pengajuan.departemen')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="jenis" class="form-label"><strong>Jenis</strong></label>
                                <select name="pengajuan[jenis]" id="jenis"
                                    class="form-select select2 @error('pengajuan.jenis') is-invalid @enderror" readonly>
                                    <option value="">Pilih Jenis Pengajuan</option>
                                    @foreach ($JenisPengajuan as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            {{ old('pengajuan.jenis', $permintaan->Jenis) == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->Nama ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pengajuan.jenis')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tujuan" class="form-label"><strong>Tujuan Pengajuan</strong></label>
                                <select name="pengajuan[tujuan]" id="tujuan"
                                    class="form-select select2 @error('pengajuan.tujuan') is-invalid @enderror">
                                    <option value="">Pilih Tujuan Pengajuan</option>
                                    <option value="Pembelian Baru"
                                        {{ old('pengajuan.tujuan', $permintaan->Tujuan ?? '') == 'Pembelian Baru' ? 'selected' : '' }}>
                                        Pembelian Baru
                                    </option>
                                    <option value="Penggantian"
                                        {{ old('pengajuan.tujuan', $permintaan->Tujuan ?? '') == 'Penggantian' ? 'selected' : '' }}>
                                        Penggantian
                                    </option>
                                    <option value="Perbaikan"
                                        {{ old('pengajuan.tujuan', $permintaan->Tujuan ?? '') == 'Perbaikan' ? 'selected' : '' }}>
                                        Perbaikan</option>
                                    <option value="Adendum"
                                        {{ old('pengajuan.tujuan', $permintaan->Tujuan ?? '') == 'Adendum' ? 'selected' : '' }}>
                                        Adendum</option>
                                </select>
                                @error('pengajuan.tujuan')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="perkiraan_utilisasi" class="form-label"><strong>Perkiraan Utilitasi
                                        Bulanan</strong></label>
                                <input type="text" name="pengajuan[perkiraan_utilitasi_bulanan]" id="perkiraan_utilisasi"
                                    class="form-control @error('pengajuan.perkiraan_utilitasi_bulanan') is-invalid @enderror"
                                    value="{{ old('pengajuan.perkiraan_utilitasi_bulanan', isset($permintaan->getPengajuanPembelian) ? $permintaan->getPengajuanPembelian->PerkiraanUtilitasiBulanan : '') }}"
                                    placeholder="Contoh: 200 jam/bulan, 30 unit/bulan, dll">
                                <small class="text-danger">Ketik "-" jika barang jasa / umum</small>
                                @error('pengajuan.perkiraan_utilitasi_bulanan')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="perkiraan_bep" class="form-label"><strong>Perkiraan BEP Pada
                                        Tahun</strong></label>
                                @php
                                    $selectedBepTahun = old(
                                        'pengajuan.perkiraan_bep_pada_tahun',
                                        isset($permintaan->getPengajuanPembelian)
                                            ? $permintaan->getPengajuanPembelian->PerkiraanBepPadaTahun
                                            : '',
                                    );
                                    $tahunSekarang = date('Y');
                                    $tahunAkhir = $tahunSekarang + 9;
                                @endphp
                                <select name="pengajuan[perkiraan_bep_pada_tahun]" id="perkiraan_bep"
                                    class="form-select select2 @error('pengajuan.perkiraan_bep_pada_tahun') is-invalid @enderror">
                                    <option value="">Pilih tahun perkiraan BEP</option>
                                    @for ($tahun = $tahunSekarang; $tahun <= $tahunAkhir; $tahun++)
                                        <option value="{{ $tahun }}"
                                            {{ $selectedBepTahun == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endfor
                                    <option value="-" {{ $selectedBepTahun == '-' ? 'selected' : '' }}>-</option>
                                </select>
                                <small class="text-danger">Ketik "-" jika barang jasa / umum</small>
                                @error('pengajuan.perkiraan_bep_pada_tahun')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="rkap" class="form-label"><strong>RAB Project / RKAP</strong></label>
                                <input type="text" name="pengajuan[rkap]" id="rkap"
                                    class="form-control @error('pengajuan.rkap') is-invalid @enderror"
                                    value="{{ old('pengajuan.rkap', isset($permintaan->getPengajuanPembelian) ? $permintaan->getPengajuanPembelian->Rkap : '') }}"
                                    placeholder="Masukkan RKAP">
                                <small class="text-danger">Ketik "-" jika barang medis</small>
                                @error('pengajuan.rkap')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="nominal_rkap" class="form-label"><strong>Nominal RKAP</strong></label>
                                <input type="text" name="pengajuan[nominal_rkap]" id="nominal_rkap"
                                    class="form-control @error('pengajuan.nominal_rkap') is-invalid @enderror currency-input-global"
                                    value="{{ old('pengajuan.nominal_rkap', isset($permintaan->getPengajuanPembelian) ? $permintaan->getPengajuanPembelian->NominalRkap : '') }}"
                                    placeholder="Contoh: 1.000.000">
                                @error('pengajuan.nominal_rkap')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xxl-12 col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Vendor</div>
                                    </div>
                                    <div class="card-body">
                                        {{-- Tab Nav --}}
                                        <ul class="nav nav-tabs mb-3" id="vendorTab" role="tablist">
                                            @for ($i = 0; $i < 3; $i++)
                                                @php
                                                    $canActivate = true;
                                                    if ($i > 0) {
                                                        $prevVendorFilled = false;
                                                        if (
                                                            isset(
                                                                $permintaan->getPengajuanPembelian->getVendor[$i - 1],
                                                            ) &&
                                                            $permintaan->getPengajuanPembelian->getVendor[$i - 1]
                                                                ->NamaVendor
                                                        ) {
                                                            $prevVendorFilled = true;
                                                        } elseif (old("vendors.$i.vendor_id")) {
                                                            $prevVendorFilled = true;
                                                        } elseif (request()->old('vendors.' . $i . '.vendor_id')) {
                                                            $prevVendorFilled = true;
                                                        }
                                                        $canActivate = $prevVendorFilled;
                                                    }
                                                @endphp
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{ $i == 0 ? 'active' : '' }}"
                                                        id="vendor-tab-{{ $i }}" data-bs-toggle="tab"
                                                        data-bs-target="#vendor-panel-{{ $i }}" type="button"
                                                        role="tab" aria-controls="vendor-panel-{{ $i }}"
                                                        aria-selected="{{ $i == 0 ? 'true' : 'false' }}"
                                                        {{ $i > 0 && !$canActivate ? 'disabled style=pointer-events:none;opacity:0.6;' : '' }}>
                                                        Vendor {{ $i + 1 }}
                                                    </button>
                                                </li>
                                            @endfor
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    for (let i = 1; i < 3; i++) {
                                                        let btn = document.getElementById("vendor-tab-" + i);
                                                        if (btn) {
                                                            btn.addEventListener('click', function(e) {
                                                                let prevSelect = document.querySelector(`#vendor_list_${i-1}_id`);
                                                                if (prevSelect && (!prevSelect.value || prevSelect.value === "")) {
                                                                    e.preventDefault();
                                                                    e.stopPropagation();
                                                                    btn.blur();
                                                                    alert("Isi vendor sebelumnya terlebih dahulu.");
                                                                    return false;
                                                                }
                                                            });
                                                        }
                                                    }
                                                });
                                            </script>
                                        </ul>
                                        <div class="tab-content" id="vendorTabContent">
                                            <div class="alert alert-info mb-3" role="alert">
                                                <strong>Petunjuk:</strong> Untuk setiap tab vendor, isilah data vendor
                                                secara berurutan, mulai dari Vendor 1.
                                                Tab vendor berikutnya (Vendor 2) baru bisa diisi setelah vendor sebelumnya
                                                lengkap dipilih.
                                                Vendor ke-3 <strong>bersifat opsional</strong>: silakan dikosongkan apabila
                                                tidak ingin mengisi vendor ketiga.
                                            </div>
                                            @for ($i = 0; $i < 3; $i++)
                                                <div class="tab-pane fade {{ $i == 0 ? 'show active' : '' }}"
                                                    id="vendor-panel-{{ $i }}" role="tabpanel"
                                                    aria-labelledby="vendor-tab-{{ $i }}">
                                                    <div class="row mb-3">
                                                        <div class="col-xl-6">
                                                            <div class="card">
                                                                <div class="row g-0">
                                                                    <div class="col-md-8">
                                                                        <div class="card-header">
                                                                            <div class="card-title">
                                                                                <label
                                                                                    for="vendor_list_{{ $i }}_id"
                                                                                    class="form-label mb-0"><strong>Pilih
                                                                                        Vendor</strong></label>
                                                                                <select
                                                                                    class="form-control select2 main-vendor-select"
                                                                                    id="vendor_list_{{ $i }}_id"
                                                                                    name="vendors[{{ $i }}][vendor_id]"
                                                                                    style="width:100%;"
                                                                                    data-placeholder="Pilih Vendor"
                                                                                    onchange="updateVendorInfoAndLock({{ $i }})">
                                                                                    <option value="">-- Pilih Vendor
                                                                                        --</option>
                                                                                    @foreach ($vendor as $v)
                                                                                        <option
                                                                                            value="{{ $v->id }}"
                                                                                            data-namapic="{{ $v->NamaPic ?? '' }}"
                                                                                            data-nohppic="{{ $v->NoHpPic ?? '' }}"
                                                                                            {{ isset($permintaan->getPengajuanPembelian->getVendor[$i]) && $permintaan->getPengajuanPembelian->getVendor[$i]->NamaVendor == $v->id ? 'selected' : '' }}>
                                                                                            {{ $v->Nama }}

                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div id="vendor_info_{{ $i }}">
                                                                                <h6 class="card-title fw-semibold mb-2">
                                                                                    Isi Nama Pic dan Kontak Pic <span
                                                                                        class="text-danger">*</span>
                                                                                </h6>


                                                                                <table class="table table-bordered mb-0">
                                                                                    <tr>
                                                                                        <th>Nama PIC</th>
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                class="form-control vendor_namapic"
                                                                                                name="vendors[{{ $i }}][nama_pic]"
                                                                                                placeholder="Masukan Nama PIC"
                                                                                                @if (isset($permintaan->getPengajuanPembelian->getVendor[$i]) &&
                                                                                                        $permintaan->getPengajuanPembelian->getVendor[$i]->NamaVendor) value="{{ $permintaan->getPengajuanPembelian->getVendor[$i]->NamaPic ?? '' }}" @endif>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>No HP PIC</th>
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                class="form-control vendor_nohppic"
                                                                                                name="vendors[{{ $i }}][no_hp_pic]"
                                                                                                placeholder="Masukan Kontak PIC"
                                                                                                @if (isset($permintaan->getPengajuanPembelian->getVendor[$i]) &&
                                                                                                        $permintaan->getPengajuanPembelian->getVendor[$i]->KontakPic) value="{{ $permintaan->getPengajuanPembelian->getVendor[$i]->KontakPic ?? '' }}" @endif>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <img src="{{ asset('assets/img/ccp/vendor.png') }}"
                                                                            class="img-fluid rounded-end object-fit-cover h-100 w-100"
                                                                            alt="...">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-6">
                                                            <div class="col-md-12">
                                                                <label
                                                                    for="vendor_list_{{ $i }}_penawaran_file"
                                                                    class="form-label"><strong>Upload Surat Penawaran
                                                                        Vendor <span
                                                                            class="text-danger">*</span></strong></label>
                                                                <input
                                                                    class="form-control @error('vendors.' . $i . '.penawaran_file.0') is-invalid @enderror"
                                                                    type="file"
                                                                    id="vendor_list_{{ $i }}_penawaran_file"
                                                                    name="vendors[{{ $i }}][penawaran_file][]"
                                                                    accept=".pdf" multiple
                                                                    style="border: 2px dashed #ced4da; padding: 20px;"
                                                                    ondragover="this.classList.add('dragover')"
                                                                    ondragleave="this.classList.remove('dragover')"
                                                                    onchange="this.classList.remove('dragover')">
                                                                <small class="form-text text-muted">
                                                                    <span class="fw-bold text-danger">File wajib diupload
                                                                        dalam format PDF, ukuran maksimal 5 MB.</span> <br>
                                                                    Anda bisa drag and drop file di area ini atau klik untuk
                                                                    memilih file.
                                                                </small>

                                                                @if ($errors->has('vendors.' . $i . '.penawaran_file.0'))
                                                                    <div class="invalid-feedback d-block"
                                                                        id="error-vendor-tab-{{ $i }}">
                                                                        {{ $errors->first('vendors.' . $i . '.penawaran_file.0') }}
                                                                    </div>
                                                                    <script>
                                                                        document.addEventListener('DOMContentLoaded', function() {
                                                                            var idx = {{ $i }};
                                                                            if (idx > 0) {
                                                                                var tabTrigger = document.querySelector(
                                                                                    '[data-bs-toggle="tab"][data-bs-target="#vendor-panel-{{ $i }}"]'
                                                                                );
                                                                                if (tabTrigger) {
                                                                                    var tab = new bootstrap.Tab(tabTrigger);
                                                                                    tab.show();
                                                                                }
                                                                            }
                                                                        });
                                                                    </script>
                                                                @endif
                                                                @php
                                                                    // Cek jika data penawaran vendor ada
                                                                    $has_penawaran = false;
                                                                    $preview_files = [];
                                                                    if (
                                                                        isset(
                                                                            $permintaan->getPengajuanPembelian
                                                                                ->getVendor[$i],
                                                                        ) &&
                                                                        $permintaan->getPengajuanPembelian->getVendor[
                                                                            $i
                                                                        ]->SuratPenawaranVendor
                                                                    ) {
                                                                        $has_penawaran = true;
                                                                        // handle multiple file (assuming it's stored as JSON or separated by ; )
    $raw =
        $permintaan->getPengajuanPembelian
            ->getVendor[$i]->SuratPenawaranVendor;
    if (is_array($raw)) {
        $preview_files = $raw;
    } else {
        $decoded = @json_decode($raw, true);
        if (is_array($decoded)) {
            $preview_files = $decoded;
        } else {
            $preview_files = preg_split(
                '/[;,]/',
                                                                                    $raw,
                                                                                    -1,
                                                                                    PREG_SPLIT_NO_EMPTY,
                                                                                );
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                @if ($has_penawaran && !empty($preview_files))
                                                                    <div class="mt-2">
                                                                        <strong>Preview Surat Penawaran Vendor:</strong>
                                                                        <ul class="list-unstyled">
                                                                            @foreach ($preview_files as $f)
                                                                                <li>
                                                                                    <a href="{{ asset('storage/penawaran_vendor/' . $f) }}"
                                                                                        target="_blank"
                                                                                        class="btn btn-sm btn-info">
                                                                                        <i class="fa fa-eye"></i> Preview
                                                                                        @php
                                                                                            $namafile = basename($f);
                                                                                            $namafile =
                                                                                                strlen($namafile) > 30
                                                                                                    ? substr(
                                                                                                            $namafile,
                                                                                                            0,
                                                                                                            27,
                                                                                                        ) . '...'
                                                                                                    : $namafile;
                                                                                        @endphp
                                                                                        {{ $namafile }}
                                                                                    </a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                                @error('vendors.' . $i . '.penawaran_file')
                                                                    <div class="text-danger mt-1">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="alert alert-danger mt-2"
                                                        style="border: 1px solid #f5c6cb;">
                                                        <i class="fa fa-exclamation-triangle"></i>
                                                        <strong>Penting:</strong> Masukkan <b>Harga dari Vendor</b> kemudian
                                                        klik <b>/ PPN</b> untuk mencari harga tanpa PPN pada form di bawah
                                                        ini.
                                                    </div>
                                                    <div class="table-responsive">
                                                        <!-- Table list vendor detail per vendor tab -->
                                                        <table class="table align-middle"
                                                            id="table-detail-vendor-{{ $i }}">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Barang</th>
                                                                    {{-- <th>Merek</th> --}}
                                                                    <th>Nama Vendor</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Harga Satuan (Tanpa PPN)</th>
                                                                    <th>Jenis Diskon</th>
                                                                    <th>Diskon</th>
                                                                    <th>Total Diskon</th>
                                                                    <th>Total Harga</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="table-detail-body-{{ $i }}">
                                                                @forelse ($permintaan->getDetail as $key => $barang)
                                                                    @php

                                                                        $vendorDetail = isset(
                                                                            $permintaan->getPengajuanPembelian
                                                                                ->getVendor[$i]->getVendorDetail[$key],
                                                                        )
                                                                            ? $permintaan->getPengajuanPembelian
                                                                                ->getVendor[$i]->getVendorDetail[$key]
                                                                            : null;
                                                                    @endphp
                                                                    <tr>
                                                                        <td width="15%">
                                                                            <select
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][barang_id]"
                                                                                class="form-control select2 barang-name-in-table"
                                                                                data-placeholder="Pilih Barang"
                                                                                style="width: 100%;">
                                                                                <option value="">Pilih Barang
                                                                                </option>
                                                                                @foreach ($masterbarang as $b)
                                                                                    <option value="{{ $b->id }}"
                                                                                        @if ($barang->NamaBarang == $b->id) selected @endif>
                                                                                        {{ $b->Nama }} -
                                                                                        {{ $b->getMerk->Nama ?? '' }} -
                                                                                        {{ $b->Tipe ?? '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        {{-- <td>
                                                                            <input type="text"
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][merek]"
                                                                                class="form-control merek-vendor-input-{{ $i }}"
                                                                                placeholder="Merek"
                                                                                value=" {{ $barang->getBarang->getMerk->Nama ?? null }}"
                                                                                readonly>
                                                                        </td> --}}
                                                                        <td width="15%">
                                                                            <select
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][vendor_id]"
                                                                                class="form-control select2 vendor-name-in-table"
                                                                                data-placeholder="Pilih Vendor"
                                                                                style="width: 100%;">
                                                                                <option value="">Pilih Vendor
                                                                                </option>
                                                                                @foreach ($vendor as $v)
                                                                                    <option value="{{ $v->id }}">
                                                                                        {{ $v->Nama }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][jumlah]"
                                                                                class="form-control jumlah-vendor-input-{{ $i }}"
                                                                                placeholder="Jumlah"
                                                                                oninput="hitungOtomatis{{ $i }}(this)"
                                                                                value="{{ $barang->Jumlah }}" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input type="text"
                                                                                    name="vendors[{{ $i }}][details][{{ $key }}][harga_satuan]"
                                                                                    class="form-control harga-satuan-input-{{ $i }} currency-input-{{ $i }}"
                                                                                    placeholder="Harga Satuan"
                                                                                    oninput="hitungOtomatis{{ $i }}(this); formatRupiahInput(this);"
                                                                                    onkeyup="formatRupiahInput(this);"
                                                                                    value="{{ $vendorDetail ? number_format((int) str_replace(['.', ','], '', $vendorDetail->HargaSatuan), 0, ',', '.') : '' }}">

                                                                                <span
                                                                                    style="width:12px;display:inline-block"></span>

                                                                                <button type="button"
                                                                                    class="btn btn-outline-secondary"
                                                                                    onclick="
                                                                                        if(this.dataset.clicked === '1') {
                                                                                            this.disabled = true;
                                                                                            return;
                                                                                        }
                                                                                        this.disabled = true;
                                                                                        this.dataset.clicked = '1';
                                                                                        let btn = this;
                                                                                        let inputHarga = btn.parentNode.querySelector('input[name=\'vendors[{{ $i }}][details][{{ $key }}][harga_satuan]\']');
                                                                                        let rawValue = inputHarga.value.replace(/[^\d]/g, '');
                                                                                        if(rawValue){
                                                                                            let hargaNonPPN = Math.round(parseInt(rawValue) / 1.11);
                                                                                            inputHarga.value = hargaNonPPN.toLocaleString('id-ID');
                                                                                            hitungOtomatis{{ $i }}(inputHarga);
                                                                                            formatRupiahInput(inputHarga);
                                                                                        }
                                                                                    "
                                                                                    data-clicked="0"
                                                                                    title="Hitung Harga Sebelum PPN (÷1,11)">
                                                                                    /PPN
                                                                                </button>

                                                                                <span
                                                                                    style="width:1px;display:inline-block"></span>

                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger ms-2"
                                                                                    onclick="
                                                                                        let inputHarga = this.parentNode.querySelector('input[name=\'vendors[{{ $i }}][details][{{ $key }}][harga_satuan]\']');
                                                                                        inputHarga.value = '';
                                                                                        hitungOtomatis{{ $i }}(inputHarga);
                                                                                        formatRupiahInput(inputHarga);
                                                                                        // Reset /PPN button for current row
                                                                                        let btnPPN = this.parentNode.querySelector('button[title=\'Hitung Harga Sebelum PPN (÷1,11)\']');
                                                                                        if (btnPPN) {
                                                                                            btnPPN.disabled = false;
                                                                                            btnPPN.dataset.clicked = '0';
                                                                                        }
                                                                                    "
                                                                                    title="Batalkan Nilai Harga Satuan">
                                                                                    <i class="fa fa-times"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][jenis_diskon_item]"
                                                                                class="form-control jenis-diskon-item-select-{{ $i }}"
                                                                                onchange="hitungOtomatis{{ $i }}(this); formatDiskonItemInput(this, {{ $i }}, {{ $key }});">
                                                                                <option value="Rp"
                                                                                    {{ $vendorDetail && $vendorDetail->JenisDiskon == 'Rp' ? 'selected' : '' }}>
                                                                                    Rp</option>
                                                                                <option value="Persen"
                                                                                    {{ $vendorDetail && $vendorDetail->JenisDiskon == 'Persen' ? 'selected' : '' }}>
                                                                                    Persen</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][diskon_item]"
                                                                                class="form-control diskon-item-input-{{ $i }} currency-input-{{ $i }}"
                                                                                placeholder="Diskon"
                                                                                oninput="
                                                                                    hitungOtomatis{{ $i }}(this);
                                                                                    var jenisDiskon = document.getElementsByName('vendors[{{ $i }}][details][{{ $key }}][jenis_diskon_item]')[0]?.value;
                                                                                    if(jenisDiskon === 'Rp'){
                                                                                        formatRupiahInput(this);
                                                                                        this.removeAttribute('max');
                                                                                        this.type = 'text';
                                                                                        this.inputMode = 'decimal';
                                                                                        this.pattern = '[0-9.,]*';
                                                                                    } else if(jenisDiskon === 'Persen'){
                                                                                        this.type='text';
                                                                                        this.inputMode = 'decimal';
                                                                                        this.pattern = '[0-9.,]*';
                                                                                        this.setAttribute('max', '100');
                                                                                        if(parseFloat(this.value.replace(',','.')) > 100) this.value = '100';
                                                                                    }
                                                                                "
                                                                                onkeyup="
                                                                                    var jenisDiskon = document.getElementsByName('vendors[{{ $i }}][details][{{ $key }}][jenis_diskon_item]')[0]?.value;
                                                                                    if(jenisDiskon === 'Rp'){
                                                                                        formatRupiahInput(this);
                                                                                        this.removeAttribute('max');
                                                                                        this.type = 'text';
                                                                                        this.inputMode = 'decimal';
                                                                                        this.pattern = '[0-9.,]*';
                                                                                    } else if(jenisDiskon === 'Persen'){
                                                                                        this.type='text';
                                                                                        this.inputMode = 'decimal';
                                                                                        this.pattern = '[0-9.,]*';
                                                                                        this.setAttribute('max', '100');
                                                                                        if(parseFloat(this.value.replace(',','.')) > 100) this.value = '100';
                                                                                    }
                                                                                "
                                                                                value="{{ $vendorDetail ? ($vendorDetail->JenisDiskon == 'Rp' ? (is_numeric($vendorDetail->Diskon) ? ($vendorDetail->Diskon == 0 ? '0' : number_format((float) str_replace(['.', ','], ['', '.'], $vendorDetail->Diskon), 2, ',', '.')) : $vendorDetail->Diskon) : str_replace('.', ',', $vendorDetail->Diskon)) : '0' }}">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][total_diskon]"
                                                                                class="form-control total-diskon-input-{{ $i }} currency-input-{{ $i }}"
                                                                                placeholder="Total Diskon" readonly
                                                                                onkeyup="formatRupiahInput(this);"
                                                                                value="{{ $vendorDetail ? number_format((int) str_replace(['.', ','], '', $vendorDetail->TotalDiskon), 0, ',', '.') : '' }}">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="vendors[{{ $i }}][details][{{ $key }}][total_harga]"
                                                                                class="form-control total-harga-input-{{ $i }} currency-input-{{ $i }}"
                                                                                placeholder="Total Harga" readonly
                                                                                onkeyup="formatRupiahInput{{ $i }}(this);"
                                                                                value="{{ $vendorDetail ? number_format((int) str_replace(['.', ','], '', $vendorDetail->TotalHarga), 0, ',', '.') : '' }}">
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="8" class="text-center">Tidak ada
                                                                            barang pada
                                                                            permintaan ini.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <tfoot>
                                                                @php
                                                                    $vendorMain = isset(
                                                                        $permintaan->getPengajuanPembelian->getVendor[
                                                                            $i
                                                                        ],
                                                                    )
                                                                        ? $permintaan->getPengajuanPembelian->getVendor[
                                                                            $i
                                                                        ]
                                                                        : null;
                                                                @endphp
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Total
                                                                            Harga Sebelum
                                                                            Diskon</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_harga_sebelum_diskon]"
                                                                            id="total-harga-sebelum-diskon-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Harga Sebelum Diskon"
                                                                            readonly
                                                                            value="{{ $vendorMain ? 'Rp ' . number_format((int) str_replace(['.', ','], '', $vendorMain->HargaTanpaDiskon), 0, ',', '.') : '' }}">
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Total
                                                                            Diskon</strong>
                                                                    </td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_diskon]"
                                                                            id="total-diskon-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Semua Diskon" readonly
                                                                            value="{{ $vendorMain ? 'Rp ' . number_format((int) str_replace(['.', ','], '', $vendorMain->TotalDiskon), 0, ',', '.') : '' }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Harga
                                                                            Setelah
                                                                            Diskon</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_harga_setelah_diskon]"
                                                                            id="total-harga-setelah-diskon-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Harga Setelah Diskon"
                                                                            readonly
                                                                            value="{{ $vendorMain ? 'Rp ' . number_format((int) str_replace(['.', ','], '', $vendorMain->HargaDenganDiskon), 0, ',', '.') : '' }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end">
                                                                        <strong>PPN (%)</strong>
                                                                    </td>
                                                                    <td colspan="1">
                                                                        <input type="number"
                                                                            name="vendors[{{ $i }}][ppn_persen]"
                                                                            id="ppn-persen-{{ $i }}"
                                                                            class="form-control" placeholder="PPN (%)"
                                                                            oninput="hitungOtomatisGlobal{{ $i }}()"
                                                                            value="{{ $vendorMain ? $vendorMain->Ppn : '' }}">
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Total PPN
                                                                            (All)</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_ppn]"
                                                                            id="total-ppn-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Semua PPN" readonly
                                                                            value="{{ $vendorMain ? 'Rp ' . number_format((int) str_replace(['.', ','], '', $vendorMain->TotalPpn), 0, ',', '.') : '' }}">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Total
                                                                            Harga</strong>
                                                                    </td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][grand_total]"
                                                                            id="grand-total-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Harga" readonly
                                                                            value="{{ $vendorMain ? 'Rp ' . number_format((int) str_replace(['.', ','], '', $vendorMain->TotalHarga), 0, ',', '.') : '' }}">
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                        <script>
                                                            // --- FORMATTING & AUTOCALC vendor tab [{{ $i }}] ---
                                                            function formatRupiah{{ $i }}(angka, prefix = "Rp ") {
                                                                if (angka === null || angka === undefined) return '';
                                                                let number_string = angka.toString().replace(/[^,\d]/g, ''),
                                                                    split = number_string.split(','),
                                                                    sisa = split[0].length % 3,
                                                                    rupiah = split[0].substr(0, sisa),
                                                                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                                                                if (ribuan) {
                                                                    let separator = sisa ? '.' : '';
                                                                    rupiah += separator + ribuan.join('.');
                                                                }

                                                                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                                                                return prefix + rupiah;
                                                            }

                                                            function formatRupiahInput{{ $i }}(input) {
                                                                // Format to rupiah no matter initial value
                                                                let nilaiOrigin = input.value;
                                                                let nilai = nilaiOrigin.replace(/[^,\d]/g, '');
                                                                if (!nilai) {
                                                                    input.value = '';
                                                                    return;
                                                                }
                                                                let formatted = formatRupiah{{ $i }}(nilai, "Rp ");
                                                                input.value = formatted;
                                                            }

                                                            function parseNum{{ $i }}(val) {
                                                                if (typeof val === 'string') {
                                                                    val = val.replace(/[^0-9.,\-]/g, "");
                                                                    val = val.replace(/\./g, "");
                                                                    val = val.replace(',', '.');
                                                                }
                                                                let x = parseFloat(val);
                                                                return isNaN(x) ? 0 : x;
                                                            }

                                                            function hitungOtomatisGlobal{{ $i }}() {
                                                                let $first = $(
                                                                    "#table-detail-body-{{ $i }} input, #table-detail-body-{{ $i }} select"
                                                                ).first();
                                                                if ($first.length) hitungOtomatis{{ $i }}($first[0]);
                                                            }

                                                            function hitungOtomatis{{ $i }}(elem) {
                                                                let $row = $(elem).closest('tr');
                                                                let jumlah = parseNum{{ $i }}($row.find('.jumlah-vendor-input-{{ $i }}').val());
                                                                let hargaSatuan = parseNum{{ $i }}($row.find('.harga-satuan-input-{{ $i }}').val());

                                                                let diskonInput = $row.find('.diskon-item-input-{{ $i }}');
                                                                let diskonValueRaw = diskonInput.val();
                                                                let diskon = parseNum{{ $i }}(diskonValueRaw);
                                                                let jenisDiskon = $row.find('.jenis-diskon-item-select-{{ $i }}').val();

                                                                if (jenisDiskon === 'Rp') {
                                                                    diskonInput.addClass('currency-input-{{ $i }}');
                                                                    formatRupiahInput{{ $i }}(diskonInput[0]);
                                                                    diskonInput.attr('placeholder', 'Diskon (Rp)');
                                                                } else {
                                                                    diskonInput.removeClass('currency-input-{{ $i }}');
                                                                    // force allow decimal for %, allow koma/titik, and cap at 100
                                                                    let cleaned = parseNum{{ $i }}(diskonValueRaw);
                                                                    if (cleaned > 100) cleaned = 100;

                                                                    // Show as decimal with comma if entered with "."
                                                                    let rawVal = diskonValueRaw.replace('.', ',');
                                                                    // If empty, assign
                                                                    if (rawVal === '') {
                                                                        diskonInput.val('');
                                                                    } else {
                                                                        // Only allow one comma
                                                                        var tokens = rawVal.split(',');
                                                                        if (tokens.length > 2) {
                                                                            rawVal = tokens[0] + ',' + tokens[1];
                                                                        }
                                                                        if (cleaned == 100) rawVal = '100';
                                                                        diskonInput.val(rawVal);
                                                                    }
                                                                    diskonInput.attr('placeholder', 'Diskon (%)');
                                                                }

                                                                let subTotal = jumlah * hargaSatuan;

                                                                let totalDiskon = 0;
                                                                if (jenisDiskon === 'Rp') {
                                                                    totalDiskon = diskon;
                                                                } else if (jenisDiskon === 'Persen') {
                                                                    totalDiskon = subTotal * (diskon / 100);
                                                                }
                                                                if (totalDiskon > subTotal) totalDiskon = subTotal;

                                                                let totalSetelahDiskon = subTotal - totalDiskon;

                                                                let formattedDiskon = formatRupiah{{ $i }}(totalDiskon.toFixed(0));
                                                                let formattedTotal = formatRupiah{{ $i }}(totalSetelahDiskon.toFixed(0));

                                                                $row.find('.total-diskon-input-{{ $i }}').val(formattedDiskon);
                                                                $row.find('.total-harga-input-{{ $i }}').val(formattedTotal);

                                                                // Akumulasi total
                                                                let sumSubTotal = 0,
                                                                    sumDiskon = 0,
                                                                    sumTotalHarga = 0;
                                                                $("#table-detail-body-{{ $i }} tr").each(function() {
                                                                    let tr = $(this);
                                                                    let jml = parseNum{{ $i }}(tr.find('.jumlah-vendor-input-{{ $i }}')
                                                                        .val());
                                                                    let satuan = parseNum{{ $i }}(tr.find('.harga-satuan-input-{{ $i }}')
                                                                        .val());
                                                                    sumSubTotal += jml * satuan;
                                                                    sumDiskon += parseNum{{ $i }}(tr.find('.total-diskon-input-{{ $i }}')
                                                                        .val());
                                                                    sumTotalHarga += parseNum{{ $i }}(tr.find('.total-harga-input-{{ $i }}')
                                                                        .val());
                                                                });

                                                                let totalHargaSebelumDiskonAll = sumSubTotal;
                                                                let hargaSetelahDiskonAll = sumTotalHarga;
                                                                let totalDiskonAll = sumDiskon;
                                                                let grandTotalSebelumPPN = sumTotalHarga;
                                                                let ppnGlobal = parseNum{{ $i }}($('#ppn-persen-{{ $i }}').val());
                                                                let totalPpnAll = grandTotalSebelumPPN * (ppnGlobal / 100);
                                                                let grandTotal = grandTotalSebelumPPN + totalPpnAll;

                                                                $('#total-harga-sebelum-diskon-{{ $i }}').val(formatRupiah{{ $i }}(
                                                                    totalHargaSebelumDiskonAll.toFixed(0)));
                                                                $('#total-harga-setelah-diskon-{{ $i }}').val(formatRupiah{{ $i }}(
                                                                    hargaSetelahDiskonAll.toFixed(0)));
                                                                $('#total-diskon-{{ $i }}').val(formatRupiah{{ $i }}(totalDiskonAll.toFixed(0)));
                                                                $('#total-ppn-{{ $i }}').val(formatRupiah{{ $i }}(totalPpnAll.toFixed(0)));
                                                                $('#grand-total-{{ $i }}').val(formatRupiah{{ $i }}(grandTotal.toFixed(0)));
                                                            }

                                                            // Init formatting & hitung otomatis di document ready agar saat datanya dari DB, langsung keformat & terhitung:
                                                            $(document).ready(function() {
                                                                // Format currency input saat page load
                                                                $('.currency-input-{{ $i }}').each(function() {
                                                                    formatRupiahInput{{ $i }}(this);
                                                                });

                                                                // Untuk setiap baris, trigger hitung setelah format
                                                                $('#table-detail-body-{{ $i }} tr').each(function(idx, tr) {
                                                                    // pastikan kalau ada input harga_satuan nya, lakukan format dan trigger hitung
                                                                    $(tr).find('.harga-satuan-input-{{ $i }}').each(function() {
                                                                        formatRupiahInput{{ $i }}(this);
                                                                    });
                                                                });

                                                                // Panggil hitung otomatis untuk semua baris jika ada data
                                                                // (semua baris diproses satu-satu, supaya datanya konsisten)
                                                                setTimeout(function() {
                                                                    $('#table-detail-body-{{ $i }} tr').each(function(idx, tr) {
                                                                        // Trigger hitung otomatis untuk setiap input field utama (harga_satuan/input diskon)
                                                                        var hargaInput = $(tr).find('.harga-satuan-input-{{ $i }}');
                                                                        if (hargaInput.length > 0 && hargaInput.val() != "") {
                                                                            hitungOtomatis{{ $i }}(hargaInput[0]);
                                                                        }
                                                                    });

                                                                    // Sync value awal Nama Vendor pada tabel dengan dropdown utama & lock
                                                                    var mainVendorSelect = $('#vendor_list_{{ $i }}_id');
                                                                    var vendorValue = mainVendorSelect.val();
                                                                    $('#table-detail-body-{{ $i }} select.vendor-name-in-table').each(function() {
                                                                        $(this).val(vendorValue).prop('disabled', true).trigger('change');
                                                                    });
                                                                }, 10);

                                                                $('#table-detail-vendor-{{ $i }}').on('input change blur',
                                                                    '.jumlah-vendor-input-{{ $i }}, .harga-satuan-input-{{ $i }}, .diskon-item-input-{{ $i }}, .jenis-diskon-item-select-{{ $i }}',
                                                                    function() {
                                                                        hitungOtomatis{{ $i }}(this);
                                                                        if ($(this).hasClass('currency-input-{{ $i }}') &&
                                                                            $(this).is('.diskon-item-input-{{ $i }}')
                                                                        ) {
                                                                            var $row = $(this).closest('tr');
                                                                            var jenisDiskon = $row.find('.jenis-diskon-item-select-{{ $i }}').val();
                                                                            if (jenisDiskon === 'Rp') {
                                                                                formatRupiahInput{{ $i }}(this);
                                                                            }
                                                                        } else if ($(this).hasClass('currency-input-{{ $i }}')) {
                                                                            formatRupiahInput{{ $i }}(this);
                                                                        }
                                                                    });

                                                                $('#table-detail-vendor-{{ $i }}').on('keyup blur focus',
                                                                    '.currency-input-{{ $i }}',
                                                                    function() {
                                                                        var $row = $(this).closest('tr');
                                                                        if ($(this).is('.diskon-item-input-{{ $i }}')) {
                                                                            var jenisDiskon = $row.find('.jenis-diskon-item-select-{{ $i }}').val();
                                                                            if (jenisDiskon === 'Rp') {
                                                                                formatRupiahInput{{ $i }}(this);
                                                                            }
                                                                        } else {
                                                                            formatRupiahInput{{ $i }}(this);
                                                                        }
                                                                    });

                                                                $('#table-detail-vendor-{{ $i }}').on('change',
                                                                    '.jenis-diskon-item-select-{{ $i }}',
                                                                    function() {
                                                                        var $row = $(this).closest('tr');
                                                                        hitungOtomatis{{ $i }}($(this));
                                                                    });

                                                                $('#ppn-persen-{{ $i }}').on('input change', function() {
                                                                    hitungOtomatisGlobal{{ $i }}();
                                                                });

                                                                // Tambahkan event handler ini agar 0 pada diskon tipe Rp tetap dianggap valid
                                                                $('#table-detail-vendor-{{ $i }}').on('blur', '.diskon-item-input-{{ $i }}',
                                                                    function() {
                                                                        var $this = $(this);
                                                                        var jenisDiskon = $this.closest('tr').find(
                                                                            '.jenis-diskon-item-select-{{ $i }}').val();
                                                                        var val = $this.val();
                                                                        if (jenisDiskon === 'Rp' && (val === '' || val === null)) {
                                                                            $this.val('0');
                                                                        }
                                                                    });
                                                            });
                                                        </script>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('ajukan.index') }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            {{-- <button type="submit" class="btn btn-primary me-2" name="submit_type" value="simpan">
                                <i class="fa fa-save"></i> Simpan
                            </button> --}}
                            <button type="button" class="btn btn-primary me-2" id="btn-simpan">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.getElementById('btn-simpan').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menyimpan data ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Sedang Memproses',
                        html: 'Mohon tunggu, data sedang diupload dan disimpan...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    document.getElementById('form-pengajuan').submit();
                }
            });
        });
    </script>
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ Session::get('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif
    <script>
        // Global rupiah formatting for other fields (outside vendor tabs)
        function formatRupiahInput(input) {
            // support decimal
            let angka = input.value.replace(/[^,\d]/g, '').toString();
            let split = angka.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah;
        }
        $(document).ready(function() {
            let $rkap = $('#nominal_rkap');
            if ($rkap.length) {
                formatRupiahInput($rkap[0]);
                $rkap.on('keyup blur', function() {
                    formatRupiahInput(this);
                });
            }
            // Pemanggilan format untuk field lain yang perlu langsung diformat, misal dengan class tertentu
            $('.currency-input-global').each(function() {
                formatRupiahInput(this);
            });

            // Pastikan pada saat pertama kali load semua input dengan class currency-input-global sudah diformat
            setTimeout(function() {
                $('.currency-input-global').each(function() {
                    formatRupiahInput(this);
                });
            }, 0);
        });
    </script>
    <script>
        // Fungsi untuk update info vendor & sinkronisasi dropdown vendor di tabel, untuk 3 tab vendor
        function updateVendorInfoAndLock(vn) {
            var select = document.getElementById('vendor_list_' + vn + '_id');
            if (!select) return;
            var selectedOption = select.options[select.selectedIndex];
            var namaPIC = selectedOption ? (selectedOption.getAttribute('data-namapic') || '-') : '-';
            var noHpPIC = selectedOption ? (selectedOption.getAttribute('data-nohppic') || '-') : '-';

            var infoDiv = document.getElementById('vendor_info_' + vn);
            if (infoDiv) {
                var namaPICSpan = infoDiv.querySelector('.vendor_namapic');
                var noHpPICSpan = infoDiv.querySelector('.vendor_nohppic');
                if (namaPICSpan) namaPICSpan.innerText = namaPIC;
                if (noHpPICSpan) noHpPICSpan.innerText = noHpPIC;
            }

            // Set semua select vendor pada tabel
            var tableSelector = '#table-detail-body-' + vn + ' select.vendor-name-in-table';
            $(tableSelector).each(function() {
                $(this).val(select.value).prop('disabled', true).trigger('change');
            });
        }

        $(document).ready(function() {
            for (let i = 0; i < 3; i++) {
                updateVendorInfoAndLock(i);
            }
        });
    </script>
@endpush
