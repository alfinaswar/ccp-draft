@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Detail Pembelian</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.total-pembelian') }}">Laporan Total Pembelian</a></li>
                    <li class="breadcrumb-item active">{{ $perusahaan->Nama }}</li>
                </ul>
            </div>



        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Breakdown Item: {{ $perusahaan->Nama }}</h4>
                        <p class="card-text text-white-50 mb-0">Kode RS: <strong>{{ $perusahaan->Kode }}</strong> | Khusus Rekomendasi 1</p>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Summary Cards (Opsional tapi sangat disarankan untuk UX) -->
                    <div class="row mb-4 g-3" id="summaryCards">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Total Harga Awal</h6>
                                    <h4 class="fw-bold text-dark mb-0" id="sumAwal">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Total Harga Nego</h6>
                                    <h4 class="fw-bold text-primary mb-0" id="sumNego">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="text-success mb-1">Total Penghematan</h6>
                                    <h4 class="fw-bold text-success mb-0" id="sumHemat">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                                        <!-- Filter Section -->
                    <div class="row mb-3 g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">Bulan Awal</label>
                            <input type="month" name="start_month" id="start_month"
                                   class="form-control form-control-sm"
                                   value="{{ request('start_month') }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-secondary small">Bulan Akhir</label>
                            <input type="month" name="end_month" id="end_month"
                                   class="form-control form-control-sm"
                                   value="{{ request('end_month') }}" readonly>
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            {{-- Buttons disabled in read-only mode --}}
                        </div>
                    </div>


                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="detailTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Permintaan / Item</th>
                                    <th>Harga Awal</th>
                                    <th>Harga Nego</th>
                                    <th>Selisih</th>
                                    {{-- <th>Tanggal</th> --}}
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            let table;
            const kodePerusahaan = "{{ $perusahaan->Kode }}";

            function loadDataTable() {
                table = $('#detailTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('laporan.total-pembelian.detail', ':kode') }}".replace(':kode', kodePerusahaan),
                        data: function (d) {
                            d.start_month = $('#start_month').val();
                            d.end_month = $('#end_month').val();
                        },
                        dataSrc: function (json) {
                            // LANGSUNG AMBIL DARI CONTROLLER (SUDAH DI-SUM OLEH DATABASE)
                            // Ini menjamin angka total selalu benar, meski di halaman 1, 2, dst.
                            $('#sumAwal').text('Rp ' + json.sumAwal);
                            $('#sumNego').text('Rp ' + json.sumNego);
                            $('#sumHemat').text('Rp ' + json.sumSelisih);

                            return json.data;
                        }
                    },
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'NamaPermintaan', name: 'NamaPermintaan' },
                        { data: 'HargaAwal', name: 'HargaAwal' },
                        { data: 'HargaNego', name: 'HargaNego' },
                        { data: 'Selisih', name: 'Selisih', orderable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            }

            loadDataTable();

            $('#btnFilter').on('click', function() { table.ajax.reload(); });
            $('#btnReset').on('click', function() {
                $('#start_month').val('');
                $('#end_month').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
