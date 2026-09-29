@extends('layouts.app')

@section('content')
    <div class="page-header ph-redesign">
        <div class="ph-container">
            <!-- KIRI: Breadcrumb, Title, Subtitle -->
            <div class="ph-left">
                <ul class="ph-breadcrumb">
                    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Pengaturan TTD</li>
                </ul>
                <h3 class="ph-title">
                    <i class="fa fa-signature"></i>
                    Pengaturan TTD
                </h3>
                <p class="ph-subtitle">
                    Kelola dan atur seluruh data pengaturan TTD yang ada di sistem ini.
                </p>
            </div>
            <!-- KANAN: Icon -->
            <div class="ph-right">
                <div class="ph-icon">
                    <i class="fa fa-signature"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <h4 class="card-title mb-0">List Pengaturan TTD</h4>
                        <p class="card-text mb-0">
                            Tabel ini berisi semua data pengaturan TTD yang ada.
                        </p>
                    </div>
                    <div>
                        <a class="btn btn-primary" href="{{ route('pengaturan-ttd.create') }}">Tambah Pengaturan TTD
                            Baru</a>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew cell-border compact stripe" id="perusahaanTable" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Nama Lengkap</th>
                                    <th width="15%">Aksi</th>
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

            function loadDataTable() {
                $('#perusahaanTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('master-approval.index') }}",
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
                            data: 'Kode',
                            name: 'Kode'
                        },
                        {
                            data: 'Nama',
                            name: 'Nama'
                        },
                        {
                            data: 'NamaLengkap',
                            name: 'NamaLengkap'
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

            loadDataTable();
        });
    </script>
@endpush
