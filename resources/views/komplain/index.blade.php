@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Komplain Vendor</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-exclamation-triangle"></i>
                    Komplain Vendor
                </h3>
                <p class="ph-subtitle">
                    Daftar formulir evaluasi BMHP umum yang masuk.
                </p>
            </div>
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">List Komplain</h4>
                        <small class="card-text text-muted">
                            Formulir diisi tanpa login melalui <a href="{{ route('komplain.form') }}" target="_blank">form
                                publik</a>.
                        </small>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select id="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($statusList as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="komplainTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nomor</th>
                                    <th>Tanggal</th>
                                    <th>Pelapor</th>
                                    <th>Unit Kerja</th>
                                    <th>Barang</th>
                                    <th>Status</th>
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
            function loadDataTable() {
                $('#komplainTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('komplain.index') }}",
                        data: function(d) {
                            d.status = $('#filterStatus').val();
                        }
                    },
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'KodeKomplain',
                            name: 'KodeKomplain'
                        },
                        {
                            data: 'Tanggal',
                            name: 'Tanggal'
                        },
                        {
                            data: 'NamaPelapor',
                            name: 'NamaPelapor'
                        },
                        {
                            data: 'UnitKerja',
                            name: 'UnitKerja'
                        },
                        {
                            data: 'NamaBarang',
                            name: 'NamaBarang'
                        },
                        {
                            data: 'StatusBadge',
                            name: 'Status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            $('#filterStatus').on('change', function() {
                $('#komplainTable').DataTable().ajax.reload();
            });

            loadDataTable();
        });
    </script>
@endpush
