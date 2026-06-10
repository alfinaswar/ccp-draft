@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Permintaan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pp.index') }}">Permintaan Pembelian</a></li>
                    <li class="breadcrumb-item active">Edit Permintaan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form id="formPenilai" method="POST" action="{{ route('pp.update', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="card mb-4">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0">Edit Formulir Permintaan Pembelian</h4>
                        <p class="card-text mb-0">
                            Silakan ubah data permintaan pembelian di bawah ini.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="departemen" class="form-label"><strong>Departemen / Divisi</strong></label>
                                <select name="Departemen" id="departemen"
                                    class="form-control select2 @error('Departemen') is-invalid @enderror">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departemen ?? [] as $d)
                                        <option value="{{ $d->id }}"
                                            {{ old('Departemen', $data->Departemen) == $d->id ? 'selected' : '' }}>
                                            {{ $d->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Departemen')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label"><strong>Tanggal</strong></label>
                                <input type="date" name="Tanggal" id="tanggal"
                                    class="form-control @error('Tanggal') is-invalid @enderror"
                                    value="{{ old('Tanggal', $data->Tanggal ? (is_object($data->Tanggal) ? $data->Tanggal->format('Y-m-d') : $data->Tanggal) : date('Y-m-d')) }}">
                                @error('Tanggal')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="jenis" class="form-label"><strong>Jenis Permintaan</strong></label>
                                <select name="Jenis" id="jenis"
                                    class="form-control @error('Jenis') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis Permintaan</option>
                                    @foreach ($jenisPengajuan as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            {{ old('Jenis', $data->Jenis) == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Jenis')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tujuan" class="form-label"><strong>Tujuan</strong></label>
                                <select name="Tujuan" id="tujuan"
                                    class="form-control @error('Tujuan') is-invalid @enderror" required>
                                    <option value="">Pilih Tujuan</option>
                                    <option value="Pembelian Baru"
                                        {{ old('Tujuan', $data->Tujuan) == 'Pembelian Baru' ? 'selected' : '' }}>Pembelian
                                        Baru</option>
                                    <option value="Penggantian"
                                        {{ old('Tujuan', $data->Tujuan) == 'Penggantian' ? 'selected' : '' }}>Penggantian
                                    </option>
                                    <option value="Perbaikan"
                                        {{ old('Tujuan', $data->Tujuan) == 'Perbaikan' ? 'selected' : '' }}>Perbaikan
                                    </option>
                                    <option value="Adendum"
                                        {{ old('Tujuan', $data->Tujuan) == 'Adendum' ? 'selected' : '' }}>Adendum
                                    </option>
                                </select>
                                @error('Tujuan')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <h5 class="mb-3"><strong>Detail Permintaan Pembelian</strong></h5>
                        <p class="mb-3">
                            Ubah barang yang diminta.
                        </p>
                        <div class="table-responsive">
                            <table class="table align-middle" id="table-detail-pembelian">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%">Nama Barang</th>
                                        <th style="width: 15%">Jumlah</th>
                                        <th style="width: 15%">Satuan</th>
                                        <th style="width: 25%">Rencana Penempatan</th>
                                        <th style="width: 20%">Keterangan</th>
                                        <th style="width: 8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $oldNamaBarang = old('NamaBarang');
                                        $oldJumlah = old('Jumlah');
                                        $oldSatuan = old('Satuan');
                                        $oldRencanaPenempatan = old('RencanaPenempatan');
                                        $oldKeterangan = old('Keterangan');
                                        $useOld = is_array($oldNamaBarang) && count($oldNamaBarang) > 0;
                                        $rowCount = 0;
                                    @endphp

                                    @if ($useOld)
                                        @foreach ($oldNamaBarang as $i => $nb)
                                            <tr>
                                                <td>
                                                    <select name="NamaBarang[]" class="form-control select2" required>
                                                        <option value="">Pilih Barang</option>
                                                        @foreach ($barang ?? [] as $b)
                                                            <option value="{{ $b->id }}"
                                                                data-jenis-id="{{ $b->Jenis }}"
                                                                {{ $nb == $b->id ? 'selected' : '' }}>
                                                                {{ $b->Nama }} - {{ $b->getMerk->Nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="Jumlah[]" class="form-control jumlah-input"
                                                        min="1"
                                                        value="{{ isset($oldJumlah[$i]) ? $oldJumlah[$i] : 1 }}" required>
                                                </td>
                                                <td>
                                                    <select name="Satuan[]" class="select2" required>
                                                        <option value="">Pilih Satuan</option>
                                                        @foreach ($satuan ?? [] as $s)
                                                            <option value="{{ $s->id }}"
                                                                {{ isset($oldSatuan[$i]) && $oldSatuan[$i] == $s->id ? 'selected' : '' }}>
                                                                {{ $s->NamaSatuan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="RencanaPenempatan[]" class="form-control"
                                                        placeholder="Misal: Gudang A"
                                                        value="{{ isset($oldRencanaPenempatan[$i]) ? $oldRencanaPenempatan[$i] : '' }}"
                                                        required>
                                                </td>
                                                <td>
                                                    <input type="text" name="Keterangan[]" class="form-control"
                                                        placeholder="Keterangan tambahan"
                                                        value="{{ isset($oldKeterangan[$i]) ? $oldKeterangan[$i] : '' }}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                        {{ $i == 0 ? 'disabled' : '' }}>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php $rowCount++; @endphp
                                        @endforeach
                                    @else
                                        @foreach ($data->getDetail as $key => $list)
                                            {{-- {{ dd($list) }} --}}
                                            <tr>
                                                <td>
                                                    <select name="NamaBarang[]" class="form-control select2" required>
                                                        <option value="">Pilih Barang</option>
                                                        @foreach ($barang ?? [] as $b)
                                                            <option value="{{ $b->id }}"
                                                                data-jenis-id="{{ $b->Jenis }}"
                                                                {{ $list->NamaBarang == $b->id ? 'selected' : '' }}>
                                                                {{ $b->Nama }} - {{ $b->getMerk->Nama ?? null }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="Jumlah[]"
                                                        class="form-control jumlah-input" min="1"
                                                        value="{{ $list->Jumlah }}" required>
                                                </td>
                                                <td>
                                                    <select name="Satuan[]" class="select2" required>
                                                        <option value="">Pilih Satuan</option>
                                                        @foreach ($satuan ?? [] as $s)
                                                            <option value="{{ $s->id }}"
                                                                {{ $list->Satuan == $s->id ? 'selected' : '' }}>
                                                                {{ $s->NamaSatuan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="RencanaPenempatan[]" class="form-control"
                                                        placeholder="Misal: Gudang A"
                                                        value="{{ $list->RencanaPenempatan }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="Keterangan[]" class="form-control"
                                                        placeholder="Keterangan tambahan"
                                                        value="{{ $list->Keterangan }}">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                        {{ $key == 0 ? 'disabled' : '' }}>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php $rowCount++; @endphp
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        {{-- <td colspan="6">
                                            <button type="button" class="btn btn-success btn-sm" id="btn-tambah-baris">
                                                <i class="fa fa-plus"></i> Tambah Baris
                                            </button>
                                        </td> --}}
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">
                                            <strong style="font-size: 1rem;">
                                                Total Jumlah: <span id="total-jumlah-view" style="font-size: 1rem;">
                                                </span>
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <a href="{{ route('pp.index') }}" class="btn btn-secondary me-2">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            {{-- <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan Perubahan
                            </button> --}}
                            <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal"
                                data-bs-target="#modalPenilai">
                                <i class="fa fa-eye"></i> Kirim dan Ajukan
                            </button>

                        </div>
                    </div>
                </div>
                @include('form.permintaan-pembelian.modal-kirim-email')
            </form>
            <!-- Modal: Pengaturan Tanda Tangan -->
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Status Approval</h5>
                    {{-- @if (!empty($approval) && count($approval) > 0)
                        <a href="{{ route('pp.kirim-ulang-notifikasi', $data->id) }}" class="btn btn-primary"
                            id="btnKonfirmasiKirimUlang">
                            <i class="fa fa-paper-plane me-1"></i> Kirim Ulang Notifikasi
                        </a>
                    @endif --}}
                </div>
                <div class="card-body">
                    {{-- <span>
                        Gunakan fitur <strong>Kirim Ulang Notifikasi</strong> untuk mengirim ulang email
                        approval ke penilai yang statusnya masih <strong>Pending</strong>, tanpa membatalkan
                        status approval yang sudah ada sebelumnya.
                    </span> --}}
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Status Email</th>
                                    <th>TanggalApprove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($approval && count($approval) > 0)
                                    @foreach ($approval as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->Nama }}</td>
                                            <td>{{ $item->Email }}</td>
                                            <td>{{ $item->Urutan }}</td>
                                            <td>
                                                <span
                                                    class="badge
                                            @if ($item->Status == 'Approved') bg-success
                                            @elseif($item->Status == 'Pending') bg-warning text-dark
                                            @elseif($item->Status == 'Rejected') bg-danger
                                            @else bg-secondary @endif">
                                                    {{ $item->Status }}
                                                </span>
                                            </td>
                                            <td>{{ $item->StatusEmail }}</td>
                                            <td>
                                                {{ $item->TanggalApprove ? \Carbon\Carbon::parse($item->TanggalApprove)->format('d-m-Y H:i') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="16" class="text-center">Belum ada data approval.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
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
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnKonfirmasiKirimUlang');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Kirim Ulang Notifikasi?',
                        text: 'Anda yakin ingin mengirim ulang notifikasi approval yang masih pending?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Mengirim Notifikasi...',
                                html: `<b>Mohon tunggu, proses pengiriman email sedang berlangsung.</b>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal
                                        .showLoading();
                                    setTimeout(() => {
                                        window.location.href = btn.getAttribute(
                                            'href');
                                    }, 500);
                                }
                            });
                        }
                    });
                });
            }
        });
    </script>
    <script>
        function updateTotalJumlah() {
            let total = 0;
            $('#table-detail-pembelian tbody .jumlah-input').each(function() {
                let val = parseInt($(this).val());
                if (!isNaN(val)) total += val;
            });
            $('#total-jumlah-view').text(total);
        }

        // Barang akan dikelompokkan berdasarkan Jenis (val Jenis select)
        function getBarangOptions(JenisFilter) {
            let barang = @json($barang ?? []);
            let html = `<option value="">Pilih Barang</option>`;
            barang.forEach(function(b) {
                if (!JenisFilter || String(b.Jenis) === String(JenisFilter)) {
                    let merkNama = (b.get_merk && b.get_merk.Nama) ? b.get_merk.Nama : '';
                    html +=
                        `<option value="${b.id}" data-jenis-id="${b.Jenis}">${b.Nama} - ${merkNama}</option>`;
                }
            });
            return html;
        }

        $(document).ready(function() {
            function initSelect2(row) {
                if ($.fn.select2) {
                    $(row).find('select.select2').select2({
                        width: "100%"
                    });
                }
            }
            // Ambil jenis ID (val) dari opsi Jenis
            let jenisPengajuanArr = @json($jenisPengajuan ?? []);
            let currentFilterJenis = $('#jenis').val() || null;

            function filterBarangSelects(Jenis) {
                $('#table-detail-pembelian tbody tr').each(function() {
                    let $select = $(this).find('select[name="NamaBarang[]"]');
                    let currentVal = $select.val();
                    let html = getBarangOptions(Jenis);
                    $select.html(html);
                    if (currentVal && $select.find(`option[value="${currentVal}"]`).length) {
                        $select.val(currentVal).trigger("change");
                    } else {
                        $select.val('').trigger("change");
                    }
                });
            }

            function generateRowTemplate(Jenis = null) {
                let options = getBarangOptions(Jenis);
                let satuanOptions = `@foreach ($satuan ?? [] as $s)
                    <option value="{{ $s->id }}">{{ $s->NamaSatuan }}</option>
                @endforeach`;
                return `
                <tr>
                    <td>
                        <select name="NamaBarang[]" class="form-control select2" required>
                            ${options}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="Jumlah[]" class="form-control jumlah-input" min="1" value="1" required>
                    </td>
                    <td>
                        <select name="Satuan[]" class="select2" required>
                            <option value="">Pilih Satuan</option>
                            ${satuanOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="RencanaPenempatan[]" class="form-control" placeholder="Misal: Gudang A" required>
                    </td>
                    <td>
                        <input type="text" name="Keterangan[]" class="form-control" placeholder="Keterangan tambahan">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            }

            // Ketika jenis berubah, ambil value id, filter barang dengan Jenis barang sama dengan value Jenis
            $('#jenis').on('change', function() {
                let selectedJenis = $(this).val();
                currentFilterJenis = selectedJenis;
                filterBarangSelects(currentFilterJenis);
            });

            // Inisialisasi jika sudah terisi
            if (currentFilterJenis) {
                filterBarangSelects(currentFilterJenis);
            }

            $('#btn-tambah-baris').on('click', function() {
                let $tbody = $('#table-detail-pembelian tbody');
                let $row = $(generateRowTemplate(currentFilterJenis));
                $tbody.append($row);
                initSelect2($row);
                updateRemoveButtons();
                updateTotalJumlah();
            });

            $('#table-detail-pembelian').on('click', '.btn-remove-row', function() {
                let rowCount = $('#table-detail-pembelian tbody tr').length;
                if (rowCount > 1) {
                    $(this).closest('tr').remove();
                    updateRemoveButtons();
                    updateTotalJumlah();
                }
            });

            function updateRemoveButtons() {
                let $rows = $('#table-detail-pembelian tbody tr');
                if ($rows.length === 1) {
                    $rows.find('.btn-remove-row').attr('disabled', true);
                } else {
                    $rows.find('.btn-remove-row').removeAttr('disabled');
                }
            }

            updateRemoveButtons();
            initSelect2($('#table-detail-pembelian tbody tr'));
            filterBarangSelects(currentFilterJenis);

            $('#table-detail-pembelian').on('input change', '.jumlah-input', function() {
                updateTotalJumlah();
            });
            updateTotalJumlah();
        });
    </script>
@endpush
