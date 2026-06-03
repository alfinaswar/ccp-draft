@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Laporan Rekomendasi Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan Rekomendasi Pembelian</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title mb-0">Filter Laporan</h4>
                    <p class="card-text mb-0">
                        Silakan pilih tanggal untuk melihat laporan rekomendasi pembelian.
                    </p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="tanggal_awal" class="form-label"><strong>Tanggal Awal</strong></label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal"
                                class="form-control @error('tanggal_awal') is-invalid @enderror"
                                value="{{ request('tanggal_awal') }}">
                            @error('tanggal_awal')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_akhir" class="form-label"><strong>Tanggal Akhir</strong></label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                                class="form-control @error('tanggal_akhir') is-invalid @enderror"
                                value="{{ request('tanggal_akhir', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            @error('tanggal_akhir')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="perusahaan" class="form-label"><strong>Perusahaan</strong></label>
                            <select class="select2 form-select @error('perusahaan') is-invalid @enderror" name="perusahaan"
                                id="perusahaan">
                                <option value="">Semua Perusahaan</option>
                                @foreach ($perusahaan as $item)
                                    <option value="{{ $item->Kode }}"
                                        {{ request('perusahaan') == $item->Kode ? 'selected' : '' }}>
                                        {{ $item->Nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('perusahaan')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="namaBarang" class="form-label">
                                <strong>Nama Barang/Jasa</strong>
                                <span class="text-danger" style="font-size: 0.95em; display:block;">
                                    * Anda bisa memilih lebih dari satu barang/jasa sekaligus
                                </span>
                            </label>
                            <select name="namaBarang" id="namaBarang"
                                class="form-select select2 @error('namaBarang') is-invalid @enderror" multiple>
                                @foreach ($namaBarang as $item)
                                    <option value="{{ $item->id }}"
                                        {{ collect(request('namaBarang'))->contains($item->Nama) ? 'selected' : '' }}>
                                        {{ $item->Nama }}
                                        @if ($item->getMerk)
                                            - {{ $item->getMerk->Nama }}
                                        @endif
                                        @if ($item->Tipe)
                                            - {{ $item->Tipe }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('namaBarang')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 d-flex align-items-end justify-content-end mt-2 gap-2">
                            <button type="button" id="btnPreview" class="btn btn-primary">
                                <i class="fa fa-filter"></i> Preview Data
                            </button>
                            <button type="button" id="btnExport" class="btn btn-success" style="display:none;">
                                <i class="fa fa-file-excel"></i> Export ke Excel
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table cell-border compact stripe" id="rekomendasiTable" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Asal Pengajuan</th>
                                    <th style="width: 140px;">Tanggal Pengajuan</th>
                                    <th>Nama Barang/Jasa</th>
                                    <th>Merek</th>
                                    <th>Tipe</th>
                                    <th>Vendor</th>
                                    <th>Nama PIC</th>
                                    <th>Kontak</th>
                                    <th>Harga Awal</th>
                                    <th>Harga Nego</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="10" style="text-align:right">Total Harga Nego:</th>
                                    <th></th>
                                    <th id="totalHargaNego" style="text-align:right"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table;
        if ($.fn.DataTable.isDataTable('#rekomendasiTable')) {
            table = $('#rekomendasiTable').DataTable();
        } else {
            table = $('#rekomendasiTable').DataTable({
                processing: true,
                serverSide: false,
                searching: false,
                data: [],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode',
                        name: 'kode',
                        defaultContent: '-'
                    },
                    {
                        data: 'asal_pengajuan',
                        name: 'asal_pengajuan',
                        defaultContent: '-'
                    },
                    {
                        data: 'tanggal_pengajuan',
                        name: 'tanggal_pengajuan',
                        defaultContent: '-'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang',
                        defaultContent: '-'
                    },
                    {
                        data: 'merek',
                        name: 'merek',
                        defaultContent: '-'
                    },
                    {
                        data: 'tipe',
                        name: 'tipe',
                        defaultContent: '-'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor',
                        defaultContent: '-'
                    },
                    {
                        data: 'nama_pic',
                        name: 'nama_pic',
                        defaultContent: '-'
                    },
                    {
                        data: 'kontak_pic',
                        name: 'kontak_pic',
                        defaultContent: '-'
                    },
                    {
                        data: 'harga_awal',
                        name: 'harga_awal',
                        render: $.fn.dataTable.render.number('.', ',', 0, 'Rp '),
                        defaultContent: '-'
                    },
                    {
                        data: 'harga_nego',
                        name: 'harga_nego',
                        render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ')
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin"></i>',
                    zeroRecords: '<i class="fa fa-info-circle"></i> Tidak ada data.',
                    emptyTable: '<i class="fa fa-eye"></i> Klik <strong>Preview</strong> untuk lihat data',
                    info: '<i class="fa fa-list-ul"></i> _START_ - _END_ dari _TOTAL_',
                    infoEmpty: '<i class="fa fa-list-ul"></i> Tidak ada data',
                    search: '<i class="fa fa-search"></i> Cari:',
                    lengthMenu: '<i class="fa fa-list"></i> _MENU_',
                    paginate: {
                        first: '<i class="fa fa-angle-double-left"></i>',
                        last: '<i class="fa fa-angle-double-right"></i>',
                        next: '<i class="fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i>',
                    },
                },
                // Update footerCallback: harga_nego column index is now 11, Harga Awal is index 10
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    let total = 0;
                    // Harga Nego (column index 11)
                    var colIdx = 11;
                    total = api
                        .column(colIdx, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            if (typeof a === 'string') {
                                a = a.replace(/[^\d.-]/g, '');
                            }
                            if (typeof b === 'string') {
                                b = b.replace(/[^\d.-]/g, '');
                            }
                            return (parseFloat(a) || 0) + (parseFloat(b) || 0);
                        }, 0);

                    // Format total ke rupiah
                    let totalFormat = total.toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    });
                    // Taruh pada footer kolom harga_nego (kolom index 12/colIndex 11, lihat id th)
                    $(api.column(colIdx).footer()).html(totalFormat);
                },
            });
        }

        // ── Preview Data ────────────────────────────────────────────────────────
        $('#btnPreview').on('click', function() {
            const tanggal_awal = $('#tanggal_awal').val();
            const tanggal_akhir = $('#tanggal_akhir').val();
            const perusahaan = $('#perusahaan').val();
            const namaBarang = $('#namaBarang').val();

            // Validasi tanggal sederhana di sisi client
            if (tanggal_awal && tanggal_akhir && tanggal_awal > tanggal_akhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Tanggal',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memuat...');

            $.ajax({
                url: "{{ route('rekomendasi.laporan.preview') }}",
                method: 'GET',
                data: {
                    tanggal_awal,
                    tanggal_akhir,
                    perusahaan,
                    namaBarang
                },
                success: function(response) {
                    // Map harga_rekomendasi ke harga_nego jika perlu, dan harga_awal jika tersedia
                    let rows = (response.data || []).map(function(row) {
                        row.harga_nego = row.harga_nego !== undefined ? row.harga_nego : row
                            .harga_rekomendasi;
                        // harga_awal fallback ke property lain jika diperlukan (opsional, jika ada di backend)
                        row.harga_awal = row.harga_awal !== undefined ? row.harga_awal : (row
                            .harga_awal_rekomendasi !== undefined ? row
                            .harga_awal_rekomendasi : 0);
                        return row;
                    });
                    table.clear().rows.add(rows).draw();
                    $('#btnExport').toggle(rows && rows.length > 0);
                    if (response.error) {
                        // Jika pesan error dikirim oleh backend (misal: data tidak ditemukan)
                        Swal.fire({
                            icon: 'info',
                            title: 'Informasi',
                            text: response.error,
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengambil data. Silakan coba lagi.',
                        confirmButtonColor: '#d33'
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-filter"></i> Preview Data');
                },
            });
        });

        // ── Export ke Excel ─────────────────────────────────────────────────────
        $('#btnExport').on('click', function() {
            const tanggal_awal = $('#tanggal_awal').val();
            const tanggal_akhir = $('#tanggal_akhir').val();
            const perusahaan = $('#perusahaan').val();
            const namaBarang = $('#namaBarang').val();

            const params = new URLSearchParams({
                tanggal_awal,
                tanggal_akhir,
                perusahaan
            });
            (namaBarang || []).forEach(n => params.append('namaBarang[]', n));

            window.location.href = "{{ route('rekomendasi.laporan.export') }}?" + params.toString();
        });
    </script>
@endpush
