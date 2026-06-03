@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Edit Pengajuan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ajukan.index') }}">Pengajuan Pembelian</a></li>
                    <li class="breadcrumb-item active">Edit Pengajuan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('ajukan.update', $data->id) }}" method="POST" enctype="multipart/form-data"
                autocomplete="off">
                @csrf
                @method('PUT')
                <div class="card mb-4">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0">Formulir Edit Pengajuan Pembelian</h4>
                        <p class="card-text mb-0">
                            Silakan ubah data pengajuan pembelian di bawah ini.
                        </p>
                    </div>
                    <div class="card-body">
                        {{-- Table pengajuan pembelian --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="tanggal" class="form-label"><strong>Tanggal</strong></label>
                                <input type="date" name="pengajuan[tanggal]" id="tanggal"
                                    class="form-control @error('pengajuan.tanggal') is-invalid @enderror"
                                    value="{{ old('pengajuan.tanggal', $data->Tanggal ? $data->Tanggal : date('Y-m-d')) }}"
                                    placeholder="Pilih tanggal">
                                @error('pengajuan.tanggal')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="pengajuan[id_permintaan]" value="{{ $data->IdPermintaan }}">
                            </div>
                            <div class="col-md-4">
                                <label for="departemen" class="form-label"><strong>Permintaan Dari
                                        Departemen</strong></label>
                                <select name="pengajuan[departemen]" id="departemen"
                                    class="form-select select2 @error('pengajuan.departemen') is-invalid @enderror" readonly
                                    style="pointer-events: none; background-color: #e9ecef;">
                                    @if (isset($data->getDepartemen))
                                        <option value="{{ $data->getDepartemen->Kode ?? $data->DepartemenId }}" selected>
                                            {{ $data->getDepartemen->Nama ?? '-' }}
                                        </option>
                                    @else
                                        <option value="">Pilih Departemen</option>
                                        @foreach ($departemen as $dept)
                                            <option value="{{ $dept->Kode }}"
                                                {{ old('pengajuan.departemen', $data->DepartemenId) == $dept->Kode ? 'selected' : '' }}>
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
                                    class="form-select select2 @error('pengajuan.jenis') is-invalid @enderror" readonly
                                    style="pointer-events: none; background-color: #e9ecef;">
                                    <option value="">Pilih Jenis Pengajuan</option>
                                    @foreach ($JenisPengajuan as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            {{ old('pengajuan.jenis', $data->Jenis) == $jenis->id ? 'selected' : '' }}>
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
                                        {{ old('pengajuan.tujuan', $data->Tujuan ?? '') == 'Pembelian Baru' ? 'selected' : '' }}>
                                        Pembelian Baru
                                    </option>
                                    <option value="Penggantian"
                                        {{ old('pengajuan.tujuan', $data->Tujuan ?? '') == 'Penggantian' ? 'selected' : '' }}>
                                        Penggantian
                                    </option>
                                    <option value="Perbaikan"
                                        {{ old('pengajuan.tujuan', $data->Tujuan ?? '') == 'Perbaikan' ? 'selected' : '' }}>
                                        Perbaikan</option>
                                    <option value="Adendum"
                                        {{ old('pengajuan.tujuan', $data->Tujuan ?? '') == 'Adendum' ? 'selected' : '' }}>
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
                                    value="{{ old('pengajuan.perkiraan_utilitasi_bulanan', $data->PerkiraanUtilitasiBulanan) }}"
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
                                        isset($data->PerkiraanBepPadaTahun) ? $data->PerkiraanBepPadaTahun : '',
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
                                    value="{{ old('pengajuan.rkap', $data->Rkap) }}" placeholder="Masukkan RKAP">
                                <small class="text-danger">Ketik "-" jika barang medis</small>
                                @error('pengajuan.rkap')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col-md-2">
                                <label for="nominal_rkap" class="form-label"><strong>Nominal RKAP</strong></label>
                                <input type="text" name="pengajuan[nominal_rkap]" id="nominal_rkap"
                                    class="form-control @error('pengajuan.nominal_rkap') is-invalid @enderror"
                                    value="{{ old('pengajuan.nominal_rkap', $data->NominalRkap ?? 0) }}"
                                    placeholder="Contoh: Rp1.000.000" oninput="formatRupiahInput(this)">
                                @error('pengajuan.nominal_rkap')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Table list vendor 3 TAB --}}
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
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{ $i == 0 ? 'active' : '' }}"
                                                        id="vendor-tab-{{ $i }}" data-bs-toggle="tab"
                                                        data-bs-target="#vendor-panel-{{ $i }}" type="button"
                                                        role="tab" aria-controls="vendor-panel-{{ $i }}"
                                                        aria-selected="{{ $i == 0 ? 'true' : 'false' }}">
                                                        Vendor {{ $i + 1 }}
                                                    </button>
                                                </li>
                                            @endfor
                                        </ul>
                                        <div class="tab-content" id="vendorTabContent">
                                            @php
                                                // Collect list barang dan merek dari vendor 1
                                                $vendor1Details =
                                                    isset($data->getVendor[0]) && $data->getVendor[0]->getVendorDetail
                                                        ? $data->getVendor[0]->getVendorDetail
                                                        : collect();
                                                $vendor1BarangList = [];
                                                $vendor1MerekList = [];
                                                foreach ($vendor1Details as $vendor1Barang) {
                                                    $vendor1BarangList[] = $vendor1Barang->NamaBarang;
                                                    $vendor1MerekList[] =
                                                        optional(optional($vendor1Barang->getNamaBarang)->getMerk)
                                                            ->Nama ?? '';
                                                }
                                            @endphp
                                            @for ($i = 0; $i < 3; $i++)
                                                @php
                                                    $vendorTab = $data->getVendor[$i] ?? null;
                                                    $vendorDetailTab = $vendorTab
                                                        ? $vendorTab->getVendorDetail
                                                        : collect();
                                                @endphp
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
                                                                                            {{ ($vendorTab && $vendorTab->NamaVendor == $v->id) || old("vendors.$i.vendor_id") == $v->id ? 'selected' : '' }}>
                                                                                            {{ $v->Nama }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div id="vendor_info_{{ $i }}">
                                                                                <h6 class="card-title fw-semibold mb-2">
                                                                                    Informasi Vendor Berdasarkan Pilihan
                                                                                </h6>
                                                                                <table class="table table-bordered mb-0">
                                                                                    <tr>
                                                                                        <th>Nama PIC</th>
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                class="form-control vendor_namapic"
                                                                                                name="vendors[{{ $i }}][nama_pic]"
                                                                                                placeholder="Masukan Nama PIC"
                                                                                                value="{{ old("vendors.$i.nama_pic", isset($data->getVendor[$i]) && $data->getVendor[$i]->NamaVendor ? $data->getVendor[$i]->NamaPic ?? '' : '') }}">
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>No HP PIC</th>
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                class="form-control vendor_nohppic"
                                                                                                name="vendors[{{ $i }}][no_hp_pic]"
                                                                                                placeholder="Masukan Kontak PIC"
                                                                                                value="{{ old("vendors.$i.no_hp_pic", isset($data->getVendor[$i]) && $data->getVendor[$i]->KontakPic ? $data->getVendor[$i]->KontakPic ?? '' : '') }}">
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
                                                                        Vendor</strong></label>
                                                                <input class="form-control" type="file"
                                                                    id="vendor_list_{{ $i }}_penawaran_file"
                                                                    name="vendors[{{ $i }}][penawaran_file]"
                                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                                    style="border: 2px dashed #ced4da; padding: 20px;"
                                                                    ondragover="this.classList.add('dragover')"
                                                                    ondragleave="this.classList.remove('dragover')"
                                                                    onchange="this.classList.remove('dragover')">
                                                                @if ($vendorTab && $vendorTab->SuratPenawaranVendor)
                                                                    <div class="mt-2">
                                                                        File saat ini:<br>
                                                                        @foreach (is_array(json_decode($vendorTab->SuratPenawaranVendor, true)) ? json_decode($vendorTab->SuratPenawaranVendor, true) : (is_string($vendorTab->SuratPenawaranVendor) ? explode(',', $vendorTab->SuratPenawaranVendor) : []) as $file)
                                                                            @if ($file)
                                                                                <a href="{{ asset('storage/penawaran_vendor/' . ltrim($file, '/')) }}"
                                                                                    target="_blank">{{ basename($file) }}</a><br>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                                <small class="form-text text-muted">Anda bisa drag and drop
                                                                    file di area ini atau klik untuk memilih file.</small>
                                                                @error('vendors.' . $i . '.penawaran_file')
                                                                    <div class="text-danger mt-1">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <!-- Table list vendor detail per vendor tab -->
                                                        <table class="table align-middle"
                                                            id="table-detail-vendor-{{ $i }}">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Barang</th>
                                                                    <th style="width:auto">Nama Vendor</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Harga Satuan</th>
                                                                    <th>Jenis Diskon</th>
                                                                    <th>Diskon</th>
                                                                    <th>Total Diskon</th>
                                                                    <th>Total Harga</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="table-detail-body-{{ $i }}">
                                                                @php
                                                                    $refBarangList = $vendor1BarangList;
                                                                    $refMerekList = $vendor1MerekList;
                                                                @endphp

                                                                @if ($i == 0)
                                                                    {{-- Vendor 1: Tetap default --}}
                                                                    @forelse ($vendorDetailTab as $key => $barang)
                                                                        <tr>
                                                                            <td width="15%">
                                                                                <select
                                                                                    name="vendors[0][details][{{ $key }}][barang_id]"
                                                                                    class="form-control select2 barang-name-in-table"
                                                                                    data-placeholder="Pilih Barang"
                                                                                    style="width: 100%;">
                                                                                    <option value="">Pilih Barang
                                                                                    </option>
                                                                                    @foreach ($masterbarang as $b)
                                                                                        <option
                                                                                            value="{{ $b->id }}"
                                                                                            @if (old('vendors.0.details.' . $key . '.barang_id', $barang->NamaBarang ?? null) == $b->id) selected @endif>
                                                                                            {{ $b->Nama }}{{ isset($b->Tipe) && $b->Tipe ? ' / ' . $b->Tipe : '' }}{{ isset($b->getMerk->Nama) && $b->getMerk->Nama ? ' / ' . $b->getMerk->Nama : '' }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </td>



                                                                            <td width="15%">
                                                                                <select
                                                                                    name="vendors[{{ $i }}][details][{{ $key }}][vendor_id]"
                                                                                    class="form-control vendor-name-in-table"
                                                                                    data-placeholder="Pilih Vendor"
                                                                                    style="width: 100%; background-color: #e9ecef; pointer-events: none; cursor: not-allowed;">
                                                                                    <option value="">Pilih Vendor
                                                                                    </option>
                                                                                    @foreach ($vendor as $v)
                                                                                        <option
                                                                                            value="{{ $v->id }}">
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
                                                                                    value="{{ old("vendors.$i.details.$key.jumlah", $barang->Jumlah ?? '') }}">
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $key }}][harga_satuan]"
                                                                                        class="form-control harga-satuan-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Harga Satuan"
                                                                                        oninput="hitungOtomatis{{ $i }}(this); formatRupiahInput{{ $i }}(this);"
                                                                                        onkeyup="formatRupiahInput{{ $i }}(this);"
                                                                                        value="{{ old("vendors.$i.details.$key.harga_satuan", isset($barang->HargaSatuan) ? number_format($barang->HargaSatuan, 0, ',', '.') : '') }}">

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
                                                                                                formatRupiahInput{{ $i }}(inputHarga);
                                                                                            }
                                                                                        "
                                                                                        data-clicked="0" type="button"
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
                                                                                            formatRupiahInput{{ $i }}(inputHarga);
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
                                                                                    name="vendors[0][details][{{ $key }}][jenis_diskon_item]"
                                                                                    class="form-control jenis-diskon-item-select-0"
                                                                                    onchange="hitungOtomatis0(this)">
                                                                                    <option value="Rp"
                                                                                        {{ (isset($barang) && $barang->JenisDiskon == 'Rp') || old('vendors.0.details.' . $key . '.jenis_diskon_item', $barang->JenisDiskon ?? '') == 'Rp' ? 'selected' : '' }}>
                                                                                        Rp
                                                                                    </option>
                                                                                    <option value="Persen"
                                                                                        {{ (isset($barang) && $barang->JenisDiskon == 'Persen') || old('vendors.0.details.' . $key . '.jenis_diskon_item', $barang->JenisDiskon ?? '') == 'Persen' ? 'selected' : '' }}>
                                                                                        Persen
                                                                                    </option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    name="vendors[0][details][{{ $key }}][diskon_item]"
                                                                                    class="form-control diskon-item-input-0 currency-input-0"
                                                                                    placeholder="Diskon"
                                                                                    oninput="hitungOtomatis0(this);"
                                                                                    value="{{ old('vendors.0.details.' . $key . '.diskon_item', $barang->Diskon ?? '') }}">
                                                                            </td>

                                                                            <td>
                                                                                <input type="text"
                                                                                    name="vendors[0][details][{{ $key }}][total_diskon]"
                                                                                    class="form-control total-diskon-input-0 currency-input-0"
                                                                                    placeholder="Total Diskon" readonly
                                                                                    value="{{ old('vendors.0.details.' . $key . '.total_diskon', isset($barang->TotalDiskon) ? number_format($barang->TotalDiskon, 0, ',', '.') : '') }}"
                                                                                    onkeyup="formatRupiahInput0(this);">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    name="vendors[0][details][{{ $key }}][total_harga]"
                                                                                    class="form-control total-harga-input-0 currency-input-0"
                                                                                    placeholder="Total Harga" readonly
                                                                                    value="{{ old('vendors.0.details.' . $key . '.total_harga', isset($barang->TotalHarga) ? number_format($barang->TotalHarga, 0, ',', '.') : '') }}"
                                                                                    onkeyup="formatRupiahInput0(this);">
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        {{-- default kosong --}}
                                                                    @endforelse
                                                                @else
                                                                    {{-- Untuk vendor 2/3, default NamaBarang dan Merek mengikuti vendor 1 --}}
                                                                    @if ($vendorDetailTab->isNotEmpty())
                                                                        @foreach ($vendorDetailTab as $key => $barang)
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
                                                                                            <option
                                                                                                value="{{ $b->id }}"
                                                                                                @if (old("vendors.$i.details.$key.barang_id", $barang->NamaBarang ?? ($refBarangList[$key] ?? null)) == $b->id) selected @endif>
                                                                                                {{ $b->Nama }} /
                                                                                                {{ $b->getMerk->Nama ?? '' }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>
                                                                                <td width="15%">
                                                                                    <select
                                                                                        name="vendors[{{ $i }}][details][{{ $key }}][vendor_id]"
                                                                                        class="form-control vendor-name-in-table"
                                                                                        data-placeholder="Pilih Vendor"
                                                                                        style="width: 100%; background-color: #e9ecef; pointer-events: none; cursor: not-allowed;">
                                                                                        <option value="">Pilih Vendor
                                                                                        </option>
                                                                                        @foreach ($vendor as $v)
                                                                                            <option
                                                                                                value="{{ $v->id }}">
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
                                                                                        value="{{ old("vendors.$i.details.$key.jumlah", $barang->Jumlah ?? '') }}">
                                                                                </td>
                                                                                <td>
                                                                                    <div class="input-group">
                                                                                        <input type="text"
                                                                                            name="vendors[{{ $i }}][details][{{ $key }}][harga_satuan]"
                                                                                            class="form-control harga-satuan-input-{{ $i }} currency-input-{{ $i }}"
                                                                                            placeholder="Harga Satuan"
                                                                                            oninput="hitungOtomatis{{ $i }}(this); formatRupiahInput{{ $i }}(this);"
                                                                                            onkeyup="formatRupiahInput{{ $i }}(this);"
                                                                                            value="{{ old("vendors.$i.details.$key.harga_satuan", isset($barang->HargaSatuan) ? number_format($barang->HargaSatuan, 0, ',', '.') : '') }}">

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
                                                                                                formatRupiahInput{{ $i }}(inputHarga);
                                                                                            }
                                                                                        "
                                                                                            data-clicked="0"
                                                                                            type="button"
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
                                                                                            formatRupiahInput{{ $i }}(inputHarga);
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
                                                                                        onchange="hitungOtomatis{{ $i }}(this)">
                                                                                        <option value="Rp"
                                                                                            {{ (isset($barang) && $barang->JenisDiskon == 'Rp') || old("vendors.$i.details.$key.jenis_diskon_item", $barang->JenisDiskon ?? '') == 'Rp' ? 'selected' : '' }}>
                                                                                            Rp
                                                                                        </option>
                                                                                        <option value="Persen"
                                                                                            {{ (isset($barang) && $barang->JenisDiskon == 'Persen') || old("vendors.$i.details.$key.jenis_diskon_item", $barang->JenisDiskon ?? '') == 'Persen' ? 'selected' : '' }}>
                                                                                            Persen
                                                                                        </option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $key }}][diskon_item]"
                                                                                        class="form-control diskon-item-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Diskon"
                                                                                        oninput="hitungOtomatis{{ $i }}(this);"
                                                                                        value="{{ old("vendors.$i.details.$key.diskon_item", $barang->Diskon ?? '') }}">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $key }}][total_diskon]"
                                                                                        class="form-control total-diskon-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Total Diskon" readonly
                                                                                        value="{{ old("vendors.$i.details.$key.total_diskon", isset($barang->TotalDiskon) ? number_format($barang->TotalDiskon, 0, ',', '.') : '') }}"
                                                                                        onkeyup="formatRupiahInput{{ $i }}(this);">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $key }}][total_harga]"
                                                                                        class="form-control total-harga-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Total Harga" readonly
                                                                                        value="{{ old("vendors.$i.details.$key.total_harga", isset($barang->TotalHarga) ? number_format($barang->TotalHarga, 0, ',', '.') : '') }}"
                                                                                        onkeyup="formatRupiahInput{{ $i }}(this);">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        @for ($row = 0; $row < count($refBarangList); $row++)
                                                                            <tr>
                                                                                <td width="15%">
                                                                                    <select
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][barang_id]"
                                                                                        class="form-control select2 barang-name-in-table"
                                                                                        data-placeholder="Pilih Barang"
                                                                                        style="width: 100%;">
                                                                                        <option value="">Pilih Barang
                                                                                        </option>
                                                                                        @foreach ($masterbarang as $b)
                                                                                            <option
                                                                                                value="{{ $b->id }}"
                                                                                                @selected($refBarangList[$row] == $b->id)>
                                                                                                {{ $b->Nama }} /
                                                                                                {{ optional($b->getMerk)->Nama }}
                                                                                                /
                                                                                                {{ $b->Tipe ?? '-' }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <select
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][vendor_id]"
                                                                                        class="form-control select2 vendor-name-in-table"
                                                                                        data-placeholder="Pilih Vendor"
                                                                                        style="width: 100%;">
                                                                                        <option value="">Pilih Vendor
                                                                                        </option>
                                                                                        @foreach ($vendor as $v)
                                                                                            <option
                                                                                                value="{{ $v->id }}">
                                                                                                {{ $v->Nama }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number"
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][jumlah]"
                                                                                        class="form-control jumlah-vendor-input-{{ $i }}"
                                                                                        placeholder="Jumlah">
                                                                                </td>
                                                                                <td>
                                                                                    <div class="input-group">
                                                                                        <input type="text"
                                                                                            name="vendors[{{ $i }}][details][{{ $row }}][harga_satuan]"
                                                                                            class="form-control harga-satuan-input-{{ $i }} currency-input-{{ $i }}"
                                                                                            placeholder="Harga Satuan"
                                                                                            oninput="hitungOtomatis{{ $i }}(this); formatRupiahInput{{ $i }}(this);"
                                                                                            onkeyup="formatRupiahInput{{ $i }}(this);">
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
                                                                                                let inputHarga = btn.parentNode.querySelector('input[name=\'vendors[{{ $i }}][details][{{ $row }}][harga_satuan]\']');
                                                                                                let rawValue = inputHarga.value.replace(/[^\d]/g, '');
                                                                                                if(rawValue){
                                                                                                    let hargaNonPPN = Math.round(parseInt(rawValue) / 1.11);
                                                                                                    inputHarga.value = hargaNonPPN.toLocaleString('id-ID');
                                                                                                    hitungOtomatis{{ $i }}(inputHarga);
                                                                                                    formatRupiahInput{{ $i }}(inputHarga);
                                                                                                }
                                                                                            "
                                                                                            data-clicked="0"
                                                                                            type="button"
                                                                                            title="Hitung Harga Sebelum PPN (÷1,11)">
                                                                                            /PPN
                                                                                        </button>
                                                                                        <span
                                                                                            style="width:1px;display:inline-block"></span>
                                                                                        <button type="button"
                                                                                            class="btn btn-outline-danger ms-2"
                                                                                            onclick="
                                                                                                let inputHarga = this.parentNode.querySelector('input[name=\'vendors[{{ $i }}][details][{{ $row }}][harga_satuan]\']');
                                                                                                inputHarga.value = '';
                                                                                                hitungOtomatis{{ $i }}(inputHarga);
                                                                                                formatRupiahInput{{ $i }}(inputHarga);
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
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][jenis_diskon_item]"
                                                                                        class="form-control jenis-diskon-item-select-{{ $i }}"
                                                                                        onchange="hitungOtomatis{{ $i }}(this)">
                                                                                        <option value="Rp">Rp</option>
                                                                                        <option value="Persen">Persen
                                                                                        </option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][diskon_item]"
                                                                                        class="form-control diskon-item-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Diskon"
                                                                                        oninput="hitungOtomatis{{ $i }}(this);">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][total_diskon]"
                                                                                        class="form-control total-diskon-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Total Diskon"
                                                                                        readonly>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="vendors[{{ $i }}][details][{{ $row }}][total_harga]"
                                                                                        class="form-control total-harga-input-{{ $i }} currency-input-{{ $i }}"
                                                                                        placeholder="Total Harga" readonly>
                                                                                </td>
                                                                            </tr>
                                                                        @endfor
                                                                    @endif
                                                                @endif
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Total
                                                                            Harga Sebelum Diskon</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_harga_sebelum_diskon]"
                                                                            id="total-harga-sebelum-diskon-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Harga Sebelum Diskon"
                                                                            readonly
                                                                            value="{{ old('vendors.' . $i . '.total_harga_sebelum_diskon', isset($vendorTab->HargaTanpaDiskon) ? 'Rp ' . number_format($vendorTab->HargaTanpaDiskon, 0, ',', '.') : '') }}"
                                                                            onkeyup="formatRupiahInput{{ $i }}(this);">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Total
                                                                            Diskon</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_diskon]"
                                                                            id="total-diskon-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Semua Diskon" readonly
                                                                            value="{{ old('vendors.' . $i . '.total_diskon', isset($vendorTab->TotalDiskon) ? 'Rp ' . number_format($vendorTab->TotalDiskon, 0, ',', '.') : '') }}"
                                                                            onkeyup="formatRupiahInput{{ $i }}(this);">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Harga
                                                                            Setelah Diskon</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][total_harga_setelah_diskon]"
                                                                            id="total-harga-setelah-diskon-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Total Harga Setelah Diskon"
                                                                            readonly
                                                                            value="{{ old('vendors.' . $i . '.total_harga_setelah_diskon', isset($vendorTab->HargaDenganDiskon) ? 'Rp ' . number_format($vendorTab->HargaDenganDiskon, 0, ',', '.') : '') }}"
                                                                            onkeyup="formatRupiahInput{{ $i }}(this);">
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
                                                                            value="{{ old('vendors.' . $i . '.ppn_persen', $vendorTab->Ppn ?? '') }}"
                                                                            oninput="hitungOtomatisGlobal{{ $i }}()">
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
                                                                            value="{{ old('vendors.' . $i . '.total_ppn', isset($vendorTab->TotalPpn) ? 'Rp ' . number_format($vendorTab->TotalPpn, 0, ',', '.') : '') }}"
                                                                            onkeyup="formatRupiahInput{{ $i }}(this);">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><strong>Grand
                                                                            Total</strong></td>
                                                                    <td colspan="2">
                                                                        <input type="text"
                                                                            name="vendors[{{ $i }}][grand_total]"
                                                                            id="grand-total-{{ $i }}"
                                                                            class="form-control currency-input-{{ $i }}"
                                                                            placeholder="Grand Total" readonly
                                                                            value="{{ old('vendors.' . $i . '.grand_total', isset($vendorTab->TotalHarga) ? 'Rp ' . number_format($vendorTab->TotalHarga, 0, ',', '.') : '') }}"
                                                                            onkeyup="formatRupiahInput{{ $i }}(this);">
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                        <script>
                                                            // Javascript for vendor tab [{{ $i }}]
                                                            function formatRupiah{{ $i }}(angka, prefix = "Rp ") {
                                                                if (angka === null || angka === undefined) return '';
                                                                let number_string = angka.toString().replace(/[^,\d]/g, ''),
                                                                    split = number_string.split(','),
                                                                    sisa = split[0].length % 3,
                                                                    rupiah = split[0].substring(0, sisa),
                                                                    ribuan = split[0].substring(sisa).match(/\d{3}/gi);

                                                                if (ribuan) {
                                                                    let separator = sisa ? '.' : '';
                                                                    rupiah += separator + ribuan.join('.');
                                                                }

                                                                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                                                                return prefix + rupiah;
                                                            }

                                                            function formatRupiahInput{{ $i }}(input) {
                                                                if (input.hasAttribute('readonly')) return;
                                                                let nilai = input.value.replace(/[^,\d]/g, '');
                                                                if (!nilai) {
                                                                    input.value = '';
                                                                    return;
                                                                }
                                                                let formatted = formatRupiah{{ $i }}(nilai, "Rp ");
                                                                input.value = formatted;
                                                            }

                                                            function parseNum{{ $i }}(val, allowComma = false) {
                                                                if (typeof val === 'string') {
                                                                    if (allowComma) {
                                                                        val = val.replace(/[^0-9\.,\-]/g, '');
                                                                        val = val.replace(/\.(?=.*\.)/g, '');
                                                                        val = val.replace(/\,/g, '.');
                                                                    } else {
                                                                        val = val.replace(/[^0-9\,\-]/g, "");
                                                                        val = val.replace(/\./g, "");
                                                                        val = val.replace(',', '.');
                                                                    }
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
                                                                let jenisDiskon = $row.find('.jenis-diskon-item-select-{{ $i }}').val();

                                                                let diskon;
                                                                if (jenisDiskon === 'Persen') {
                                                                    diskon = parseNum{{ $i }}(diskonInput.val(), true);
                                                                } else {
                                                                    diskon = parseNum{{ $i }}(diskonInput.val(), false);
                                                                }

                                                                if (jenisDiskon === 'Rp') {
                                                                    diskonInput.addClass('currency-input-{{ $i }}');
                                                                    formatRupiahInput{{ $i }}(diskonInput[0]);
                                                                    diskonInput.attr('placeholder', 'Diskon (Rp)');
                                                                } else {
                                                                    diskonInput.removeClass('currency-input-{{ $i }}');
                                                                    let currentValue = diskonInput.val();
                                                                    let cleanDecimal = currentValue.replace(/[^0-9\.,\-]/g, '').replace(/\.(?=.*\.)/g, '').replace(/\,/g, '.');
                                                                    diskonInput.val(cleanDecimal);
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
                                                                if (totalDiskon < 0) totalDiskon = 0;

                                                                let totalSetelahDiskon = subTotal - totalDiskon;
                                                                if (totalSetelahDiskon < 0) totalSetelahDiskon = 0;

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

                                                            $(document).ready(function() {
                                                                $('#table-detail-vendor-{{ $i }}').on('input change blur',
                                                                    '.jumlah-vendor-input-{{ $i }}, .harga-satuan-input-{{ $i }}, .diskon-item-input-{{ $i }}, .jenis-diskon-item-select-{{ $i }}',
                                                                    function() {
                                                                        hitungOtomatis{{ $i }}(this);
                                                                        var $row = $(this).closest('tr');
                                                                        var jenisDiskon = $row.find('.jenis-diskon-item-select-{{ $i }}').val();
                                                                        if ($(this).hasClass('currency-input-{{ $i }}') &&
                                                                            $(this).is('.diskon-item-input-{{ $i }}')
                                                                        ) {
                                                                            if (jenisDiskon === 'Rp') {
                                                                                formatRupiahInput{{ $i }}(this);
                                                                            }
                                                                        } else if ($(this).hasClass('currency-input-{{ $i }}')) {
                                                                            formatRupiahInput{{ $i }}(this);
                                                                        } else if ($(this).is('.diskon-item-input-{{ $i }}') && jenisDiskon ===
                                                                            'Persen') {
                                                                            var currentValue = $(this).val();
                                                                            var cleanDecimal = currentValue.replace(/[^0-9\.,\-]/g, '').replace(/\.(?=.*\.)/g, '')
                                                                                .replace(/\,/g, '.');
                                                                            $(this).val(cleanDecimal);
                                                                        }
                                                                    });

                                                                $('#table-detail-vendor-{{ $i }}').on('keyup blur focus',
                                                                    '.currency-input-{{ $i }}',
                                                                    function() {
                                                                        var $row = $(this).closest('tr');
                                                                        var jenisDiskon = $row.find('.jenis-diskon-item-select-{{ $i }}').val();
                                                                        if ($(this).is('.diskon-item-input-{{ $i }}')) {
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

                                                                setTimeout(function() {
                                                                    let $first = $(
                                                                        "#table-detail-body-{{ $i }} input, #table-detail-body-{{ $i }} select"
                                                                    ).first();
                                                                    if ($first.length) hitungOtomatis{{ $i }}($first[0]);
                                                                    $('.currency-input-{{ $i }}').each(function() {
                                                                        var $input = $(this);
                                                                        var $row = $input.closest('tr');
                                                                        var jenisDiskon = $row.find(
                                                                            '.jenis-diskon-item-select-{{ $i }}').val();
                                                                        if ($input.is('.diskon-item-input-{{ $i }}')) {
                                                                            if (jenisDiskon === 'Rp') {
                                                                                formatRupiahInput{{ $i }}(this);
                                                                            }
                                                                        } else {
                                                                            formatRupiahInput{{ $i }}(this);
                                                                        }
                                                                    });

                                                                    // Sync value awal Nama Vendor pada tabel dengan dropdown utama
                                                                    var mainVendorSelect = $('#vendor_list_{{ $i }}_id');
                                                                    var vendorValue = mainVendorSelect.val();
                                                                    $('#table-detail-body-{{ $i }} select.vendor-name-in-table').each(function() {
                                                                        $(this).val(vendorValue).trigger('change');
                                                                    });
                                                                }, 500);
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
                            <a href="{{ route('ajukan.show', encrypt($data->id)) }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-primary me-2" id="btn-simpan-perubahan"
                                name="submit_type" value="simpan">
                                <i class="fa fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    @if (Session::get('success'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(Session::get('success')),
                    iconColor: '#4BCC1F',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#4BCC1F'
                });
            }, 500);
        </script>
    @endif
    @if (Session::get('error'))
        <script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: @json(Session::get('error')),
                    iconColor: '#FFC107',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#FFC107'
                });
            }, 500);
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btnSimpan = document.getElementById('btn-simpan-perubahan');
            if (btnSimpan) {
                btnSimpan.addEventListener('click', function(e) {
                    Swal.fire({
                        title: 'Simpan Perubahan?',
                        text: 'Anda yakin ingin menyimpan perubahan pada pengajuan ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var form = this.closest('form');
                            if (form) form.submit();
                        }
                    });
                });
            }
        });
    </script>
    <script>
        function formatRupiahInput(input) {
            let angka = input.value.replace(/[^,\d]/g, '').toString();
            let split = angka.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substring(0, sisa);
            let ribuan = split[0].substring(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah;
        }
    </script>
    <script>
        // Fungsi update info vendor & sinkronisasi dropdown vendor di tabel, untuk 3 tab vendor
        function updateVendorInfoAndLock(vn) {
            var select = document.getElementById('vendor_list_' + vn + '_id');
            if (!select) return;
            var selectedOption = select.options[select.selectedIndex];
            var namaPIC = selectedOption ? (selectedOption.getAttribute('data-namapic') || '-') : '-';
            var noHpPIC = selectedOption ? (selectedOption.getAttribute('data-nohppic') || '-') : '-';

            var infoDiv = document.getElementById('vendor_info_' + vn);
            if (infoDiv) {
                var namaPICInput = infoDiv.querySelector('.vendor_namapic');
                var noHpPICInput = infoDiv.querySelector('.vendor_nohppic');
                if (namaPICInput && !namaPICInput.readOnly && !namaPICInput.disabled) {
                    namaPICInput.value = namaPIC;
                }
                if (noHpPICInput && !noHpPICInput.readOnly && !noHpPICInput.disabled) {
                    noHpPICInput.value = noHpPIC;
                }
            }
            var tableSelector = '#table-detail-body-' + vn + ' select.vendor-name-in-table';
            $(tableSelector).each(function() {
                $(this).val(select.value).trigger('change');
            });
        }

        $(document).ready(function() {
            for (let i = 0; i < 3; i++) {
                updateVendorInfoAndLock(i);
            }
        });
    </script>
@endpush
