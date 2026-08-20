@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Form Rekomendasi Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Rekomendasi Pembelian</a></li>
                    <li class="breadcrumb-item active">Form Rekomendasi</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-12 col-xl-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title m-0">
                        Rekomendasi Pembelian untuk Pengajuan: <strong>{{ $data->KodePengajuan }} /
                            {{ $data->getPerusahaan->NamaLengkap }}</strong>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-info" style="font-size:1rem;">
                            Status: {{ $data->Status ?? '-' }}
                        </span>
                    </div>
                </div>
                <form id="formRekomendasi" action="{{ route('rekomendasi.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="mb-3">Informasi Barang</h5>
                            <table class="table align-middle" style="width:100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:180px;">Nama Barang</th>
                                        <td>{{ $data->getPengajuanItem[0]->getBarang->Nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Merek</th>
                                        <td>{{ $data->getPengajuanItem[0]->getBarang->getMerk->Nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipe</th>
                                        <td>{{ $data->getPengajuanItem[0]->getBarang->Tipe ?? '-' }}</td>
                                    </tr>

                                    </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center" role="alert" style="min-height: 70px;">
                            <i class="fa fa-exclamation-circle me-2" style="align-self: center; font-size: 1.6rem;"></i>
                            <div class="d-flex align-items-center" style="min-height: 50px;">
                                <ol class="mb-0 ps-2">
                                    <li>Isi rekomendasi pembelian untuk setiap Vendor dengan data yang lengkap. Setelah
                                        rekomendasi untuk Vendor ini diisi, Anda dapat melanjutkan ke Vendor berikutnya.
                                    </li>
                                    <li>Semua kolom rekomendasi wajib diisi.</li>
                                    <li>Jika {{ auth()->user()->name }} sedang sibuk, Anda dapat menyimpan data sebagai
                                        draft
                                        dan melanjutkan pengisian di lain waktu.</li>
                                </ol>
                            </div>
                        </div>

                        <ul class="nav nav-tabs tab-style-1 d-sm-flex d-block" role="tablist" id="vendorTabs">
                            @foreach ($data->getVendor as $vIdx => $Vendor)
                                @php

                                    $enabled = $vIdx === 0;
                                    if ($vIdx > 0) {
                                        $enabled = true;
                                        for ($i = 0; $i < $vIdx; $i++) {
                                            $prevRek = $data->getVendor[$i]->getRekomendasi ?? null;
                                            if (
                                                empty(optional($prevRek)->Spesifikasi) ||
                                                empty(optional($prevRek)->Spesifikasi)
                                            ) {
                                                $enabled = false;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $vIdx === 0 ? 'active' : '' }} {{ $enabled ? '' : 'disabled' }}"
                                        id="vendor-tab-{{ $vIdx }}" data-bs-toggle="tab"
                                        href="{{ $enabled ? '#vendor-pane-' . $vIdx : 'javascript:void(0)' }}"
                                        role="tab" aria-controls="vendor-pane-{{ $vIdx }}"
                                        aria-selected="{{ $vIdx === 0 ? 'true' : 'false' }}"
                                        @if (!$enabled) tabindex="-1" aria-disabled="true" @endif>
                                        {{ $Vendor->getNamaVendor->Nama ?? 'Vendor' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="vendorTabPanes">


                            @foreach ($data->getVendor as $vIdx => $Vendor)
                                @php
                                    $paramHarga = 11 - 1;
                                    $paramSpek = 2 - 1;
                                    $paramGaransi = 13 - 1;
                                    $paramBmhp = 12 - 1;
                                @endphp
                                <div class="tab-pane fade {{ $vIdx === 0 ? 'show active' : '' }}"
                                    id="vendor-pane-{{ $vIdx }}" role="tabpanel"
                                    aria-labelledby="vendor-tab-{{ $vIdx }}">

                                    {{-- {{ dd($Vendor->getHtaGpa->Deskripsi) }} --}}
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdPengajuan]"
                                        value="{{ $data->id }}">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][PengajuanItemId]"
                                        value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdRekomendasi]"
                                        value="">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][IdVendor]"
                                        value="{{ $Vendor->NamaVendor }}">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][NamaPermintaan]"
                                        value="{{ $data->getPengajuanItem[0]->getBarang->id ?? '' }}">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][KodePerusahaan]"
                                        value="{{ $data->KodePerusahaan ?? '' }}">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][DisetujuiOleh]"
                                        value="{{ $data->getPengajuanItem[0]->getRekomendasi->DisetujuiOleh ?? '' }}">
                                    <input type="hidden" name="rekomendasi[{{ $vIdx }}][DisetujuiPada]"
                                        value="{{ $data->getPengajuanItem[0]->getRekomendasi->DisetujuiPada ?? '' }}">

                                    @if (!empty($Vendor) && !empty($Vendor->SuratPenawaranVendor))
                                        <div class="mb-4">
                                            <div class="card border-0 shadow-sm bg-light">
                                                <div class="card-body d-flex align-items-center">
                                                    <span class="me-3" style="font-size: 2rem; color: #dc3545;">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-bold mb-1">Surat Penawaran Vendor</div>
                                                        <a href="{{ asset('storage/penawaran_vendor/' . $Vendor->SuratPenawaranVendor) }}"
                                                            target="_blank" class="btn btn-sm btn-primary px-3">
                                                            <i class="fa fa-eye"></i> Lihat Surat Penawaran
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <table class="table align-middle nilai-table" style="width:100%;"
                                        data-vidx="{{ $vIdx }}">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width:5%;">No</th>
                                                <th class="text-center" style="width:25%;">Parameter</th>
                                                <th class="text-center" style="width:70%;">Deskripsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td class="fw-bold">Harga Awal
                                                    <br>
                                                    <small class="text-muted" style="font-weight: normal;">
                                                        Harga sudah termasuk PPN dari pengajuan
                                                    </small>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        name="rekomendasi[{{ $vIdx }}][HargaAwal]"
                                                        class="form-control currency-input-global"
                                                        placeholder="Masukkan Harga Awal"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaAwal) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaAwal : (isset($Vendor->getHtaGpa->Deskripsi[$paramHarga]) ? $Vendor->getHtaGpa->Deskripsi[$paramHarga] : (old("rekomendasi.$vIdx.HargaAwal") ? preg_replace('/[^0-9]/', '', old("rekomendasi.$vIdx.HargaAwal")) : '')) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td class="fw-bold">Harga Nego</td>
                                                <td>
                                                    <input type="text"
                                                        name="rekomendasi[{{ $vIdx }}][HargaNego]"
                                                        class="form-control currency-input-global"
                                                        placeholder="Masukkan Harga Nego"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaNego) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->HargaNego : old("rekomendasi.$vIdx.HargaNego") }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3</td>
                                                <td class="fw-bold">Spesifikasi</td>
                                                <td>
                                                    <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Spesifikasi]" rows="10"
                                                        placeholder="Masukkan Spesifikasi">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Spesifikasi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Spesifikasi : (isset($Vendor->getHtaGpa->Deskripsi[$paramSpek]) ? $Vendor->getHtaGpa->Deskripsi[$paramSpek] : old("rekomendasi.$vIdx.Spesifikasi")) }}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4</td>
                                                <td class="fw-bold">Negara Produksi</td>
                                                <td>
                                                    <select name="rekomendasi[{{ $vIdx }}][NegaraProduksi]"
                                                        class="form-control select2" data-placeholder="Pilih Negara">
                                                        <option value="">Pilih Negara</option>
                                                        @foreach ($negara as $n)
                                                            <option value="{{ $n->Kode }}"
                                                                {{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->NegaraProduksi) && $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->NegaraProduksi == $n->Kode ? 'selected' : (old("rekomendasi.$vIdx.NegaraProduksi") == $n->Kode ? 'selected' : '') }}>
                                                                {{ $n->Nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">5</td>
                                                <td class="fw-bold">Garansi</td>
                                                <td>
                                                    <input type="text"
                                                        name="rekomendasi[{{ $vIdx }}][Garansi]"
                                                        class="form-control" placeholder="Garansi"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Garansi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Garansi : (isset($Vendor->getHtaGpa->Deskripsi[$paramGaransi]) ? $Vendor->getHtaGpa->Deskripsi[$paramGaransi] : old("rekomendasi.$vIdx.Garansi")) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">6</td>
                                                <td class="fw-bold">Teknisi</td>
                                                <td>
                                                    <input type="text"
                                                        name="rekomendasi[{{ $vIdx }}][Teknisi]"
                                                        class="form-control" placeholder="Teknisi"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Teknisi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Teknisi : old("rekomendasi.$vIdx.Teknisi") }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">7</td>
                                                <td class="fw-bold">Bmhp</td>
                                                <td>
                                                    <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Bmhp]" rows="4"
                                                        placeholder="Bahan Medis Habis Pakai (Bmhp)">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Bmhp) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Bmhp : (isset($Vendor->getHtaGpa->Deskripsi[$paramBmhp]) ? $Vendor->getHtaGpa->Deskripsi[$paramBmhp] : old("rekomendasi.$vIdx.Bmhp")) }}</textarea>
                                                </td>
                                            </tr>
                                            <!-- POPULASI Setelah Bmhp -->
                                            <tr>
                                                <td class="text-center">8</td>
                                                <td class="fw-bold">Populasi</td>
                                                <td>
                                                    <textarea class="ckeditor" id="ckeditor" name="rekomendasi[{{ $vIdx }}][Populasi]" placeholder="Populasi">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Populasi : old("rekomendasi.$vIdx.Populasi") }}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">9</td>
                                                <td class="fw-bold">Spare Part</td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="rekomendasi[{{ $vIdx }}][SparePart]"
                                                        placeholder="Spare Part"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->SparePart) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->SparePart : old("rekomendasi.$vIdx.SparePart") }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">10</td>
                                                <td class="fw-bold">Backup Unit</td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="rekomendasi[{{ $vIdx }}][BackupUnit]"
                                                        placeholder="Backup Unit"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->BackupUnit) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->BackupUnit : old("rekomendasi.$vIdx.BackupUnit") }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">11</td>
                                                <td class="fw-bold">TOP (Term of Payment)</td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="rekomendasi[{{ $vIdx }}][Top]"
                                                        placeholder="Contoh: 30 hari"
                                                        value="{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Top) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Top : old("rekomendasi.$vIdx.Top") }}">
                                                </td>
                                            </tr>
                                            {{-- <tr>
                                                <td class="text-center">12</td>
                                                <td class="fw-bold">Rekomendasi</td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="rekomendasi[{{ $vIdx }}][Rekomendasi]"
                                                        placeholder="Isi Rekomendasi"
                                                        value="{{ old("rekomendasi.$vIdx.Rekomendasi") }}">
                                                </td>
                                            </tr> --}}
                                            <tr>
                                                <td class="text-center">12</td>
                                                <td class="fw-bold">Keterangan</td>
                                                <td>
                                                    <textarea class="form-control" name="rekomendasi[{{ $vIdx }}][Keterangan]" rows="3"
                                                        placeholder="Masukkan Keterangan">{{ isset($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Keterangan) ? $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Keterangan : old("rekomendasi.$vIdx.Keterangan") }}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">13</td>
                                                <td class="fw-bold" style="color:red;">Keterangan (Dikeluarkan Oleh GH
                                                    Procurement)</td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="rekomendasi[{{ $vIdx }}][Rekomendasi]"
                                                        value="{{ $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->Rekomendasi ?? (old("rekomendasi.$vIdx.Rekomendasi") ?? '') }}"
                                                        readonly>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach


                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">

                            @if ($data->Status == 'Selesai Review' || $data->Status == 'Menunggu Rekomendasi GH')
                                <a href="{{ route('rekomendasi.batalkan', encrypt($data->id)) }}"
                                    class="btn btn-danger d-flex align-items-center" id="btnBatalkanRekomendasi">
                                    <i class="fa fa-times me-2"></i> Batalkan
                                </a>
                            @else
                                <button type="submit" name="action" value="draft"
                                    class="btn btn-warning d-flex align-items-center">
                                    <i class="fa fa-save me-2"></i> Simpan Rekomendasi
                                </button>
                            @endif
                            @if (
                                !empty($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]) &&
                                    !is_null($data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->IdPengajuan))
                                <a href="{{ route('rekomendasi.store-selesai', $data->getRekomendasi[0]->getRekomedasiDetail[$vIdx]->IdPengajuan) }}"
                                    class="btn btn-success btn-md d-flex align-items-center">
                                    <i class="fa fa-check-circle me-2"></i> Review Selesai
                                </a>
                            @endif
                            <a href="{{ route('rekomendasi.show', encrypt($data->id)) }}"
                                class="btn btn-secondary d-flex align-items-center">
                                <i class="fa fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                </form>
            </div>
        </div>

        <form id="formAjukanHtaGpa" action="{{ route('htagpa.ajukan') }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="IdPengajuan" value={{ $data->id }}"">
            <input type="hidden" name="PengajuanItemId" value="{{ $data->getPengajuanItem[0]->id ?? '' }}">
            <input type="hidden" name="IdBarang" value="{{ $data->getPengajuanItem[0]->IdBarang ?? '' }}">
            <input type="hidden" name="Status" value="Diajukan">
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
                    text: '{{ Session::get('success') }}',
                    iconColor: '#4BCC1F',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#4BCC1F',
                });
            }, 500);
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnAjukan').addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Ajukan Penilaian?',
                    text: "Apakah Anda yakin ingin mengajukan data penilaian ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ajukan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formAjukanHtaGpa').submit();
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnBatalkan = document.getElementById('btnBatalkanRekomendasi');
            if (btnBatalkan) {
                btnBatalkan.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin mau ganti status ke Dalam Review ?',
                        text: "Anda tidak dapat mengembalikan aksi ini!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, batalkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = btnBatalkan.getAttribute('href');
                        }
                    });
                });
            }
        });
    </script>
    <script>
        function updateSubtotalsAndGrandTotal(vendorTable) {
            let grandTotal = 0;
            vendorTable.find("tbody tr").each(function() {
                let subtotal = 0;
                $(this).find('.nilai-input').each(function() {
                    let nilai = parseFloat($(this).val());
                    if (isNaN(nilai)) nilai = 0;
                    if (nilai > 5) {
                        $(this).val(5);
                        nilai = 5;
                    }
                    if (nilai < 0) {
                        $(this).val(0);
                        nilai = 0;
                    }
                    subtotal += nilai;
                });
                $(this).find('.subtotal-input').val(subtotal);
                grandTotal += subtotal;
            });
            vendorTable.find('.grandtotal-input').val(grandTotal);
        }

        $(document).ready(function() {
            $('.nilai-table').each(function() {
                let vendorTable = $(this);
                updateSubtotalsAndGrandTotal(vendorTable);

                vendorTable.on('input', '.nilai-input', function() {
                    updateSubtotalsAndGrandTotal(vendorTable);
                });
            });
        });
    </script>
    <script>
        // Fungsi format Rupiah
        function formatRupiahInput(input) {
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
            $('.currency-input-global, .harga-nego-input').each(function() {
                formatRupiahInput(this);
            });

            // Event format otomatis saat mengetik atau blur (untuk perubahan manual)
            $(document).on('keyup blur', '.currency-input-global, .harga-nego-input', function() {
                formatRupiahInput(this);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropArea = document.querySelector('[id^="custom-file-drop-area-"]');
            var fileInput = dropArea.querySelector('input[type="file"]');
            var fileNameDisplay = dropArea.querySelector('#file-selected-name');

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false)
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.borderColor = '#2563eb';
                    dropArea.style.backgroundColor = '#e0e7ff';
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.style.borderColor = '#b6c2d1';
                    dropArea.style.backgroundColor = '';
                }, false);
            });

            dropArea.addEventListener('click', function(e) {
                // Only trigger file input if clicking main area, not on file name text
                if (!e.target.closest('#file-selected-name')) {
                    fileInput.click();
                }
            });

            dropArea.addEventListener('drop', function(e) {
                var dt = e.dataTransfer;
                var files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileNameDisplay.textContent = files[0].name;
                    fileNameDisplay.classList.add('text-success');
                }
            });

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = fileInput.files[0].name;
                    fileNameDisplay.classList.add('text-success');
                } else {
                    fileNameDisplay.textContent = '';
                    fileNameDisplay.classList.remove('text-success');
                }
            });
        });
    </script>
@endpush
