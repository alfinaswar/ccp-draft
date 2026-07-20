@extends('layouts.app')

@section('content')
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title mb-1">Laporan Total Pembelian per RS <span class="fw-normal" style="font-size: 70%"></span></h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 py-0 bg-white px-0 ps-1">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Total Pembelian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title mb-1">Rekapitulasi Penghematan per Rumah Sakit</h4>
                    <small class="card-text text-white-50">Perbandingan total Harga Awal vs Harga Negosiasi (Khusus Rekomendasi 1).</small>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form id="filterForm" autocomplete="off">
                        <div class="row g-3 align-items-end mb-4">
                            <div class="col-md-3">
                                <label for="start_month" class="form-label fw-bold text-secondary mb-1">Bulan Awal</label>
                                <input type="month" name="start_month" id="start_month" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label for="end_month" class="form-label fw-bold text-secondary mb-1">Bulan Akhir</label>
                                <input type="month" name="end_month" id="end_month" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6 d-flex gap-2 pt-2 pt-md-0">
                                <button type="button" id="btnFilter" class="btn btn-primary">
                                    <i class="fa fa-filter me-1"></i> Filter
                                </button>
                                <button type="button" id="btnReset" class="btn btn-secondary">
                                    <i class="fa fa-undo me-1"></i> Reset
                                </button>
                                <a href="{{ route('laporan.total-pembelian.export') }}" id="btnExport" class="btn btn-success" target="_blank">
                                    <i class="fa fa-file-excel-o me-1"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </form>
                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="laporanTable" width="100%">
                            <thead class="table-dark align-middle text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode RS</th>
                                    <th>Nama Rumah Sakit</th>
                                    <th>Total Belanja RS / Cisco</th>
                                    <th>Total Belanja Rekomendasi CCP</th>
                                    <th>Total Selisih (Hemat)</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="3" class="text-end fw-bold text-dark align-middle">Akumulasi Total:</th>
                                    <th id="grandTotalHargaAwal" class="fw-bold text-primary align-middle">Rp 0</th>
                                    <th id="grandTotalHargaNego" class="fw-bold text-info align-middle">Rp 0</th>
                                    <th id="grandTotalSelisih" class="fw-bold text-success align-middle">Rp 0</th>
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
        $(document).ready(function() {
            let table;

            function numberFormat(number) {
                return 'Rp ' + parseFloat(number).toLocaleString('id-ID', {minimumFractionDigits: 0});
            }

            function loadDataTable() {
                table = $('#laporanTable').DataTable({
                    responsive: true,
                    serverSide: false,    // DISABLE SERVER SIDE
                    processing: true,
                    bDestroy: true,
                    paging: false,        // DISABLE PAGINATION
                    searching: false,     // Optional: disable search
                    ordering: true,       // Enable/disable as needed
                    info: false,          // Optional: hide table info
                    ajax: {
                        url: "{{ route('laporan.total-pembelian') }}",
                        dataSrc: function (json) {
                            // Calculate totals for HargaAwal, HargaNego, and Selisih
                            let totalHargaAwal = 0;
                            let totalHargaNego = 0;
                            let totalSelisih = 0;

                            json.data.forEach(function(row) {
                                const hargaAwal = parseFloat((row.TotalHargaAwal ?? "0").toString().replace(/\D/g, '')) || 0;
                                const hargaNego = parseFloat((row.TotalHargaNego ?? "0").toString().replace(/\D/g, '')) || 0;
                                totalHargaAwal += hargaAwal;
                                totalHargaNego += hargaNego;
                                totalSelisih += (hargaAwal - hargaNego);
                            });

                            // Formatting
                            $('#grandTotalHargaAwal').html(numberFormat(totalHargaAwal));
                            $('#grandTotalHargaNego').html(numberFormat(totalHargaNego));
                            $('#grandTotalSelisih').html(numberFormat(totalSelisih));

                            // Update Export Excel link dynamically
                            let exportUrl = "{{ route('laporan.total-pembelian.export') }}";
                            let params = [];
                            if ($('#start_month').val()) params.push('start_month=' + $('#start_month').val());
                            if ($('#end_month').val()) params.push('end_month=' + $('#end_month').val());
                            if (params.length > 0) exportUrl += '?' + params.join('&');
                            $('#btnExport').attr('href', exportUrl);

                            // DataTables expects an array for client mode
                            return json.data;
                        },
                        data: function (d) {
                            // Because no paging, only send the filter params
                            d.start_month = $('#start_month').val();
                            d.end_month = $('#end_month').val();
                        }
                    },
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> <span class="sr-only">Memuat...</span>',
                        emptyTable: "Data tidak tersedia"
                    },
                    columns: [
                        {
                            data: null,
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: "text-center",
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        { data: 'Kode', name: 'Kode', className: "text-center" },
                        { data: 'Nama', name: 'Nama' },
                        {
                            data: 'TotalHargaAwal',
                            name: 'TotalHargaAwal',
                            orderable: false,
                            className: "text-end",
                            render: function(data, type, row) {
                                let val = parseFloat((data ?? "0").toString().replace(/\D/g, '')) || 0;
                                return '<span class="fw-bold">' + numberFormat(val) + '</span>';
                            }
                        },
                        {
                            data: 'TotalHargaNego',
                            name: 'TotalHargaNego',
                            orderable: false,
                            className: "text-end",
                            render: function(data, type, row) {
                                let val = parseFloat((data ?? "0").toString().replace(/\D/g, '')) || 0;
                                return '<span class="fw-bold text-info">' + numberFormat(val) + '</span>';
                            }
                        },
                        {
                            // Calculate selisih per row and bold-format
                            data: null,
                            name: 'TotalSelisih',
                            orderable: false,
                            className: "text-end text-success",
                            render: function(data, type, row) {
                                let hargaAwal = parseFloat((row.TotalHargaAwal ?? "0").toString().replace(/\D/g, '')) || 0;
                                let hargaNego = parseFloat((row.TotalHargaNego ?? "0").toString().replace(/\D/g, '')) || 0;
                                let selisih = hargaAwal - hargaNego;
                                return '<span class="fw-bold">' + numberFormat(selisih) + '</span>';
                            }
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center" }
                    ]
                });
            }

            loadDataTable();

            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            $('#btnReset').on('click', function() {
                $('#start_month').val('');
                $('#end_month').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
