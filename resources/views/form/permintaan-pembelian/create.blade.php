@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Permintaan Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pp.index') }}">Permintaan Pembelian</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah Permintaan Pembelian</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('pp.store') }}" method="POST">
                @csrf
                <div class="card mb-4">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-0">Formulir Permintaan Pembelian</h4>
                        <p class="card-text mb-0">
                            Silakan isi data permintaan pembelian di bawah ini.
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
                                            {{ old('Departemen') == $d->id ? 'selected' : '' }}>
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
                                    value="{{ old('Tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
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
                                            {{ old('Jenis') == $jenis->id ? 'selected' : '' }}>{{ $jenis->Nama }}
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
                                        {{ old('Tujuan') == 'Pembelian Baru' ? 'selected' : '' }}>Pembelian Baru</option>
                                    <option value="Penggantian" {{ old('Tujuan') == 'Penggantian' ? 'selected' : '' }}>
                                        Penggantian</option>
                                    <option value="Perbaikan" {{ old('Tujuan') == 'Perbaikan' ? 'selected' : '' }}>
                                        Perbaikan</option>
                                    <option value="Adendum" {{ old('Tujuan') == 'Adendum' ? 'selected' : '' }}>
                                        Adendum</option>

                                </select>
                                @error('Tujuan')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <h5 class="mb-3"><strong>Detail Permintaan Pembelian</strong></h5>
                        <p class="mb-3">
                            Tambahkan barang yang ingin diminta pembeliannya.
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
                                        // Ambil old detail jika ada validasi gagal
                                        $oldBarang = old('NamaBarang', []);
                                        $oldJumlah = old('Jumlah', []);
                                        $oldSatuan = old('Satuan', []);
                                        $oldPenempatan = old('RencanaPenempatan', []);
                                        $oldKeterangan = old('Keterangan', []);
                                        $rows = max(
                                            1,
                                            max(
                                                count($oldBarang),
                                                count($oldJumlah),
                                                count($oldSatuan),
                                                count($oldPenempatan),
                                                count($oldKeterangan),
                                            ),
                                        );
                                    @endphp
                                    @for ($i = 0; $i < $rows; $i++)
                                        <tr>
                                            <td>
                                                <select name="NamaBarang[]" class="form-control select2" required>
                                                    <option value="">Pilih Barang</option>
                                                    @foreach ($barang ?? [] as $b)
                                                        <option value="{{ $b->id }}"
                                                            data-idjenis="{{ $b->Jenis }}"
                                                            {{ isset($oldBarang[$i]) && $oldBarang[$i] == $b->id ? 'selected' : '' }}>
                                                            {{ $b->Nama }} -
                                                            {{ $b->getMerk->Nama ?? null }}{{ isset($b->Tipe) && $b->Tipe ? ' - ' . $b->Tipe : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="Jumlah[]" class="form-control jumlah-input"
                                                    min="1" value="{{ isset($oldJumlah[$i]) ? $oldJumlah[$i] : 1 }}"
                                                    required>
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
                                                    value="{{ isset($oldPenempatan[$i]) ? $oldPenempatan[$i] : '' }}"
                                                    required>
                                            </td>
                                            <td>
                                                <input type="text" name="Keterangan[]" class="form-control"
                                                    placeholder="Keterangan tambahan"
                                                    value="{{ isset($oldKeterangan[$i]) ? $oldKeterangan[$i] : '' }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                    {{ $rows == 1 ? 'disabled' : '' }}>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            {{-- <button type="button" class="btn btn-success btn-sm" id="btn-tambah-baris">
                                                <i class="fa fa-plus"></i> Tambah Baris
                                            </button> --}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end">
                                            <strong style="font-size: 1rem;">
                                                Total Jumlah: <span id="total-jumlah-view" style="font-size: 1rem;">
                                                    {{ array_sum(old('Jumlah', [1])) }}
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan Sebagai Draft
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
        function updateTotalJumlah() {
            let total = 0;
            $('#table-detail-pembelian tbody .jumlah-input').each(function() {
                let val = parseInt($(this).val());
                if (!isNaN(val)) total += val;
            });
            $('#total-jumlah-view').text(total);
        }

        // Ambil barang berdasarkan VALUE ID yang dipilih dari Jenis
        function getBarangOptionsByJenisId(jenisId) {
            let barang = @json($barang ?? []);
            let html = `<option value="">Pilih Barang</option>`;
            barang.forEach(function(b) {
                if (!jenisId || String(b.Jenis) === String(jenisId)) {
                    let merkNama = (b.get_merk && b.get_merk.Nama) ? b.get_merk.Nama : '';
                    let tipe = (typeof b.Tipe !== 'undefined' && b.Tipe != null && b.Tipe !== '') ? ' - ' + b.Tipe :
                        '';
                    html +=
                        `<option value="${b.id}" data-idjenis="${b.Jenis}">${b.Nama} - ${merkNama}${tipe}</option>`;
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

            let jenisPengajuanArr = @json($jenisPengajuan ?? []);

            function filterBarangSelectsByJenisId(jenisId) {
                $('#table-detail-pembelian tbody tr').each(function() {
                    let $select = $(this).find('select[name="NamaBarang[]"]');
                    let currentVal = $select.val();
                    let html = getBarangOptionsByJenisId(jenisId);
                    $select.html(html);

                    if (currentVal && $select.find(`option[value="${currentVal}"]`).length) {
                        $select.val(currentVal).trigger('change');
                    } else {
                        $select.val('').trigger('change');
                    }
                });
            }

            function generateRowTemplateByJenisId(jenisId = null) {
                let options = getBarangOptionsByJenisId(jenisId);
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

            // Inisialisasi filter awal
            let currentJenisId = $('#jenis').val() || null;

            // Filter pada saat Jenis diubah
            $('#jenis').on('change', function() {
                currentJenisId = $(this).val() || null;
                filterBarangSelectsByJenisId(currentJenisId);
            });

            // Saat reload (old value)
            if (currentJenisId) {
                filterBarangSelectsByJenisId(currentJenisId);
            }

            // Tambah baris baru
            $('#btn-tambah-baris').on('click', function() {
                let $tbody = $('#table-detail-pembelian tbody');
                let row = $(generateRowTemplateByJenisId(currentJenisId));
                $tbody.append(row);
                initSelect2(row);
                updateRemoveButtons();
                updateTotalJumlah();
            });

            // Hapus baris
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

            // Jalankan saat load
            updateRemoveButtons();

            // Inisialisasi awal select2 jika ada
            initSelect2($('#table-detail-pembelian tbody tr'));

            // Filter barang sesuai old value jika ada
            filterBarangSelectsByJenisId(currentJenisId);

            // Recalculate total-jumlah jika ada perubahan pada input jumlah
            $('#table-detail-pembelian').on('input change', '.jumlah-input', function() {
                updateTotalJumlah();
            });

            // Hitung initial total jumlah saat reload
            updateTotalJumlah();
        });
    </script>
@endpush
